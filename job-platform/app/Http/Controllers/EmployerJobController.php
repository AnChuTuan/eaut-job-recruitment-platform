<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Job;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EmployerJobController extends Controller
{
    private const ALLOWED_STATUSES = [
        'draft',
        'pending',
        'approved',
        'rejected',
        'needs_revision',
        'banned',
    ];

    /**
     * Display a listing of the current employer's job postings.
     */
    public function index(Request $request): View
    {
        $this->ensureEmployerRole();
        $company = $this->getEmployerCompany();

        $statusFilter = $request->query('status');
        $search = trim((string) $request->query('search', ''));

        if (! $company) {
            $emptyPaginator = new LengthAwarePaginator([], 0, 10);

            return view('employer.jobs.index', [
                'company' => null,
                'jobs' => $emptyPaginator,
                'statusCounts' => $this->emptyStatusCounts(),
                'statusFilter' => $statusFilter,
                'search' => $search,
            ]);
        }

        $query = Job::where('company_id', $company->id);

        if ($statusFilter && in_array($statusFilter, self::ALLOWED_STATUSES, true)) {
            $query->where('status', $statusFilter);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $jobs = $query->orderByDesc('updated_at')->paginate(10)->withQueryString();

        $rawCounts = Job::where('company_id', $company->id)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $statusCounts = $this->emptyStatusCounts();
        foreach ($rawCounts as $status => $total) {
            if (array_key_exists($status, $statusCounts)) {
                $statusCounts[$status] = (int) $total;
                $statusCounts['all'] += (int) $total;
            }
        }

        return view('employer.jobs.index', [
            'company' => $company,
            'jobs' => $jobs,
            'statusCounts' => $statusCounts,
            'statusFilter' => $statusFilter,
            'search' => $search,
        ]);
    }

    /**
     * Show the form for creating a new job posting.
     */
    public function create(): View|RedirectResponse
    {
        $this->ensureEmployerRole();
        $company = $this->getEmployerCompany();

        if (! $company) {
            return redirect()
                ->route('employer.jobs.index')
                ->with('error', 'Bạn cần hoàn thiện hồ sơ công ty trước khi đăng tin tuyển dụng.');
        }

        return view('employer.jobs.create', [
            'company' => $company,
            'job' => new Job(['currency' => 'VND', 'quantity' => 1]),
        ]);
    }

    /**
     * Store a newly created job posting in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->ensureEmployerRole();
        $company = $this->getEmployerCompany();

        if (! $company) {
            return redirect()
                ->route('employer.jobs.index')
                ->with('error', 'Bạn cần hoàn thiện hồ sơ công ty trước khi đăng tin tuyển dụng.');
        }

        $actionType = $request->input('action_type', 'submit');
        $isDraftOrPreview = in_array($actionType, ['draft', 'preview'], true);
        $validated = $this->validateJob($request, $isDraftOrPreview);

        $status = $isDraftOrPreview ? 'draft' : 'pending';

        $job = new Job();
        $job->company_id = $company->id;
        $this->fillJobAttributes($job, $validated, $status);
        $job->save();

        if ($actionType === 'preview') {
            return redirect()
                ->route('employer.jobs.preview', $job)
                ->with('success', 'Đã lưu bản nháp. Bạn có thể kiểm tra trước khi gửi duyệt.');
        }

        $message = $status === 'draft'
            ? 'Đã lưu tin tuyển dụng ở trạng thái Nháp.'
            : 'Đã gửi tin tuyển dụng thành công! Tin đang chờ Admin kiểm duyệt.';

        return redirect()->route('employer.jobs.index')->with('success', $message);
    }

    /**
     * Show the form for editing the specified job posting.
     */
    public function edit(Job $job): View|RedirectResponse
    {
        $this->ensureEmployerRole();
        $company = $this->requireEmployerCompany();
        $this->authorizeJobOwner($job, $company);

        if ($job->status === 'banned') {
            return redirect()
                ->route('employer.jobs.index')
                ->with('error', 'Tin tuyển dụng đã bị khóa vĩnh viễn và không thể chỉnh sửa.');
        }

        return view('employer.jobs.edit', [
            'company' => $company,
            'job' => $job,
        ]);
    }

    /**
     * Update the specified job posting in storage.
     */
    public function update(Request $request, Job $job): RedirectResponse
    {
        $this->ensureEmployerRole();
        $company = $this->requireEmployerCompany();
        $this->authorizeJobOwner($job, $company);

        if ($job->status === 'banned') {
            return redirect()
                ->route('employer.jobs.index')
                ->with('error', 'Tin tuyển dụng đã bị khóa vĩnh viễn và không thể cập nhật.');
        }

        $actionType = $request->input('action_type', 'submit');
        $isDraftOrPreview = in_array($actionType, ['draft', 'preview'], true);
        $validated = $this->validateJob($request, $isDraftOrPreview);

        $status = $isDraftOrPreview ? 'draft' : 'pending';

        $this->fillJobAttributes($job, $validated, $status);
        $job->save();

        if ($actionType === 'preview') {
            return redirect()
                ->route('employer.jobs.preview', $job)
                ->with('success', 'Đã lưu bản nháp. Bạn có thể kiểm tra lại trước khi gửi duyệt.');
        }

        $message = $status === 'draft'
            ? 'Đã cập nhật và lưu tin ở trạng thái Nháp.'
            : 'Đã cập nhật và gửi tin tuyển dụng chờ Admin duyệt.';

        return redirect()->route('employer.jobs.index')->with('success', $message);
    }

    /**
     * Preview the specified job posting before submitting for moderation.
     */
    public function preview(Job $job): View
    {
        $this->ensureEmployerRole();
        $company = $this->requireEmployerCompany();
        $this->authorizeJobOwner($job, $company);

        return view('employer.jobs.preview', [
            'company' => $company,
            'job' => $job,
        ]);
    }

    /**
     * Submit a draft / needs_revision / rejected job for admin approval.
     */
    public function submit(Job $job): RedirectResponse
    {
        $this->ensureEmployerRole();
        $company = $this->requireEmployerCompany();
        $this->authorizeJobOwner($job, $company);

        if (! in_array($job->status, ['draft', 'needs_revision', 'rejected'], true)) {
            return redirect()
                ->route('employer.jobs.index')
                ->with('error', 'Trạng thái hiện tại của tin tuyển dụng không thể gửi duyệt lại.');
        }

        if ($job->deadline && $job->deadline < now()->toDateString()) {
            return redirect()
                ->route('employer.jobs.edit', $job)
                ->with('error', 'Hạn nộp hồ sơ đã qua. Vui lòng cập nhật hạn nộp mới trước khi gửi duyệt.');
        }

        $job->status = 'pending';
        $job->save();

        return redirect()
            ->route('employer.jobs.index')
            ->with('success', 'Đã gửi tin tuyển dụng sang trạng thái Chờ duyệt.');
    }

    /**
     * Remove the specified job posting from storage.
     */
    public function destroy(Job $job): RedirectResponse
    {
        $this->ensureEmployerRole();
        $company = $this->requireEmployerCompany();
        $this->authorizeJobOwner($job, $company);

        $job->delete();

        return redirect()
            ->route('employer.jobs.index')
            ->with('success', 'Đã xóa tin tuyển dụng thành công.');
    }

    private function ensureEmployerRole(): void
    {
        $user = Auth::user();
        if (! $user || $user->role !== 'employer') {
            abort(403, 'Chỉ tài khoản Nhà tuyển dụng mới có quyền truy cập chức năng này.');
        }
    }

    private function getEmployerCompany(): ?Company
    {
        return Company::where('user_id', Auth::id())->first();
    }

    private function requireEmployerCompany(): Company
    {
        $company = $this->getEmployerCompany();
        if (! $company) {
            abort(403, 'Không tìm thấy hồ sơ công ty gắn với tài khoản này.');
        }

        return $company;
    }

    private function authorizeJobOwner(Job $job, Company $company): void
    {
        if ((int) $job->company_id !== (int) $company->id) {
            abort(403, 'Bạn không có quyền thao tác trên tin tuyển dụng này.');
        }
    }

    private function emptyStatusCounts(): array
    {
        return [
            'all' => 0,
            'draft' => 0,
            'pending' => 0,
            'approved' => 0,
            'needs_revision' => 0,
            'rejected' => 0,
            'banned' => 0,
        ];
    }

    /**
     * Validate job posting input fields against the jobs table schema.
     *
     * @return array<string, mixed>
     */
    private function validateJob(Request $request, bool $isDraft): array
    {
        $deadlineRule = $isDraft
            ? ['required', 'date']
            : ['required', 'date', 'after_or_equal:today'];

        $descriptionRule = $isDraft
            ? ['required', 'string']
            : ['required', 'string', 'min:20'];

        return $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'category' => ['required', 'string', 'max:100'],
            'job_level' => ['nullable', 'string', 'max:50'],
            'employment_type' => ['nullable', 'string', 'max:50'],
            'quantity' => ['required', 'integer', 'min:1', 'max:1000'],
            'deadline' => $deadlineRule,
            'salary_min' => ['nullable', 'numeric', 'min:0', 'max:9999999999'],
            'salary_max' => [
                'nullable',
                'numeric',
                'min:0',
                'max:9999999999',
                Rule::when($request->filled('salary_min'), ['gte:salary_min']),
            ],
            'currency' => ['required', 'string', 'in:VND,USD'],
            'location' => ['required', 'string', 'max:150'],
            'description' => $descriptionRule,
            'requirements' => ['nullable', 'string'],
            'benefits' => ['nullable', 'string'],
            'skills' => ['nullable', 'string'],
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề tin tuyển dụng.',
            'title.max' => 'Tiêu đề không được vượt quá 200 ký tự.',
            'category.required' => 'Vui lòng chọn hoặc nhập ngành nghề.',
            'category.max' => 'Ngành nghề không được vượt quá 100 ký tự.',
            'quantity.required' => 'Vui lòng nhập số lượng cần tuyển.',
            'quantity.integer' => 'Số lượng cần tuyển phải là số nguyên.',
            'quantity.min' => 'Số lượng cần tuyển tối thiểu là 1.',
            'deadline.required' => 'Vui lòng chọn hạn nộp hồ sơ.',
            'deadline.date' => 'Hạn nộp hồ sơ không đúng định dạng ngày.',
            'deadline.after_or_equal' => 'Hạn nộp hồ sơ khi gửi duyệt phải từ hôm nay trở đi.',
            'salary_min.numeric' => 'Mức lương tối thiểu phải là số.',
            'salary_min.min' => 'Mức lương tối thiểu không được âm.',
            'salary_max.numeric' => 'Mức lương tối đa phải là số.',
            'salary_max.min' => 'Mức lương tối đa không được âm.',
            'salary_max.gte' => 'Mức lương tối đa phải lớn hơn hoặc bằng mức lương tối thiểu.',
            'currency.required' => 'Vui lòng chọn đơn vị tiền tệ.',
            'currency.in' => 'Đơn vị tiền tệ chỉ hỗ trợ VND hoặc USD.',
            'location.required' => 'Vui lòng nhập địa điểm làm việc.',
            'location.max' => 'Địa điểm làm việc không được vượt quá 150 ký tự.',
            'description.required' => 'Vui lòng nhập mô tả công việc.',
            'description.min' => 'Mô tả công việc khi gửi duyệt cần tối thiểu 20 ký tự.',
        ]);
    }

    /**
     * Assign validated attributes to a Job model instance.
     *
     * @param  array<string, mixed>  $validated
     */
    private function fillJobAttributes(Job $job, array $validated, string $status): void
    {
        $job->title = $validated['title'];
        $job->category = $validated['category'];
        $job->job_level = $validated['job_level'] ?? null;
        $job->employment_type = $validated['employment_type'] ?? null;
        $job->quantity = (int) $validated['quantity'];
        $job->deadline = $validated['deadline'];
        $job->salary_min = $validated['salary_min'] ?? null;
        $job->salary_max = $validated['salary_max'] ?? null;
        $job->currency = $validated['currency'];
        $job->location = $validated['location'];
        $job->description = $validated['description'];
        $job->requirements = $validated['requirements'] ?? null;
        $job->benefits = $validated['benefits'] ?? null;
        $job->skills = $validated['skills'] ?? null;
        $job->status = $status;
    }
}

