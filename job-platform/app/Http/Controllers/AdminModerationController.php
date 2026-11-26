<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Job;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminModerationController extends Controller
{
    private const MODERATABLE_STATUSES = [
        'pending',
        'approved',
        'needs_revision',
        'rejected',
        'banned',
    ];

    /**
     * Display the moderation queue ordered FIFO (oldest submitted/created first for pending).
     */
    public function index(Request $request): View
    {
        $this->ensureAdminRole();

        $statusFilter = $request->query('status', 'pending');
        if ($statusFilter !== 'all' && ! in_array($statusFilter, self::MODERATABLE_STATUSES, true)) {
            $statusFilter = 'pending';
        }

        $search = trim((string) $request->query('search', ''));

        $query = Job::with('company')->where('status', '!=', 'draft');

        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhereHas('company', function ($companyQuery) use ($search) {
                        $companyQuery->where('company_name', 'like', "%{$search}%");
                    });
            });
        }

        // FIFO ordering for pending queue: earliest submitted/created job appears first.
        if ($statusFilter === 'pending') {
            $query->orderBy('updated_at', 'asc')
                ->orderBy('created_at', 'asc')
                ->orderBy('id', 'asc');
        } else {
            $query->orderByDesc('updated_at')->orderByDesc('id');
        }

        $jobs = $query->paginate(12)->withQueryString();

        $rawCounts = Job::where('status', '!=', 'draft')
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $statusCounts = [
            'all' => 0,
            'pending' => 0,
            'approved' => 0,
            'needs_revision' => 0,
            'rejected' => 0,
            'banned' => 0,
        ];

        foreach ($rawCounts as $status => $total) {
            if (array_key_exists($status, $statusCounts)) {
                $statusCounts[$status] = (int) $total;
                $statusCounts['all'] += (int) $total;
            }
        }

        return view('admin.moderation.index', [
            'jobs' => $jobs,
            'statusCounts' => $statusCounts,
            'statusFilter' => $statusFilter,
            'search' => $search,
        ]);
    }

    /**
     * Display full job details and audit log history for moderation review.
     */
    public function show(Job $job): View
    {
        $this->ensureAdminRole();

        $job->load('company.user');

        $auditLogs = AuditLog::with('admin')
            ->where('target_type', 'job')
            ->where('target_id', $job->id)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        return view('admin.moderation.show', [
            'job' => $job,
            'auditLogs' => $auditLogs,
        ]);
    }

    /**
     * Approve a job posting and record the action in audit_logs.
     */
    public function approve(Request $request, Job $job): RedirectResponse
    {
        $this->ensureAdminRole();

        if ($job->status === 'banned') {
            return redirect()
                ->route('admin.moderation.show', $job)
                ->with('error', 'Tin tuyển dụng đã bị khóa vĩnh viễn do vi phạm/lừa đảo, không thể duyệt.');
        }

        if ($job->status === 'approved') {
            return redirect()
                ->route('admin.moderation.show', $job)
                ->with('error', 'Tin tuyển dụng này đã được duyệt trước đó.');
        }

        $validated = $request->validateWithBag('approve', [
            'reason' => ['nullable', 'string', 'max:1000'],
        ], [
            'reason.max' => 'Ghi chú không được vượt quá 1000 ký tự.',
        ]);

        $note = trim((string) ($validated['reason'] ?? ''));

        $this->applyModerationAction(
            $job,
            'approved',
            'approve_job',
            $note !== '' ? $note : null
        );

        return redirect()
            ->route('admin.moderation.index')
            ->with('success', "Đã duyệt tin \"{$job->title}\" thành công và ghi nhận lịch sử kiểm duyệt.");
    }

    /**
     * Reject a job posting with a mandatory reason and record in audit_logs.
     */
    public function reject(Request $request, Job $job): RedirectResponse
    {
        $this->ensureAdminRole();

        if ($job->status === 'banned') {
            return redirect()
                ->route('admin.moderation.show', $job)
                ->with('error', 'Tin tuyển dụng đã bị khóa vĩnh viễn.');
        }

        $reason = $this->validateMandatoryReason($request, 'reject', 'từ chối');

        $this->applyModerationAction($job, 'rejected', 'reject_job', $reason);

        return redirect()
            ->route('admin.moderation.index')
            ->with('success', "Đã từ chối tin \"{$job->title}\" và lưu lý do vào nhật ký kiểm duyệt.");
    }

    /**
     * Request revision for a job posting with a mandatory reason and record in audit_logs.
     */
    public function requestRevision(Request $request, Job $job): RedirectResponse
    {
        $this->ensureAdminRole();

        if ($job->status === 'banned') {
            return redirect()
                ->route('admin.moderation.show', $job)
                ->with('error', 'Tin tuyển dụng đã bị khóa vĩnh viễn, không thể yêu cầu chỉnh sửa.');
        }

        $reason = $this->validateMandatoryReason($request, 'revision', 'yêu cầu chỉnh sửa');

        $this->applyModerationAction($job, 'needs_revision', 'request_revision_job', $reason);

        return redirect()
            ->route('admin.moderation.index')
            ->with('success', "Đã gửi yêu cầu chỉnh sửa cho tin \"{$job->title}\" và ghi nhật ký kiểm duyệt.");
    }

    /**
     * Ban a fraudulent/scam job posting with a mandatory reason and record in audit_logs.
     */
    public function ban(Request $request, Job $job): RedirectResponse
    {
        $this->ensureAdminRole();

        if ($job->status === 'banned') {
            return redirect()
                ->route('admin.moderation.show', $job)
                ->with('error', 'Tin tuyển dụng này đã bị khóa trước đó.');
        }

        $reason = $this->validateMandatoryReason($request, 'ban', 'khóa/ban tin lừa đảo');

        $this->applyModerationAction($job, 'banned', 'ban_job', $reason);

        return redirect()
            ->route('admin.moderation.index')
            ->with('success', "Đã khóa (Ban) tin lừa đảo \"{$job->title}\" và ghi nhận vào nhật ký kiểm duyệt.");
    }

    private function ensureAdminRole(): void
    {
        $user = Auth::user();
        if (! $user || $user->role !== 'admin') {
            abort(403, 'Chỉ tài khoản Quản trị viên (Admin) mới có quyền truy cập chức năng kiểm duyệt.');
        }
    }

    private function validateMandatoryReason(Request $request, string $errorBag, string $actionLabel): string
    {
        $validated = $request->validateWithBag($errorBag, [
            'reason' => ['required', 'string', 'min:5', 'max:1000'],
        ], [
            'reason.required' => "Vui lòng nhập lý do {$actionLabel}.",
            'reason.min' => "Lý do {$actionLabel} phải có ít nhất 5 ký tự.",
            'reason.max' => "Lý do {$actionLabel} không được vượt quá 1000 ký tự.",
        ]);

        return trim($validated['reason']);
    }

    private function applyModerationAction(Job $job, string $newStatus, string $auditAction, ?string $reason): void
    {
        DB::transaction(function () use ($job, $newStatus, $auditAction, $reason): void {
            $job->status = $newStatus;
            $job->save();

            AuditLog::create([
                'admin_id' => Auth::id(),
                'target_type' => 'job',
                'target_id' => $job->id,
                'action' => $auditAction,
                'reason' => $reason,
            ]);
        });
    }
}

