@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{ statusFilter: 'all' }">
    
    <!-- Page Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-[#142B47]">Việc Làm Đã Ứng Tuyển</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">
                Theo dõi tiến độ và phản hồi từ các nhà tuyển dụng cho các vị trí bạn đã nộp hồ sơ.
            </p>
        </div>

        <a href="{{ route('jobs.index') }}" class="px-5 py-2.5 bg-[#2A81CB] hover:bg-[#1F6BB0] text-white rounded-xl text-xs font-bold transition shadow-md flex items-center gap-2 w-fit">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <span>Tìm thêm việc làm</span>
        </a>
    </div>

    <!-- Status Filters Bar -->
    <div class="flex items-center gap-2 overflow-x-auto pb-4 mb-6 text-xs font-semibold">
        <button @click="statusFilter = 'all'" 
                :class="statusFilter === 'all' ? 'bg-[#2A81CB] text-white shadow-sm' : 'bg-white text-gray-600 hover:bg-[#EAF2FA] border border-gray-200'"
                class="px-4 py-2 rounded-xl transition shrink-0">
            Tất cả đơn ({{ $applications->total() }})
        </button>
        <button @click="statusFilter = 'pending'" 
                :class="statusFilter === 'pending' ? 'bg-[#2A81CB] text-white shadow-sm' : 'bg-white text-gray-600 hover:bg-[#EAF2FA] border border-gray-200'"
                class="px-4 py-2 rounded-xl transition shrink-0">
            Chờ xét duyệt
        </button>
        <button @click="statusFilter = 'reviewing'" 
                :class="statusFilter === 'reviewing' ? 'bg-[#2A81CB] text-white shadow-sm' : 'bg-white text-gray-600 hover:bg-[#EAF2FA] border border-gray-200'"
                class="px-4 py-2 rounded-xl transition shrink-0">
            Đang xem xét
        </button>
        <button @click="statusFilter = 'shortlisted'" 
                :class="statusFilter === 'shortlisted' ? 'bg-[#2A81CB] text-white shadow-sm' : 'bg-white text-gray-600 hover:bg-[#EAF2FA] border border-gray-200'"
                class="px-4 py-2 rounded-xl transition shrink-0">
            Phù hợp
        </button>
        <button @click="statusFilter = 'hired'" 
                :class="statusFilter === 'hired' ? 'bg-[#2A81CB] text-white shadow-sm' : 'bg-white text-gray-600 hover:bg-[#EAF2FA] border border-gray-200'"
                class="px-4 py-2 rounded-xl transition shrink-0">
            Đã trúng tuyển
        </button>
        <button @click="statusFilter = 'rejected'" 
                :class="statusFilter === 'rejected' ? 'bg-[#2A81CB] text-white shadow-sm' : 'bg-white text-gray-600 hover:bg-[#EAF2FA] border border-gray-200'"
                class="px-4 py-2 rounded-xl transition shrink-0">
            Đã từ chối
        </button>
    </div>

    <!-- Applications List -->
    <div class="space-y-4">
        @forelse($applications as $app)
            <div x-show="statusFilter === 'all' || statusFilter === '{{ $app->status }}'" 
                 class="bg-white p-5 sm:p-6 rounded-3xl shadow-sm border border-gray-100 hover:border-[#2A81CB]/30 transition space-y-4">
                
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    
                    <!-- Left: Logo & Job Info -->
                    <div class="flex items-start gap-4 flex-1">
                        <div class="w-14 h-14 rounded-2xl bg-[#EAF2FA] text-[#2A81CB] font-bold text-lg flex items-center justify-center shrink-0 border border-[#2A81CB]/20 overflow-hidden">
                            @if($app->job && $app->job->company && $app->job->company->logo_url)
                                <img src="{{ asset($app->job->company->logo_url) }}" alt="{{ $app->job->company->company_name }}" class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(substr($app->job->company->company_name ?? 'C', 0, 1)) }}
                            @endif
                        </div>

                        <div class="space-y-1">
                            <h3 class="font-bold text-base text-[#142B47] hover:text-[#2A81CB] transition">
                                <a href="{{ route('jobs.show', $app->job) }}">{{ $app->job->title }}</a>
                            </h3>

                            <div class="flex items-center gap-2 text-xs text-gray-600 flex-wrap">
                                <span class="font-medium text-[#142B47]">{{ $app->job->company->company_name ?? 'Doanh nghiệp tuyển dụng' }}</span>
                                <span>•</span>
                                <span>{{ $app->job->location }}</span>
                                <span>•</span>
                                <span class="text-[#2A81CB] font-semibold">{{ $app->job->formatted_salary }}</span>
                            </div>

                            <div class="text-[11px] text-gray-400 pt-1">
                                Đã nộp vào: {{ $app->created_at->format('d/m/Y H:i') }} ({{ $app->created_at->diffForHumans() }})
                            </div>
                        </div>
                    </div>

                    <!-- Right: Status Badge & JD Link -->
                    <div class="flex sm:flex-col items-center sm:items-end justify-between w-full sm:w-auto gap-3 pt-3 sm:pt-0 border-t sm:border-t-0 border-gray-100">
                        @php
                            $statusConfig = [
                                'pending' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'label' => 'Chờ xét duyệt'],
                                'reviewing' => ['bg' => 'bg-blue-50', 'text' => 'text-[#2A81CB]', 'border' => 'border-[#2A81CB]/30', 'label' => 'Đang xem xét'],
                                'shortlisted' => ['bg' => 'bg-purple-50', 'text' => 'text-purple-700', 'border' => 'border-purple-200', 'label' => 'Phù hợp hồ sơ'],
                                'hired' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'label' => 'Đã trúng tuyển'],
                                'rejected' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'border' => 'border-rose-200', 'label' => 'Đã từ chối'],
                            ];
                            $badge = $statusConfig[$app->status] ?? ['bg' => 'bg-gray-50', 'text' => 'text-gray-700', 'border' => 'border-gray-200', 'label' => $app->status];
                        @endphp

                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $badge['bg'] }} {{ $badge['text'] }} {{ $badge['border'] }}">
                            {{ $badge['label'] }}
                        </span>

                        <a href="{{ route('jobs.show', $app->job) }}" class="text-xs font-semibold text-[#2A81CB] hover:underline flex items-center gap-1">
                            <span>Xem lại tin tuyển dụng</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        </a>
                    </div>

                </div>

                <!-- Attached CV & Cover letter detail snippet -->
                <div class="pt-3 border-t border-gray-100 text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-gray-500">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#2A81CB]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        <span>CV đã nộp: 
                            <a href="{{ asset($app->cv_url) }}" target="_blank" class="font-semibold text-[#2A81CB] hover:underline">
                                {{ $app->cv->cv_title ?? 'File CV ứng tuyển' }}
                            </a>
                        </span>
                    </div>

                    @if($app->cover_letter)
                        <div class="italic text-gray-600 line-clamp-1 max-w-md">
                            "{{ $app->cover_letter }}"
                        </div>
                    @endif
                </div>

            </div>
        @empty
            <div class="bg-white p-12 rounded-3xl shadow-sm border border-gray-100 text-center">
                <div class="w-16 h-16 bg-[#EAF2FA] text-[#2A81CB] rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-[#142B47] mb-2">Bạn chưa ứng tuyển công việc nào</h3>
                <p class="text-xs text-gray-500 max-w-md mx-auto mb-6">
                    Hãy tìm kiếm và nộp đơn vào những công việc phù hợp với kỹ năng và mục tiêu nghề nghiệp của bạn.
                </p>
                <a href="{{ route('jobs.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-[#2A81CB] text-white rounded-xl text-xs font-bold hover:bg-[#1F6BB0] transition shadow-md">
                    Khám phá việc làm ngay
                </a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $applications->links() }}
    </div>

</div>
@endsection
