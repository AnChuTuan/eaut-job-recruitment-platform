<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyVerification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use RuntimeException;

class CompanyVerificationController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        $company = $request->user()->company;
        if (! $company) {
            return redirect()->route('employer.company.edit')
                ->with('status', 'Vui lòng tạo hồ sơ công ty trước khi xác minh.');
        }

        $verification = $company->verifications()->latest()->first();

        return view('employer.company.verification', compact('company', 'verification'));
    }

    public function submit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supporting_document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ]);

        $company = $request->user()->company;
        abort_unless($company, 404);

        $path = $validated['supporting_document']->store('company-verifications', 'local');
        if (! $path) {
            throw new RuntimeException('Không thể lưu giấy phép công ty.');
        }

        $oldPath = DB::transaction(function () use ($company, $path): ?string {
            $lockedCompany = Company::query()->lockForUpdate()->findOrFail($company->id);
            $verification = $lockedCompany->verifications()->where('status', 'pending')->lockForUpdate()->latest()->first();
            if ($verification) {
                $oldPath = $verification->supporting_documents;
                $verification->update([
                    'supporting_documents' => $path,
                    'rejection_reason' => null,
                ]);
            } else {
                $oldPath = null;
                $lockedCompany->verifications()->create([
                    'supporting_documents' => $path,
                    'status' => 'pending',
                ]);
            }
            $lockedCompany->update(['verification_status' => 'pending']);

            return $oldPath;
        });
        if ($oldPath) {
            Storage::disk('local')->delete($oldPath);
        }

        return back()->with('status', 'Đã gửi hồ sơ xác minh công ty.');
    }

    public function adminIndex(Request $request): View
    {
        $verifications = CompanyVerification::query()
            ->with('company')
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.verifications.index', compact('verifications'));
    }

    public function adminShow(CompanyVerification $verification): View
    {
        $verification->load(['company', 'reviewer']);

        return view('admin.verifications.show', compact('verification'));
    }

    public function downloadDocument(CompanyVerification $verification)
    {
        abort_unless(Storage::disk('local')->exists($verification->supporting_documents), 404);

        return Storage::disk('local')->download($verification->supporting_documents);
    }

    public function approve(Request $request, CompanyVerification $verification): RedirectResponse
    {
        $this->review($request, $verification, 'verified', null);

        return back()->with('status', 'Đã duyệt hồ sơ xác minh công ty.');
    }

    public function reject(Request $request, CompanyVerification $verification): RedirectResponse
    {
        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:2000'],
        ]);
        $this->review($request, $verification, 'rejected', $validated['rejection_reason']);

        return back()->with('status', 'Đã từ chối hồ sơ xác minh công ty.');
    }

    private function review(
        Request $request,
        CompanyVerification $verification,
        string $status,
        ?string $reason
    ): void {
        DB::transaction(function () use ($request, $verification, $status, $reason): void {
            $lockedVerification = CompanyVerification::query()
                ->lockForUpdate()
                ->findOrFail($verification->id);
            abort_unless($lockedVerification->status === 'pending', 409, 'Hồ sơ này đã được xử lý.');

            $lockedVerification->update([
                'status' => $status,
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
                'rejection_reason' => $reason,
            ]);
            Company::query()->whereKey($lockedVerification->company_id)
                ->update(['verification_status' => $status]);
        });
    }
}
