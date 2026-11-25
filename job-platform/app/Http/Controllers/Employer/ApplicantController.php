<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ApplicantController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $company = $request->user()->company;
        if (! $company) {
            return redirect()->route('employer.company.edit')
                ->with('status', 'Vui lòng tạo hồ sơ công ty trước khi xem ứng viên.');
        }

        $applications = Application::query()
            ->with(['candidate', 'job'])
            ->whereHas('job', fn ($query) => $query->where('company_id', $company->id))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search')->toString();
                $query->where(function ($matches) use ($search): void {
                    $matches->whereHas('candidate', fn ($candidate) => $candidate
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%"))
                        ->orWhereHas('job', fn ($job) => $job->where('title', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('employer.applicants.index', compact('applications'));
    }

    public function show(Request $request, Application $application): View
    {
        $this->authorizeApplication($request, $application);

        $application->load(['candidate', 'job', 'cv']);

        return view('employer.applicants.show', compact('application'));
    }

    public function updateStatus(Request $request, Application $application): RedirectResponse
    {
        $this->authorizeApplication($request, $application);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'reviewing', 'shortlisted', 'rejected', 'hired'])],
        ]);
        $application->update($validated);

        return back()->with('status', 'Đã cập nhật trạng thái hồ sơ ứng tuyển.');
    }

    public function downloadCv(Request $request, Application $application)
    {
        $this->authorizeApplication($request, $application);

        $path = $application->cv_url;
        abort_unless(Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->download($path);
    }

    private function authorizeApplication(Request $request, Application $application): void
    {
        abort_unless(
            $application->job()->where('company_id', $request->user()->company?->id)->exists(),
            404
        );
    }
}
