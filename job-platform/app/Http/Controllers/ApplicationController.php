<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\CandidateProfile;
use App\Models\Cv;
use App\Models\Job;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends Controller
{
    /**
     * UC2: Apply for Job - Nộp đơn ứng tuyển.
     */
    public function store(Request $request, Job $job)
    {
        $user = Auth::user();

        // Đảm bảo vai trò là candidate
        if ($user->role !== 'candidate' && $user->role !== 'admin') {
            return back()->with('error', 'Chỉ tài khoản ứng viên mới có thể nộp đơn ứng tuyển.');
        }

        // 1. Chống nộp trùng: Kiểm tra đã từng nộp cho job này chưa
        $existingApplication = Application::where('job_id', $job->id)
            ->where('candidate_id', $user->id)
            ->first();

        if ($existingApplication) {
            return back()->with('error', 'Bạn đã nộp đơn ứng tuyển cho vị trí này rồi! Vui lòng không nộp lại.');
        }

        // Lấy hoặc tự tạo CandidateProfile nếu chưa có
        $profile = $user->candidateProfile ?: CandidateProfile::create([
            'user_id' => $user->id,
            'completion_score' => 0,
            'is_searchable' => false,
        ]);

        // Validate dữ liệu gửi lên
        $request->validate([
            'cv_option' => 'required|in:saved,new',
            'cv_id' => 'required_if:cv_option,saved|nullable|exists:cvs,id',
            'new_cv' => 'required_if:cv_option,new|nullable|file|mimes:pdf,doc,docx|max:5120', // max 5MB
            'cover_letter' => 'nullable|string|max:2000',
        ], [
            'cv_option.required' => 'Vui lòng chọn hình thức gửi CV.',
            'cv_id.required_if' => 'Vui lòng chọn một CV đã lưu trong hồ sơ.',
            'cv_id.exists' => 'CV được chọn không hợp lệ.',
            'new_cv.required_if' => 'Vui lòng chọn file CV để tải lên.',
            'new_cv.mimes' => 'File CV phải có định dạng PDF, DOC hoặc DOCX.',
            'new_cv.max' => 'Dung lượng file CV không được vượt quá 5MB.',
        ]);

        $cvId = null;
        $cvUrl = null;

        if ($request->cv_option === 'saved') {
            $selectedCv = Cv::where('id', $request->cv_id)
                ->where('candidate_profile_id', $profile->id)
                ->firstOrFail();

            $cvId = $selectedCv->id;
            $cvUrl = $selectedCv->cv_url;
        } else {
            // Tải lên CV mới
            $file = $request->file('new_cv');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $filename = 'cv_' . $user->id . '_' . time() . '.' . $extension;

            // Lưu vào storage/app/public/cvs
            $path = $file->storeAs('cvs', $filename, 'public');
            $cvUrl = 'storage/' . $path;

            // Kiểm tra số lượng CV hiện có của ứng viên (tối đa 5 CV)
            $currentCvsCount = $profile->cvs()->count();
            if ($currentCvsCount < 5) {
                // Tự động lưu vào danh sách CV của ứng viên
                $isDefault = $currentCvsCount === 0;
                $newCvRecord = Cv::create([
                    'candidate_profile_id' => $profile->id,
                    'cv_title' => $originalName ?: 'CV Ứng tuyển ' . date('d/m/Y'),
                    'cv_url' => $cvUrl,
                    'file_size' => $file->getSize(),
                    'is_default' => $isDefault,
                ]);
                $cvId = $newCvRecord->id;
                $profile->recalculateCompletionScore();
            }
        }

        // Lưu đơn ứng tuyển vào DB (Bọc transaction để đảm bảo toàn vẹn)
        DB::transaction(function () use ($job, $user, $cvId, $cvUrl, $request) {
            Application::create([
                'job_id' => $job->id,
                'candidate_id' => $user->id,
                'cv_id' => $cvId,
                'cv_url' => $cvUrl,
                'cover_letter' => $request->cover_letter,
                'status' => 'pending',
            ]);

            // Gửi thông báo cho Nhà tuyển dụng sở hữu công việc này
            $employerUser = $job->company->user ?? null;
            if ($employerUser) {
                Notification::create([
                    'user_id' => $employerUser->id,
                    'title' => 'Ứng viên mới ứng tuyển: ' . $job->title,
                    'message' => "Ứng viên {$user->name} vừa nộp đơn ứng tuyển vào vị trí '{$job->title}'.",
                    'is_read' => false,
                ]);
            }

            // Gửi thông báo cho chính ứng viên xác nhận nộp thành công
            Notification::create([
                'user_id' => $user->id,
                'title' => 'Ứng tuyển thành công: ' . $job->title,
                'message' => "Bạn đã nộp đơn ứng tuyển thành công vào vị trí '{$job->title}' tại {$job->company->company_name}. Hãy chú ý kiểm tra thông báo từ nhà tuyển dụng nhé!",
                'is_read' => false,
            ]);
        });

        return back()->with('success', 'Nộp đơn ứng tuyển thành công! Nhà tuyển dụng sẽ xem xét hồ sơ của bạn.');
    }

    /**
     * Danh sách công việc ứng viên đã ứng tuyển.
     */
    public function index()
    {
        $user = Auth::user();
        $applications = Application::with(['job.company', 'cv'])
            ->where('candidate_id', $user->id)
            ->latest()
            ->paginate(10);

        return view('candidate.applications.index', compact('applications'));
    }
}
