<?php

namespace App\Http\Controllers;

use App\Models\CandidateProfile;
use App\Models\Cv;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CvController extends Controller
{
    /**
     * UC9: Danh sách quản lý tối đa 5 CV.
     */
    public function index()
    {
        $user = Auth::user();

        $profile = CandidateProfile::firstOrCreate(
            ['user_id' => $user->id],
            ['completion_score' => 0]
        );

        $cvs = $profile->cvs()->get();
        $cvCount = $cvs->count();
        $canUpload = $cvCount < 5;

        return view('candidate.cvs.index', compact('profile', 'cvs', 'cvCount', 'canUpload'));
    }

    /**
     * UC9: Upload CV mới (Giới hạn nghiêm ngặt tối đa 5 CV).
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $profile = CandidateProfile::firstOrCreate(
            ['user_id' => $user->id],
            ['completion_score' => 0]
        );

        // KIỂM TRA CHẶN: Tối đa 5 CV
        $currentCount = $profile->cvs()->count();
        if ($currentCount >= 5) {
            return back()->with('error', 'Bạn đã lưu tối đa 5 CV! Vui lòng xóa bớt CV cũ trước khi tải lên CV mới.');
        }

        $request->validate([
            'cv_title' => 'required|string|max:150',
            'cv_file' => 'required|file|mimes:pdf,doc,docx|max:5120', // max 5MB
            'is_default' => 'nullable|boolean',
        ], [
            'cv_title.required' => 'Vui lòng nhập tên gợi nhớ cho CV.',
            'cv_title.max' => 'Tên gợi nhớ không được vượt quá 150 ký tự.',
            'cv_file.required' => 'Vui lòng chọn file CV.',
            'cv_file.mimes' => 'Chỉ chấp nhận tệp định dạng PDF, DOC hoặc DOCX.',
            'cv_file.max' => 'Dung lượng file tối đa là 5MB.',
        ]);

        $file = $request->file('cv_file');
        $extension = $file->getClientOriginalExtension();
        $filename = 'cv_' . $user->id . '_' . time() . '.' . $extension;

        // Lưu file vào thư mục storage/app/public/cvs
        $path = $file->storeAs('cvs', $filename, 'public');
        $cvUrl = 'storage/' . $path;

        // Xác định xem có đặt làm mặc định không
        $isDefault = $request->boolean('is_default') || $currentCount === 0;

        if ($isDefault) {
            // Đặt các CV khác thành không mặc định
            $profile->cvs()->update(['is_default' => false]);
        }

        Cv::create([
            'candidate_profile_id' => $profile->id,
            'cv_title' => $request->cv_title,
            'cv_url' => $cvUrl,
            'file_size' => $file->getSize(),
            'is_default' => $isDefault,
        ]);

        // Tính lại điểm hoàn thiện
        $profile->recalculateCompletionScore();

        return redirect()->route('candidate.cvs.index')->with('success', 'Tải lên CV thành công!');
    }

    /**
     * UC9: Đặt CV làm mặc định.
     */
    public function setDefault(Cv $cv)
    {
        $user = Auth::user();
        $profile = $user->candidateProfile;

        if (!$profile || $cv->candidate_profile_id !== $profile->id) {
            abort(403, 'Bạn không có quyền thực hiện thao tác này.');
        }

        // Bỏ mặc định tất cả CV của ứng viên này
        $profile->cvs()->update(['is_default' => false]);

        // Đặt CV này làm mặc định
        $cv->is_default = true;
        $cv->save();

        return back()->with('success', "Đã đặt CV '{$cv->cv_title}' làm CV chính mặc định.");
    }

    /**
     * UC9: Xóa CV.
     */
    public function destroy(Cv $cv)
    {
        $user = Auth::user();
        $profile = $user->candidateProfile;

        if (!$profile || $cv->candidate_profile_id !== $profile->id) {
            abort(403, 'Bạn không có quyền xóa CV này.');
        }

        $wasDefault = $cv->is_default;

        // Xóa file vật lý
        if ($cv->cv_url && str_starts_with($cv->cv_url, 'storage/')) {
            $relativePath = str_replace('storage/', '', $cv->cv_url);
            Storage::disk('public')->delete($relativePath);
        }

        // Xóa bản ghi DB
        $cv->delete();

        // Nếu vừa xóa CV mặc định, tự động gán CV gần nhất làm mặc định
        if ($wasDefault) {
            $latestCv = $profile->cvs()->first();
            if ($latestCv) {
                $latestCv->is_default = true;
                $latestCv->save();
            }
        }

        // Cập nhật lại điểm hoàn thiện
        $profile->recalculateCompletionScore();

        return back()->with('success', 'Đã xóa CV thành công.');
    }

    /**
     * UC9: Tải xuống file CV.
     */
    public function download(Cv $cv)
    {
        $user = Auth::user();

        // Ứng viên sở hữu CV hoặc admin có thể tải
        $isOwner = $user && $user->candidateProfile && $user->candidateProfile->id === $cv->candidate_profile_id;
        $isAdmin = $user && $user->isAdmin();
        $isEmployer = $user && $user->isEmployer();

        if (!$isOwner && !$isAdmin && !$isEmployer) {
            abort(403, 'Bạn không có quyền truy cập CV này.');
        }

        if (str_starts_with($cv->cv_url, 'storage/')) {
            $relativePath = str_replace('storage/', '', $cv->cv_url);
            if (Storage::disk('public')->exists($relativePath)) {
                return Storage::disk('public')->download($relativePath, $cv->cv_title . '.' . pathinfo($relativePath, PATHINFO_EXTENSION));
            }
        }

        abort(404, 'Tệp CV không tồn tại trên hệ thống.');
    }
}
