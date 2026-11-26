@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ showApplyModal: false, cvOption: '{{ $userCvs->count() > 0 ? 'saved' : 'new' }}' }">
    
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-gray-500 mb-6">
        <a href="{{ route('jobs.index') }}" class="hover:text-[#2A81CB]">Trang chủ</a>
        <span>/</span>
        <a href="{{ route('jobs.index', ['category' => $job->category]) }}" class="hover:text-[#2A81CB]">{{ $job->category }}</a>
        <span>/</span>
        <span class="text-[#142B47] font-medium truncate max-w-xs">{{ $job->title }}</span>
    </nav>

    <!-- Top Job Header Card (White Card with Dark Navy #142B47 and Primary Blue #2A81CB) -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-gray-100 mb-8">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            
            <div class="flex items-start sm:items-center gap-5 flex-1">
                <!-- Company Logo -->
                <div class="w-20 h-20 rounded-2xl bg-[#EAF2FA] text-[#2A81CB] font-bold text-2xl flex items-center justify-center shrink-0 border border-[#2A81CB]/20 overflow-hidden shadow-sm">
                    @if($job->company && $job->company->logo_url)
                        <img src="{{ asset($job->company->logo_url) }}" alt="{{ $job->company->company_name }}" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr($job->company->company_name ?? 'C', 0, 1)) }}
                    @endif
                </div>

                <!-- Job Info -->
                <div class="space-y-2">
                    <h1 class="text-xl sm:text-2xl font-black text-[#142B47]">
                        {{ $job->title }}
                    </h1>
                    
                    <div class="flex items-center gap-2 flex-wrap text-sm text-gray-600">
                        <span class="font-semibold text-[#142B47]">{{ $job->company->company_name ?? 'Doanh nghiệp tuyển dụng' }}</span>
                        @if($job->company && $job->company->verification_status === 'verified')
                            <span class="inline-flex items-center gap-1 text-xs text-[#2A81CB] bg-[#EAF2FA] px-2 py-0.5 rounded-full font-medium">
                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                                Đã xác thực
                            </span>
                        @endif
                        <span>•</span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /></svg>
                            {{ $job->location }}
                        </span>
                    </div>

                    <!-- Tags -->
                    <div class="flex items-center gap-2 pt-1 flex-wrap">
                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-bold bg-[#EAF2FA] text-[#2A81CB]">
                            {{ $job->formatted_salary }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs bg-gray-100 text-gray-700 font-medium">
                            {{ $job->job_level ?? 'Mọi cấp bậc' }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs bg-gray-100 text-gray-700 font-medium">
                            {{ $job->employment_type ?? 'Toàn thời gian' }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs bg-gray-100 text-gray-700 font-medium">
                            Hạn nộp: {{ $job->deadline ? $job->deadline->format('d/m/Y') : 'Không giới hạn' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Apply CTA Button (Primary Blue #2A81CB) -->
            <div class="w-full md:w-auto shrink-0 pt-4 md:pt-0">
                @if($hasApplied)
                    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-center md:text-right">
                        <div class="flex items-center gap-2 justify-center md:justify-end font-bold text-sm">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Đã ứng tuyển</span>
                        </div>
                        <p class="text-xs text-emerald-700 mt-1">
                            Nộp ngày {{ $userApplication->created_at->format('d/m/Y') }} • Trạng thái: <strong class="uppercase">{{ $userApplication->status }}</strong>
                        </p>
                    </div>
                @else
                    @auth
                        @if(Auth::user()->isCandidate())
                            <button @click="showApplyModal = true" 
                                    class="w-full md:w-auto px-8 py-3.5 bg-[#2A81CB] hover:bg-[#1F6BB0] text-white font-bold text-sm rounded-2xl transition shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                <span>Ứng tuyển ngay</span>
                            </button>
                        @else
                            <div class="p-3 bg-amber-50 rounded-xl text-xs text-amber-800 border border-amber-200">
                                Bạn đang đăng nhập bằng tài khoản Nhà tuyển dụng/Admin.
                            </div>
                        @endif
                    @else
                        <a href="{{ route('login') }}" 
                           class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-[#2A81CB] hover:bg-[#1F6BB0] text-white font-bold text-sm rounded-2xl transition shadow-md">
                            Đăng nhập để ứng tuyển
                        </a>
                    @endauth
                @endif
            </div>

        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left: Job Details Content -->
        <div class="lg:col-span-8 space-y-8">
            
            <!-- Section 1: Job Description -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-[#142B47] mb-4 pb-3 border-b border-gray-100 flex items-center gap-2">
                    <span class="w-2 h-6 bg-[#2A81CB] rounded-full inline-block"></span>
                    Mô tả công việc
                </h3>
                <div class="prose max-w-none text-sm text-gray-700 leading-relaxed whitespace-pre-line">
                    {{ $job->description }}
                </div>
            </div>

            <!-- Section 2: Requirements -->
            @if($job->requirements)
                <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-[#142B47] mb-4 pb-3 border-b border-gray-100 flex items-center gap-2">
                        <span class="w-2 h-6 bg-[#2A81CB] rounded-full inline-block"></span>
                        Yêu cầu ứng viên
                    </h3>
                    <div class="prose max-w-none text-sm text-gray-700 leading-relaxed whitespace-pre-line">
                        {{ $job->requirements }}
                    </div>
                </div>
            @endif

            <!-- Section 3: Benefits -->
            @if($job->benefits)
                <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-[#142B47] mb-4 pb-3 border-b border-gray-100 flex items-center gap-2">
                        <span class="w-2 h-6 bg-[#2A81CB] rounded-full inline-block"></span>
                        Quyền lợi được hưởng
                    </h3>
                    <div class="prose max-w-none text-sm text-gray-700 leading-relaxed whitespace-pre-line">
                        {{ $job->benefits }}
                    </div>
                </div>
            @endif

            <!-- Section 4: Required Skills -->
            @if($job->skills)
                <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-[#142B47] mb-4 pb-3 border-b border-gray-100 flex items-center gap-2">
                        <span class="w-2 h-6 bg-[#2A81CB] rounded-full inline-block"></span>
                        Kỹ năng yêu cầu
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach(explode(',', $job->skills) as $skill)
                            @if(trim($skill))
                                <span class="px-3.5 py-1.5 bg-[#EAF2FA] text-[#2A81CB] rounded-xl text-xs font-semibold border border-[#2A81CB]/20">
                                    {{ trim($skill) }}
                                </span>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Bottom CTA Banner -->
            @if(!$hasApplied && Auth::check() && Auth::user()->isCandidate())
                <div class="p-6 bg-gradient-to-r from-[#EAF2FA] to-white rounded-3xl border border-[#2A81CB]/20 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div>
                        <h4 class="font-bold text-[#142B47] text-base">Bạn quan tâm đến cơ hội này?</h4>
                        <p class="text-xs text-gray-600 mt-1">Đừng bỏ lỡ! Hãy nộp hồ sơ ngay trước ngày {{ $job->deadline ? $job->deadline->format('d/m/Y') : '' }}.</p>
                    </div>
                    <button @click="showApplyModal = true" 
                            class="px-6 py-3 bg-[#2A81CB] hover:bg-[#1F6BB0] text-white font-bold text-xs rounded-xl transition shadow-md shrink-0">
                        Nộp đơn ứng tuyển ngay
                    </button>
                </div>
            @endif

        </div>

        <!-- Right: Overview & Company Sidebar -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Job Overview Card -->
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 space-y-4">
                <h4 class="font-bold text-[#142B47] text-sm uppercase tracking-wider pb-3 border-b border-gray-100">
                    Thông tin chung
                </h4>

                <div class="space-y-3.5 text-xs">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-[#EAF2FA] text-[#2A81CB] flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div>
                            <span class="block text-gray-400">Mức lương</span>
                            <span class="font-bold text-sm text-[#2A81CB]">{{ $job->formatted_salary }}</span>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-[#EAF2FA] text-[#2A81CB] flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /></svg>
                        </div>
                        <div>
                            <span class="block text-gray-400">Địa điểm</span>
                            <span class="font-medium text-[#142B47]">{{ $job->location }}</span>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-[#EAF2FA] text-[#2A81CB] flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                        </div>
                        <div>
                            <span class="block text-gray-400">Cấp bậc</span>
                            <span class="font-medium text-[#142B47]">{{ $job->job_level ?? 'Mọi cấp bậc' }}</span>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-[#EAF2FA] text-[#2A81CB] flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div>
                            <span class="block text-gray-400">Hình thức làm việc</span>
                            <span class="font-medium text-[#142B47]">{{ $job->employment_type ?? 'Toàn thời gian' }}</span>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-[#EAF2FA] text-[#2A81CB] flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        </div>
                        <div>
                            <span class="block text-gray-400">Số lượng tuyển dụng</span>
                            <span class="font-medium text-[#142B47]">{{ $job->quantity }} người</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Company Overview Card -->
            @if($job->company)
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 space-y-4">
                    <h4 class="font-bold text-[#142B47] text-sm uppercase tracking-wider pb-3 border-b border-gray-100">
                        Về công ty
                    </h4>

                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-[#EAF2FA] text-[#2A81CB] font-bold text-base flex items-center justify-center shrink-0 border border-[#2A81CB]/20 overflow-hidden">
                            @if($job->company->logo_url)
                                <img src="{{ asset($job->company->logo_url) }}" alt="{{ $job->company->company_name }}" class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(substr($job->company->company_name, 0, 1)) }}
                            @endif
                        </div>
                        <div>
                            <h5 class="font-bold text-sm text-[#142B47]">{{ $job->company->company_name }}</h5>
                            <span class="text-xs text-gray-500">{{ $job->company->industry ?? 'Doanh nghiệp' }}</span>
                        </div>
                    </div>

                    @if($job->company->description)
                        <p class="text-xs text-gray-600 leading-relaxed line-clamp-4">
                            {{ $job->company->description }}
                        </p>
                    @endif

                    <div class="space-y-2 text-xs text-gray-600 pt-2 border-t border-gray-100">
                        @if($job->company->company_size)
                            <div class="flex items-center gap-2">
                                <span class="text-gray-400">Quy mô:</span>
                                <span class="font-medium text-[#142B47]">{{ $job->company->company_size }}</span>
                            </div>
                        @endif
                        @if($job->company->address)
                            <div class="flex items-center gap-2">
                                <span class="text-gray-400">Trụ sở:</span>
                                <span class="font-medium text-[#142B47]">{{ $job->company->address }}</span>
                            </div>
                        @endif
                        @if($job->company->website)
                            <div class="flex items-center gap-2">
                                <span class="text-gray-400">Website:</span>
                                <a href="{{ $job->company->website }}" target="_blank" class="font-medium text-[#2A81CB] hover:underline truncate">
                                    {{ $job->company->website }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Related Jobs -->
            @if($relatedJobs->isNotEmpty())
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 space-y-4">
                    <h4 class="font-bold text-[#142B47] text-sm uppercase tracking-wider pb-3 border-b border-gray-100">
                        Việc làm liên quan
                    </h4>

                    <div class="space-y-3">
                        @foreach($relatedJobs as $rJob)
                            <a href="{{ route('jobs.show', $rJob) }}" class="block p-3 rounded-2xl bg-[#F7FAFC] hover:bg-[#EAF2FA] transition border border-gray-100 group">
                                <h6 class="font-bold text-xs text-[#142B47] group-hover:text-[#2A81CB] transition line-clamp-1">
                                    {{ $rJob->title }}
                                </h6>
                                <p class="text-[11px] text-gray-500 mt-0.5 line-clamp-1">{{ $rJob->company->company_name ?? '' }}</p>
                                <div class="flex items-center justify-between text-[11px] mt-2">
                                    <span class="font-semibold text-[#2A81CB]">{{ $rJob->formatted_salary }}</span>
                                    <span class="text-gray-400">{{ $rJob->location }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

    </div>

    <!-- UC2: Application Modal -->
    <div x-show="showApplyModal" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
        
        <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 w-full max-w-xl overflow-hidden transform transition-all animate-scale-up" 
             @click.outside="showApplyModal = false">
            
            <!-- Modal Header -->
            <div class="px-6 py-5 bg-[#EAF2FA] border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-[#142B47]">Nộp đơn ứng tuyển</h3>
                    <p class="text-xs text-gray-600 mt-0.5">{{ $job->title }} • {{ $job->company->company_name ?? '' }}</p>
                </div>
                <button @click="showApplyModal = false" class="text-gray-400 hover:text-gray-600 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <!-- Modal Form -->
            <form action="{{ route('jobs.apply', $job) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
                @csrf

                <!-- Candidate Contact Summary -->
                @auth
                    <div class="p-4 rounded-2xl bg-[#F7FAFC] border border-gray-100 text-xs space-y-1">
                        <span class="block font-bold text-[#142B47] uppercase tracking-wider text-[10px] text-gray-400">Thông tin ứng viên:</span>
                        <div class="grid grid-cols-2 gap-2 text-gray-700 pt-1">
                            <div><strong>Họ tên:</strong> {{ Auth::user()->name }}</div>
                            <div><strong>Email:</strong> {{ Auth::user()->email }}</div>
                            <div class="col-span-2"><strong>Số điện thoại:</strong> {{ Auth::user()->phone ?? 'Chưa cập nhật' }}</div>
                        </div>
                    </div>
                @endauth

                <!-- CV Selection Options -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#142B47] mb-2">
                        Chọn CV ứng tuyển <span class="text-rose-500">*</span>
                    </label>

                    @if($userCvs->isNotEmpty())
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <label class="p-3 border rounded-xl flex items-center gap-2 cursor-pointer transition text-xs font-semibold"
                                   :class="cvOption === 'saved' ? 'border-[#2A81CB] bg-[#EAF2FA] text-[#2A81CB]' : 'border-gray-200 text-gray-700'">
                                <input type="radio" name="cv_option" value="saved" x-model="cvOption" class="text-[#2A81CB] focus:ring-[#2A81CB]">
                                <span>CV đã lưu ({{ $userCvs->count() }})</span>
                            </label>

                            <label class="p-3 border rounded-xl flex items-center gap-2 cursor-pointer transition text-xs font-semibold"
                                   :class="cvOption === 'new' ? 'border-[#2A81CB] bg-[#EAF2FA] text-[#2A81CB]' : 'border-gray-200 text-gray-700'">
                                <input type="radio" name="cv_option" value="new" x-model="cvOption" class="text-[#2A81CB] focus:ring-[#2A81CB]">
                                <span>Tải lên CV mới</span>
                            </label>
                        </div>
                    @else
                        <input type="hidden" name="cv_option" value="new">
                    @endif

                    <!-- Option A: Select from saved CVs -->
                    @if($userCvs->isNotEmpty())
                        <div x-show="cvOption === 'saved'" class="space-y-2">
                            @foreach($userCvs as $cv)
                                <label class="p-3 border rounded-xl flex items-center justify-between cursor-pointer hover:bg-[#F7FAFC] transition text-xs">
                                    <div class="flex items-center gap-2.5">
                                        <input type="radio" name="cv_id" value="{{ $cv->id }}" {{ $cv->is_default ? 'checked' : '' }} class="text-[#2A81CB] focus:ring-[#2A81CB]">
                                        <span class="font-medium text-[#142B47]">{{ $cv->cv_title }}</span>
                                        @if($cv->is_default)
                                            <span class="px-1.5 py-0.5 rounded text-[10px] bg-[#2A81CB] text-white font-bold">Mặc định</span>
                                        @endif
                                    </div>
                                    <span class="text-gray-400 text-[11px]">{{ $cv->formatted_file_size }}</span>
                                </label>
                            @endforeach
                        </div>
                    @endif

                    <!-- Option B: Upload new CV file -->
                    <div x-show="cvOption === 'new'" class="space-y-2">
                        <div class="border-2 border-dashed border-gray-300 hover:border-[#2A81CB] rounded-2xl p-6 text-center transition bg-[#F7FAFC]">
                            <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <label class="cursor-pointer">
                                <span class="text-xs font-bold text-[#2A81CB] hover:underline">Nhấn để chọn file CV</span>
                                <span class="text-xs text-gray-500"> hoặc kéo thả vào đây</span>
                                <input type="file" name="new_cv" accept=".pdf,.doc,.docx" class="hidden" onchange="document.getElementById('fileNamePreview').innerText = this.files[0] ? this.files[0].name : ''">
                            </label>
                            <p class="text-[11px] text-gray-400 mt-1">Định dạng hỗ trợ: PDF, DOC, DOCX (Dung lượng tối đa 5MB)</p>
                            <p id="fileNamePreview" class="text-xs font-semibold text-[#2A81CB] mt-2"></p>
                        </div>
                    </div>
                </div>

                <!-- Cover Letter -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#142B47] mb-1">
                        Thư giới thiệu / Lời nhắn cho nhà tuyển dụng
                    </label>
                    <textarea name="cover_letter" 
                              rows="4" 
                              placeholder="Trình bày ngắn gọn về lý do ứng tuyển, điểm mạnh nổi bật hoặc lời chào gửi đến nhà tuyển dụng..." 
                              class="w-full p-3 bg-[#F7FAFC] border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-[#2A81CB] focus:border-transparent text-[#142B47]"></textarea>
                </div>

                <!-- Duplicate Warning Notice -->
                <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-[11px] text-amber-800 flex items-start gap-2">
                    <svg class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    <span>Lưu ý: Mỗi ứng viên chỉ được nộp đơn 01 lần cho mỗi vị trí tuyển dụng. Vui lòng kiểm tra kỹ CV trước khi bấm gửi.</span>
                </div>

                <!-- Modal Actions -->
                <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                    <button type="button" @click="showApplyModal = false" class="px-5 py-2.5 rounded-xl border border-gray-200 text-xs font-semibold text-gray-600 hover:bg-gray-100 transition">
                        Hủy bỏ
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#2A81CB] hover:bg-[#1F6BB0] text-white text-xs font-bold transition shadow-md flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span>Xác nhận nộp hồ sơ</span>
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
