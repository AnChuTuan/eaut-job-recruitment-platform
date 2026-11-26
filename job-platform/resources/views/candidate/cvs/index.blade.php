@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl sm:text-3xl font-black text-[#142B47]">Quản Lý CV</h1>
                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $cvCount >= 5 ? 'bg-rose-100 text-rose-700 border border-rose-200' : 'bg-[#EAF2FA] text-[#2A81CB] border border-[#2A81CB]/20' }}">
                    {{ $cvCount }}/5 CV đã lưu
                </span>
            </div>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">
                Tải lên tối đa 5 phiên bản CV phù hợp với từng vị trí ứng tuyển khác nhau.
            </p>
        </div>

        <a href="{{ route('candidate.profile') }}" class="px-4 py-2 bg-white hover:bg-[#EAF2FA] text-[#142B47] border border-gray-200 rounded-xl text-xs font-bold transition shadow-sm flex items-center gap-2 w-fit">
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            <span>Quay lại hồ sơ</span>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left: Upload New CV Form (Disabled when count >= 5) -->
        <div class="lg:col-span-5">
            <div class="bg-white p-6 sm:p-7 rounded-3xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-sm text-[#142B47] uppercase tracking-wider mb-4 pb-3 border-b border-gray-100 flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#2A81CB]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                    Tải lên CV mới
                </h3>

                @if(!$canUpload)
                    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-2">
                        <div class="flex items-center gap-2 font-bold">
                            <svg class="w-4 h-4 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                            <span>Đã đạt giới hạn tối đa 5 CV</span>
                        </div>
                        <p class="leading-relaxed">
                            Mỗi ứng viên được lưu tối đa 5 tệp CV trên hệ thống. Để tải lên phiên bản mới, vui lòng xóa bớt 1 CV cũ không còn sử dụng ở danh sách bên cạnh.
                        </p>
                    </div>
                @else
                    <form action="{{ route('candidate.cvs.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf

                        <!-- CV Title -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#142B47] mb-1.5">
                                Tên gợi nhớ CV <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="cv_title" value="{{ old('cv_title') }}" required
                                   placeholder="Ví dụ: CV Lập trình viên PHP, CV Marketing 2026..."
                                   class="w-full px-4 py-2.5 bg-[#F7FAFC] border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-[#2A81CB] focus:border-transparent text-[#142B47]">
                        </div>

                        <!-- File Input -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#142B47] mb-1.5">
                                Tệp đính kèm <span class="text-rose-500">*</span>
                            </label>
                            <div class="border-2 border-dashed border-gray-300 hover:border-[#2A81CB] rounded-2xl p-5 text-center transition bg-[#F7FAFC]">
                                <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <label class="cursor-pointer">
                                    <span class="text-xs font-bold text-[#2A81CB] hover:underline">Chọn file CV từ máy tính</span>
                                    <input type="file" name="cv_file" required accept=".pdf,.doc,.docx" class="hidden" 
                                           onchange="document.getElementById('uploadFileName').innerText = this.files[0] ? this.files[0].name + ' (' + (this.files[0].size/1024).toFixed(0) + ' KB)' : ''">
                                </label>
                                <p class="text-[11px] text-gray-400 mt-1">Định dạng: PDF, DOC, DOCX. Tối đa 5MB.</p>
                                <p id="uploadFileName" class="text-xs font-semibold text-[#2A81CB] mt-2"></p>
                            </div>
                        </div>

                        <!-- Checkbox Default -->
                        <div class="flex items-center gap-2 pt-1">
                            <input type="checkbox" name="is_default" id="is_default" value="1" {{ $cvCount === 0 ? 'checked' : '' }} class="rounded text-[#2A81CB] focus:ring-[#2A81CB]">
                            <label for="is_default" class="text-xs text-gray-700 cursor-pointer font-medium">
                                Đặt làm CV chính mặc định khi ứng tuyển
                            </label>
                        </div>

                        <!-- Submit Button (Primary Blue #2A81CB) -->
                        <div class="pt-3">
                            <button type="submit" 
                                    class="w-full py-3 px-6 bg-[#2A81CB] hover:bg-[#1F6BB0] text-white font-bold text-xs rounded-xl transition shadow-md flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                                <span>Tải lên hệ thống</span>
                            </button>
                        </div>

                    </form>
                @endif

            </div>
        </div>

        <!-- Right: CV List (Up to 5 CVs) -->
        <div class="lg:col-span-7 space-y-4">
            
            <div class="bg-white p-6 sm:p-7 rounded-3xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between pb-4 border-b border-gray-100 mb-6">
                    <h3 class="font-bold text-sm text-[#142B47] uppercase tracking-wider">
                        Danh sách CV đã lưu ({{ $cvCount }}/5)
                    </h3>
                    <span class="text-xs text-gray-400">Tối đa 5 CV</span>
                </div>

                @if($cvs->isEmpty())
                    <div class="text-center py-10">
                        <div class="w-14 h-14 rounded-2xl bg-[#EAF2FA] text-[#2A81CB] flex items-center justify-center mx-auto mb-3">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        </div>
                        <h4 class="font-bold text-sm text-[#142B47] mb-1">Chưa có CV nào được lưu</h4>
                        <p class="text-xs text-gray-500 max-w-sm mx-auto">
                            Hãy tải lên ít nhất 1 file CV để nộp đơn ứng tuyển nhanh chóng và tăng điểm hoàn thiện hồ sơ.
                        </p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($cvs as $cv)
                            <div class="p-4 sm:p-5 rounded-2xl border {{ $cv->is_default ? 'border-[#2A81CB] bg-[#EAF2FA]/30' : 'border-gray-200 bg-white' }} hover:shadow-sm transition flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                
                                <!-- CV Info -->
                                <div class="flex items-start gap-3.5 flex-1">
                                    <div class="w-11 h-11 rounded-xl {{ $cv->is_default ? 'bg-[#2A81CB] text-white' : 'bg-[#EAF2FA] text-[#2A81CB]' }} flex items-center justify-center shrink-0 shadow-sm">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    </div>

                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h4 class="font-bold text-sm text-[#142B47]">{{ $cv->cv_title }}</h4>
                                            @if($cv->is_default)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#2A81CB] text-white">
                                                    CV Mặc định
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-2 text-[11px] text-gray-500">
                                            <span>Dung lượng: {{ $cv->formatted_file_size }}</span>
                                            <span>•</span>
                                            <span>Cập nhật: {{ $cv->created_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center gap-2 w-full sm:w-auto justify-end pt-3 sm:pt-0 border-t sm:border-t-0 border-gray-100">
                                    
                                    <!-- Set Default Action -->
                                    @if(!$cv->is_default)
                                        <form action="{{ route('candidate.cvs.default', $cv) }}" method="POST">
                                            @csrf
                                            <button type="submit" 
                                                    title="Đặt làm CV chính"
                                                    class="px-3 py-1.5 rounded-xl border border-gray-200 hover:border-[#2A81CB] text-[11px] font-semibold text-gray-600 hover:text-[#2A81CB] transition">
                                                Đặt mặc định
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Download Action -->
                                    <a href="{{ route('candidate.cvs.download', $cv) }}" 
                                       title="Tải xuống CV"
                                       class="p-2 rounded-xl bg-gray-100 hover:bg-[#EAF2FA] text-gray-600 hover:text-[#2A81CB] transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                    </a>

                                    <!-- Delete Action with confirm -->
                                    <form action="{{ route('candidate.cvs.destroy', $cv) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa file CV này? Thao tác này không thể hoàn tác.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                title="Xóa CV"
                                                class="p-2 rounded-xl bg-gray-100 hover:bg-rose-50 text-gray-600 hover:text-rose-600 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>

                                </div>

                            </div>
                        @endforeach
                    </div>
                @endif

            </div>

        </div>

    </div>

</div>
@endsection
