@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    
    <!-- Page Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-[#142B47]">Hồ Sơ Cá Nhân</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Quản lý và hoàn thiện thông tin để thu hút các nhà tuyển dụng hàng đầu.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('candidate.cvs.index') }}" class="px-4 py-2.5 bg-white hover:bg-[#EAF2FA] text-[#2A81CB] border border-[#2A81CB]/30 rounded-xl text-xs font-bold transition shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                <span>Quản lý CV ({{ $cvsCount }}/5)</span>
            </a>
            <a href="{{ route('candidate.applications.index') }}" class="px-4 py-2.5 bg-white hover:bg-[#EAF2FA] text-[#142B47] border border-gray-200 rounded-xl text-xs font-bold transition shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                <span>Đơn ứng tuyển</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left: Completion % Score Card & Quick Tips (UC9) -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Completion Card (Pastel Blue Background #EAF2FA) -->
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-sm text-[#142B47] uppercase tracking-wider">Độ hoàn thiện hồ sơ</h3>
                    <span class="text-xl font-black text-[#2A81CB]">{{ $score }}%</span>
                </div>

                <!-- Progress Bar -->
                <div class="w-full bg-gray-100 rounded-full h-3 mb-4 overflow-hidden">
                    <div class="bg-gradient-to-r from-[#2A81CB] to-[#142B47] h-3 rounded-full transition-all duration-700" 
                         style="width: {{ $score }}%"></div>
                </div>

                @if($score < 100)
                    <div class="p-4 rounded-2xl bg-[#EAF2FA] border border-[#2A81CB]/20 text-xs space-y-2">
                        <p class="font-bold text-[#142B47] flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-[#2A81CB]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            Gợi ý để đạt 100% hồ sơ:
                        </p>
                        <ul class="space-y-1.5 text-gray-700">
                            @foreach($missingItems as $item)
                                <li class="flex items-start gap-1.5">
                                    <span class="text-[#2A81CB] font-bold">•</span>
                                    <span>{{ $item }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                        <span class="font-semibold">Tuyệt vời! Hồ sơ của bạn đã hoàn thiện 100% và sẵn sàng cho nhà tuyển dụng tiếp cận.</span>
                    </div>
                @endif

                <!-- Checklist breakdown -->
                <div class="mt-6 pt-5 border-t border-gray-100 space-y-2.5 text-xs">
                    <div class="flex items-center justify-between {{ !empty($user->phone) ? 'text-emerald-600 font-medium' : 'text-gray-400' }}">
                        <span>Thông tin cá nhân & SĐT</span>
                        <span>{{ !empty($user->phone) ? '✓ 15%' : '0%' }}</span>
                    </div>
                    <div class="flex items-center justify-between {{ !empty($profile->avatar_url) ? 'text-emerald-600 font-medium' : 'text-gray-400' }}">
                        <span>Ảnh đại diện</span>
                        <span>{{ !empty($profile->avatar_url) ? '✓ 15%' : '0%' }}</span>
                    </div>
                    <div class="flex items-center justify-between {{ !empty(trim($profile->career_summary ?? '')) ? 'text-emerald-600 font-medium' : 'text-gray-400' }}">
                        <span>Tóm tắt sự nghiệp</span>
                        <span>{{ !empty(trim($profile->career_summary ?? '')) ? '✓ 15%' : '0%' }}</span>
                    </div>
                    <div class="flex items-center justify-between {{ !empty(trim($profile->skills ?? '')) ? 'text-emerald-600 font-medium' : 'text-gray-400' }}">
                        <span>Kỹ năng chuyên môn</span>
                        <span>{{ !empty(trim($profile->skills ?? '')) ? '✓ 15%' : '0%' }}</span>
                    </div>
                    <div class="flex items-center justify-between {{ !empty(trim($profile->experience ?? '')) ? 'text-emerald-600 font-medium' : 'text-gray-400' }}">
                        <span>Kinh nghiệm làm việc</span>
                        <span>{{ !empty(trim($profile->experience ?? '')) ? '✓ 15%' : '0%' }}</span>
                    </div>
                    <div class="flex items-center justify-between {{ !empty(trim($profile->education ?? '')) ? 'text-emerald-600 font-medium' : 'text-gray-400' }}">
                        <span>Học vấn & Bằng cấp</span>
                        <span>{{ !empty(trim($profile->education ?? '')) ? '✓ 10%' : '0%' }}</span>
                    </div>
                    <div class="flex items-center justify-between {{ $profile->cvs()->exists() ? 'text-emerald-600 font-medium' : 'text-gray-400' }}">
                        <span>File CV đính kèm</span>
                        <span>{{ $profile->cvs()->exists() ? '✓ 15%' : '0%' }}</span>
                    </div>
                </div>

            </div>

            <!-- CV Quick Access Card -->
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <h4 class="font-bold text-sm text-[#142B47]">Kho CV cá nhân</h4>
                    <p class="text-xs text-gray-500 mt-0.5">Đã tải {{ $cvsCount }}/5 CV tối đa</p>
                </div>
                <a href="{{ route('candidate.cvs.index') }}" class="px-4 py-2 bg-[#E1EEFA] hover:bg-[#2A81CB] text-[#2A81CB] hover:text-white rounded-xl text-xs font-bold transition">
                    Xem CV
                </a>
            </div>

        </div>

        <!-- Right: Profile Form -->
        <div class="lg:col-span-8">
            <form action="{{ route('candidate.profile.update') }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-gray-100 space-y-6">
                @csrf
                @method('PUT')

                <!-- Avatar & General Info Header -->
                <div class="flex flex-col sm:flex-row items-center gap-6 pb-6 border-b border-gray-100">
                    <!-- Avatar Preview -->
                    <div class="relative group">
                        <div class="w-24 h-24 rounded-2xl bg-[#EAF2FA] text-[#2A81CB] font-bold text-3xl flex items-center justify-center border-2 border-[#2A81CB]/30 overflow-hidden shadow-sm" id="avatarPreviewContainer">
                            @if($profile->avatar_url)
                                <img src="{{ asset($profile->avatar_url) }}" id="avatarPreview" alt="{{ $user->name }}" class="w-full h-full object-cover">
                            @else
                                <span id="avatarInitial">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                <img src="" id="avatarPreview" class="hidden w-full h-full object-cover">
                            @endif
                        </div>
                        <label class="absolute -bottom-2 -right-2 bg-[#2A81CB] hover:bg-[#1F6BB0] text-white p-2 rounded-xl shadow-md cursor-pointer transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            <input type="file" name="avatar" accept="image/*" class="hidden" onchange="previewAvatar(this)">
                        </label>
                    </div>

                    <div class="space-y-1 text-center sm:text-left flex-1">
                        <h3 class="font-bold text-lg text-[#142B47]">{{ $user->name }}</h3>
                        <p class="text-xs text-gray-500">{{ $user->email }} • Ứng viên</p>
                        <p class="text-[11px] text-gray-400">Định dạng ảnh: JPG, PNG, WEBP. Dung lượng tối đa: 2MB.</p>
                    </div>
                </div>

                <!-- Input Fields: Name, Email, Phone -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#142B47] mb-1.5">
                            Họ và tên <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full px-4 py-2.5 bg-[#F7FAFC] border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-[#2A81CB] focus:border-transparent text-[#142B47]">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#142B47] mb-1.5">
                            Số điện thoại
                        </label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Ví dụ: 0987654321"
                               class="w-full px-4 py-2.5 bg-[#F7FAFC] border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-[#2A81CB] focus:border-transparent text-[#142B47]">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#142B47] mb-1.5">
                            Địa chỉ Email (Định danh tài khoản)
                        </label>
                        <input type="email" value="{{ $user->email }}" disabled
                               class="w-full px-4 py-2.5 bg-gray-100 border border-gray-200 rounded-xl text-xs text-gray-500 cursor-not-allowed">
                    </div>
                </div>

                <!-- Career Summary -->
                <div class="pt-4 border-t border-gray-100">
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#142B47] mb-1.5">
                        Tóm tắt sự nghiệp & Mục tiêu nghề nghiệp
                    </label>
                    <textarea name="career_summary" rows="4" 
                              placeholder="Mô tả ngắn gọn về định hướng nghề nghiệp, thế mạnh chuyên môn của bạn..."
                              class="w-full p-4 bg-[#F7FAFC] border border-gray-200 rounded-2xl text-xs focus:ring-2 focus:ring-[#2A81CB] focus:border-transparent text-[#142B47]">{{ old('career_summary', $profile->career_summary) }}</textarea>
                </div>

                <!-- Skills -->
                <div class="pt-4 border-t border-gray-100">
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#142B47] mb-1.5">
                        Kỹ năng chuyên môn (cách nhau bởi dấu phẩy)
                    </label>
                    <input type="text" name="skills" value="{{ old('skills', $profile->skills) }}" 
                           placeholder="Ví dụ: PHP, Laravel, Tailwind CSS, MySQL, Git, Docker, Tiếng Anh giao tiếp..."
                           class="w-full px-4 py-2.5 bg-[#F7FAFC] border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-[#2A81CB] focus:border-transparent text-[#142B47]">
                </div>

                <!-- Experience -->
                <div class="pt-4 border-t border-gray-100">
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#142B47] mb-1.5">
                        Kinh nghiệm làm việc & Dự án đã tham gia
                    </label>
                    <textarea name="experience" rows="5" 
                              placeholder="Liệt kê các vị trí, công ty hoặc dự án bạn từng tham gia (Tên công ty, vị trí, thời gian, nhiệm vụ chính)..."
                              class="w-full p-4 bg-[#F7FAFC] border border-gray-200 rounded-2xl text-xs focus:ring-2 focus:ring-[#2A81CB] focus:border-transparent text-[#142B47]">{{ old('experience', $profile->experience) }}</textarea>
                </div>

                <!-- Education -->
                <div class="pt-4 border-t border-gray-100">
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#142B47] mb-1.5">
                        Học vấn & Trình độ học vấn
                    </label>
                    <textarea name="education" rows="3" 
                              placeholder="Trường Đại học/Cao đẳng, Chuyên ngành, Niên khóa, Bằng cấp hoặc Chứng chỉ liên quan..."
                              class="w-full p-4 bg-[#F7FAFC] border border-gray-200 rounded-2xl text-xs focus:ring-2 focus:ring-[#2A81CB] focus:border-transparent text-[#142B47]">{{ old('education', $profile->education) }}</textarea>
                </div>

                <!-- Searchable Toggle -->
                <div class="pt-4 border-t border-gray-100 flex items-center justify-between p-4 bg-[#EAF2FA] rounded-2xl border border-[#2A81CB]/20">
                    <div>
                        <span class="block font-bold text-xs text-[#142B47]">Cho phép nhà tuyển dụng tìm kiếm hồ sơ</span>
                        <span class="block text-[11px] text-gray-600 mt-0.5">Bật tính năng này giúp các nhà tuyển dụng chủ động xem xét hồ sơ và liên hệ với bạn.</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_searchable" value="1" {{ old('is_searchable', $profile->is_searchable) ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#2A81CB]"></div>
                    </label>
                </div>

                <!-- Action Button (Primary Blue #2A81CB) -->
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                    <button type="submit" 
                            class="px-8 py-3 bg-[#2A81CB] hover:bg-[#1F6BB0] text-white font-bold text-xs rounded-xl transition shadow-md flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span>Lưu thay đổi hồ sơ</span>
                    </button>
                </div>

            </form>
        </div>

    </div>

</div>

<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var preview = document.getElementById('avatarPreview');
            var initial = document.getElementById('avatarInitial');
            if (preview) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
            }
            if (initial) {
                initial.classList.add('hidden');
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
