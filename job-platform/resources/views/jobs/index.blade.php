@extends('layouts.app')

@section('content')
<!-- Hero Search Banner (Section Background Pastel Blue #EAF2FA) -->
<div class="bg-[#EAF2FA] py-12 border-b border-[#2A81CB]/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-8">
            <h1 class="text-3xl sm:text-4xl font-extrabold text-[#142B47] tracking-tight">
                Tìm Kiếm Việc Làm Phù Hợp Với <span class="text-[#2A81CB]">CVMOI</span>
            </h1>
            <p class="mt-3 text-sm sm:text-base text-gray-600">
                Kết nối trực tiếp sinh viên và ứng viên tài năng với hơn <span class="font-bold text-[#2A81CB]">{{ $totalActiveJobs }}</span> cơ hội việc làm hấp dẫn.
            </p>
        </div>

        <!-- Main Search Form -->
        <form action="{{ route('jobs.index') }}" method="GET" class="bg-white p-3 sm:p-4 rounded-2xl shadow-lg border border-gray-100 max-w-5xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
                
                <!-- Keyword Input -->
                <div class="md:col-span-5 relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" 
                           name="keyword" 
                           value="{{ request('keyword') }}" 
                           placeholder="Vị trí tuyển dụng, kỹ năng, công ty..." 
                           class="w-full pl-10 pr-4 py-3 bg-[#F7FAFC] border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2A81CB] focus:border-transparent text-[#142B47]">
                </div>

                <!-- Location Filter -->
                <div class="md:col-span-3 relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <select name="location" class="w-full pl-10 pr-8 py-3 bg-[#F7FAFC] border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2A81CB] focus:border-transparent text-[#142B47]">
                        <option value="">Tất cả địa điểm</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc }}" {{ request('location') == $loc ? 'selected' : '' }}>
                                {{ $loc }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Category Filter -->
                <div class="md:col-span-2 relative">
                    <select name="category" class="w-full px-3 py-3 bg-[#F7FAFC] border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2A81CB] focus:border-transparent text-[#142B47]">
                        <option value="">Tất cả ngành nghề</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                                {{ $cat }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Search Action Button (Primary Blue #2A81CB) -->
                <div class="md:col-span-2">
                    <button type="submit" 
                            class="w-full py-3 px-6 bg-[#2A81CB] hover:bg-[#1F6BB0] text-white font-semibold rounded-xl text-sm transition shadow-md flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <span>Tìm kiếm ngay</span>
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

<!-- Main Job Listings Section -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left Sidebar: Filters -->
        <div class="lg:col-span-3 space-y-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between pb-4 border-b border-gray-100 mb-5">
                    <h3 class="font-bold text-base text-[#142B47] flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#2A81CB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Bộ lọc tìm kiếm
                    </h3>
                    @if(request()->anyFilled(['keyword', 'location', 'category', 'job_level', 'employment_type', 'salary_range', 'sort']))
                        <a href="{{ route('jobs.index') }}" class="text-xs text-[#2A81CB] hover:underline font-medium">
                            Xóa lọc
                        </a>
                    @endif
                </div>

                <form action="{{ route('jobs.index') }}" method="GET" id="filterForm">
                    <!-- Preserve search keywords -->
                    @if(request('keyword')) <input type="hidden" name="keyword" value="{{ request('keyword') }}"> @endif
                    @if(request('location')) <input type="hidden" name="location" value="{{ request('location') }}"> @endif
                    @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif

                    <!-- Filter 1: Cấp bậc (Job Level) -->
                    <div class="mb-6">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-[#142B47] mb-3">Cấp bậc</h4>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-xs text-gray-700 cursor-pointer hover:text-[#2A81CB]">
                                <input type="radio" name="job_level" value="" {{ !request('job_level') ? 'checked' : '' }} onchange="this.form.submit()" class="text-[#2A81CB] focus:ring-[#2A81CB]">
                                <span>Tất cả cấp bậc</span>
                            </label>
                            @foreach($jobLevels as $level)
                                <label class="flex items-center gap-2 text-xs text-gray-700 cursor-pointer hover:text-[#2A81CB]">
                                    <input type="radio" name="job_level" value="{{ $level }}" {{ request('job_level') == $level ? 'checked' : '' }} onchange="this.form.submit()" class="text-[#2A81CB] focus:ring-[#2A81CB]">
                                    <span>{{ $level }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Filter 2: Hình thức làm việc (Employment Type) -->
                    <div class="mb-6 pt-5 border-t border-gray-100">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-[#142B47] mb-3">Hình thức làm việc</h4>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-xs text-gray-700 cursor-pointer hover:text-[#2A81CB]">
                                <input type="radio" name="employment_type" value="" {{ !request('employment_type') ? 'checked' : '' }} onchange="this.form.submit()" class="text-[#2A81CB] focus:ring-[#2A81CB]">
                                <span>Tất cả hình thức</span>
                            </label>
                            @foreach($employmentTypes as $type)
                                <label class="flex items-center gap-2 text-xs text-gray-700 cursor-pointer hover:text-[#2A81CB]">
                                    <input type="radio" name="employment_type" value="{{ $type }}" {{ request('employment_type') == $type ? 'checked' : '' }} onchange="this.form.submit()" class="text-[#2A81CB] focus:ring-[#2A81CB]">
                                    <span>{{ $type }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Filter 3: Mức lương (Salary Range) -->
                    <div class="pt-5 border-t border-gray-100">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-[#142B47] mb-3">Mức lương</h4>
                        <div class="space-y-2">
                            @php
                                $salaryOptions = [
                                    '' => 'Tất cả mức lương',
                                    'under_10' => 'Dưới 10 triệu',
                                    '10_20' => '10 - 20 triệu',
                                    '20_30' => '20 - 30 triệu',
                                    'over_30' => 'Trên 30 triệu',
                                    'negotiable' => 'Thỏa thuận',
                                ];
                            @endphp
                            @foreach($salaryOptions as $key => $label)
                                <label class="flex items-center gap-2 text-xs text-gray-700 cursor-pointer hover:text-[#2A81CB]">
                                    <input type="radio" name="salary_range" value="{{ $key }}" {{ request('salary_range') == $key ? 'checked' : '' }} onchange="this.form.submit()" class="text-[#2A81CB] focus:ring-[#2A81CB]">
                                    <span>{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                </form>
            </div>
        </div>

        <!-- Right Content: Job Cards & Pagination -->
        <div class="lg:col-span-9 space-y-6">
            
            <!-- Top Controls: Result count & Sort -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                <div>
                    <h2 class="text-base font-bold text-[#142B47]">
                        Tìm thấy <span class="text-[#2A81CB]">{{ $jobs->total() }}</span> việc làm phù hợp
                    </h2>
                    @if(request('keyword'))
                        <p class="text-xs text-gray-500 mt-0.5">Từ khóa: "<span class="font-medium text-[#142B47]">{{ request('keyword') }}</span>"</p>
                    @endif
                </div>

                <!-- Sort select -->
                <div class="flex items-center gap-2 text-xs">
                    <span class="text-gray-500 font-medium">Sắp xếp:</span>
                    <form action="{{ route('jobs.index') }}" method="GET" class="inline">
                        @foreach(request()->except('sort') as $k => $v)
                            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                        @endforeach
                        <select name="sort" onchange="this.form.submit()" class="text-xs py-1.5 pl-3 pr-8 bg-[#F7FAFC] border border-gray-200 rounded-lg text-[#142B47] focus:ring-[#2A81CB] focus:border-transparent">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Mới nhất</option>
                            <option value="salary_desc" {{ request('sort') == 'salary_desc' ? 'selected' : '' }}>Lương cao nhất</option>
                            <option value="deadline" {{ request('sort') == 'deadline' ? 'selected' : '' }}>Sắp hết hạn</option>
                        </select>
                    </form>
                </div>
            </div>

            <!-- Job Cards Grid -->
            <div class="space-y-4">
                @forelse($jobs as $job)
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 hover:border-[#2A81CB]/40 hover:shadow-md transition-all group relative">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            
                            <!-- Left: Logo & Job Details -->
                            <div class="flex items-start gap-4 flex-1">
                                <!-- Company Logo / Avatar -->
                                <div class="w-14 h-14 rounded-xl bg-[#EAF2FA] text-[#2A81CB] font-bold text-lg flex items-center justify-center shrink-0 border border-[#2A81CB]/20 overflow-hidden">
                                    @if($job->company && $job->company->logo_url)
                                        <img src="{{ asset($job->company->logo_url) }}" alt="{{ $job->company->company_name }}" class="w-full h-full object-cover">
                                    @else
                                        {{ strtoupper(substr($job->company->company_name ?? 'C', 0, 1)) }}
                                    @endif
                                </div>

                                <!-- Information -->
                                <div class="space-y-1.5">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <a href="{{ route('jobs.show', $job) }}" class="text-base font-bold text-[#142B47] group-hover:text-[#2A81CB] transition line-clamp-1">
                                            {{ $job->title }}
                                        </a>

                                        @if($job->deadline && $job->deadline->diffInDays(now()) <= 3 && $job->deadline->isFuture())
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-600 border border-rose-200">
                                                Gấp
                                            </span>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-2 text-xs text-gray-600 flex-wrap">
                                        <span class="font-medium text-[#142B47]">{{ $job->company->company_name ?? 'Doanh nghiệp tuyển dụng' }}</span>
                                        @if($job->company && $job->company->verification_status === 'verified')
                                            <svg class="w-4 h-4 text-[#2A81CB]" fill="currentColor" viewBox="0 0 20 20" title="Doanh nghiệp đã xác thực">
                                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                        @endif
                                        <span>•</span>
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /></svg>
                                            {{ $job->location }}
                                        </span>
                                    </div>

                                    <!-- Tags: Accent Blue #E1EEFA -->
                                    <div class="flex items-center gap-2 pt-1 flex-wrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-[#E1EEFA] text-[#2A81CB]">
                                            {{ $job->formatted_salary }}
                                        </span>
                                        @if($job->category)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] bg-gray-100 text-gray-700">
                                                {{ $job->category }}
                                            </span>
                                        @endif
                                        @if($job->job_level)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] bg-gray-100 text-gray-700">
                                                {{ $job->job_level }}
                                            </span>
                                        @endif
                                        @if($job->employment_type)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] bg-gray-100 text-gray-700">
                                                {{ $job->employment_type }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Deadline & Action -->
                            <div class="flex sm:flex-col items-center sm:items-end justify-between w-full sm:w-auto gap-3 shrink-0 pt-3 sm:pt-0 border-t sm:border-t-0 border-gray-100">
                                <div class="text-right">
                                    <span class="block text-[11px] text-gray-400">Hạn nộp hồ sơ</span>
                                    <span class="block text-xs font-medium text-[#142B47]">
                                        {{ $job->deadline ? $job->deadline->format('d/m/Y') : 'Không giới hạn' }}
                                    </span>
                                </div>

                                <a href="{{ route('jobs.show', $job) }}" 
                                   class="px-4 py-2 bg-[#E1EEFA] hover:bg-[#2A81CB] text-[#2A81CB] hover:text-white font-semibold text-xs rounded-xl transition shadow-sm">
                                    Ứng tuyển ngay
                                </a>
                            </div>

                        </div>
                    </div>
                @empty
                    <div class="bg-white p-12 rounded-2xl shadow-sm border border-gray-100 text-center">
                        <div class="w-16 h-16 bg-[#EAF2FA] text-[#2A81CB] rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-[#142B47] mb-2">Không tìm thấy việc làm nào phù hợp</h3>
                        <p class="text-xs text-gray-500 max-w-md mx-auto mb-6">
                            Hãy thử tìm kiếm với các từ khóa ngắn hơn, hoặc bỏ bớt các tiêu chí lọc địa điểm, mức lương, cấp bậc.
                        </p>
                        <a href="{{ route('jobs.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#2A81CB] text-white rounded-xl text-xs font-semibold hover:bg-[#1F6BB0] transition shadow-md">
                            Xóa toàn bộ bộ lọc
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $jobs->links() }}
            </div>

        </div>

    </div>
</div>
@endsection
