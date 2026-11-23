<?php

namespace App\Http\Controllers;

use App\Models\CandidateProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CandidateProfileController extends Controller
{
    /**
     * UC9: Manage Candidate Profile - Xem và chỉnh sửa hồ sơ ứng viên.
     */
    public function edit()
    {
        $user = Auth::user();

        // Lấy hoặc tạo hồ sơ ứng viên
        $profile = CandidateProfile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'completion_score' => 0,
                'is_searchable' => false,
            ]
        );

        // Tính lại điểm hoàn thiện mới nhất
        $score = $profile->recalculateCompletionScore();

        // Danh sách gợi ý các mục còn thiếu
        $missingItems = [];
        if (empty($user->phone)) {
            $missingItems[] = 'Cập nhật số điện thoại liên hệ (+15%)';
        }
        if (empty($profile->avatar_url)) {
            $missingItems[] = 'Tải lên ảnh đại diện chuyên nghiệp (+15%)';
        }
        if (empty(trim($profile->career_summary ?? ''))) {
            $missingItems[] = 'Viết tóm tắt mục tiêu & định hướng nghề nghiệp (+15%)';
        }
        if (empty(trim($profile->skills ?? ''))) {
            $missingItems[] = 'Thêm danh sách kỹ năng chuyên môn (+15%)';
        }
        if (empty(trim($profile->experience ?? ''))) {
            $missingItems[] = 'Mô tả kinh nghiệm hoặc dự án đã tham gia (+15%)';
        }
        if (empty(trim($profile->education ?? ''))) {
            $missingItems[] = 'Điền thông tin trình độ học vấn / trường đại học (+10%)';
        }
        if (!$profile->cvs()->exists()) {
            $missingItems[] = 'Tải lên ít nhất 1 file CV (+15%)';
        }

        $cvsCount = $profile->cvs()->count();

        return view('candidate.profile', compact('user', 'profile', 'score', 'missingItems', 'cvsCount'));
    }

    /**
     * UC9: Cập nhật thông tin hồ sơ ứng viên.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:150',
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // max 2MB
            'career_summary' => 'nullable|string|max:2500',
            'skills' => 'nullable|string|max:1000',
            'experience' => 'nullable|string|max:5000',
            'education' => 'nullable|string|max:3000',
            'is_searchable' => 'nullable|boolean',
        ], [
            'name.required' => 'Họ và tên không được để trống.',
            'name.max' => 'Họ và tên không được vượt quá 150 ký tự.',
            'avatar.image' => 'Ảnh đại diện phải là tệp hình ảnh.',
            'avatar.max' => 'Dung lượng ảnh đại diện tối đa là 2MB.',
        ]);

        // Cập nhật thông tin User cơ bản
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->save();

        // Lấy hoặc tạo CandidateProfile
        $profile = CandidateProfile::firstOrCreate(
            ['user_id' => $user->id],
            ['completion_score' => 0]
        );

        // Upload avatar nếu có
        if ($request->hasFile('avatar')) {
            // Xóa avatar cũ nếu có và đang nằm trong storage
            if ($profile->avatar_url && str_starts_with($profile->avatar_url, 'storage/')) {
                $oldPath = str_replace('storage/', '', $profile->avatar_url);
                Storage::disk('public')->delete($oldPath);
            }

            $avatarFile = $request->file('avatar');
            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $avatarFile->getClientOriginalExtension();
            $path = $avatarFile->storeAs('avatars', $filename, 'public');
            $profile->avatar_url = 'storage/' . $path;
        }

        // Cập nhật các trường hồ sơ
        $profile->career_summary = $request->career_summary;
        $profile->skills = $request->skills;
        $profile->experience = $request->experience;
        $profile->education = $request->education;
        $profile->is_searchable = $request->has('is_searchable');

        $profile->save();

        // Tính lại điểm hoàn thiện %
        $score = $profile->recalculateCompletionScore();

        return redirect()->route('candidate.profile')->with('success', "Cập nhật hồ sơ thành công! Mức độ hoàn thiện: {$score}%.");
    }
}
