<x-app-layout>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        .antdd-font { font-family: 'Montserrat', sans-serif; }
    </style>

    <div class="antdd-font min-h-screen bg-[#F7FAFC] py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Preview Top Bar --}}
            <div class="bg-[#142B47] text-white rounded-2xl p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 shadow-sm">
                <div>
                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider bg-[#2A81CB] text-white mb-1.5">
                        Chế độ xem trước • ANTDD
                    </span>
                    <p class="text-sm text-slate-200">
                        Đây là giao diện hiển thị nội dung tin tuyển dụng trước khi gửi Admin duyệt.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2.5">
                    <a href="{{ route('employer.jobs.index') }}"
                        class="px-3.5 py-2 rounded-lg text-xs font-semibold bg-white/10 hover:bg-white/20 text-white transition">
                        Danh sách tin
                    </a>
                    @if ($job->status !== 'banned')
                        <a href="{{ route('employer.jobs.edit', $job) }}"
                            class="px-3.5 py-2 rounded-lg text-xs font-semibold bg-white text-[#142B47] hover:bg-[#E6F0FA] transition">
                            Chỉnh sửa
                        </a>
                    @endif
                    @if (in_array($job->status, ['draft', 'needs_revision', 'rejected'], true))
                        <form method="POST" action="{{ route('employer.jobs.submit', $job) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                class="px-4 py-2 rounded-lg text-xs font-semibold bg-[#2A81CB] hover:bg-[#2A81CB]/90 text-white shadow transition">
                                Gửi duyệt ngay
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            @if (session('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Job Header Card --}}
            <div class="bg-[#FFFFFF] rounded-2xl border border-[#E1EEFA] p-6 sm:p-8 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                    <div>
                        <div class="flex flex-wrap items-center gap-2.5 mb-2">
                            @include('employer.jobs._status_badge', ['status' => $job->status])
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-[#EAF2FA] text-[#2A81CB]">
                                {{ $job->category }}
                            </span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#142B47]">{{ $job->title }}</h1>
                        <p class="text-sm font-semibold text-[#2A81CB] mt-1">{{ $company->company_name }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-6 pt-6 border-t border-[#E1EEFA]">
                    <div class="bg-[#EAF2FA] rounded-xl p-3.5">
                        <div class="text-xs font-medium text-slate-600">Mức lương</div>
                        <div class="text-sm font-bold text-[#142B47] mt-0.5">
                            @if ($job->salary_min && $job->salary_max)
                                {{ number_format((float) $job->salary_min) }} - {{ number_format((float) $job->salary_max) }} {{ $job->currency }}
                            @elseif ($job->salary_min)
                                Từ {{ number_format((float) $job->salary_min) }} {{ $job->currency }}
                            @elseif ($job->salary_max)
                                Đến {{ number_format((float) $job->salary_max) }} {{ $job->currency }}
                            @else
                                Thỏa thuận
                            @endif
                        </div>
                    </div>

                    <div class="bg-[#EAF2FA] rounded-xl p-3.5">
                        <div class="text-xs font-medium text-slate-600">Địa điểm</div>
                        <div class="text-sm font-bold text-[#142B47] mt-0.5">{{ $job->location }}</div>
                    </div>

                    <div class="bg-[#EAF2FA] rounded-xl p-3.5">
                        <div class="text-xs font-medium text-slate-600">Hình thức / Cấp bậc</div>
                        <div class="text-sm font-bold text-[#142B47] mt-0.5">
                            {{ $job->employment_type ?: 'Chưa cập nhật' }}
                            @if ($job->job_level)
                                • {{ $job->job_level }}
                            @endif
                        </div>
                    </div>

                    <div class="bg-[#EAF2FA] rounded-xl p-3.5">
                        <div class="text-xs font-medium text-slate-600">Số lượng • Hạn nộp</div>
                        <div class="text-sm font-bold text-[#142B47] mt-0.5">
                            {{ $job->quantity }} người • {{ \Illuminate\Support\Carbon::parse($job->deadline)->format('d/m/Y') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Job Details --}}
            <div class="bg-[#FFFFFF] rounded-2xl border border-[#E1EEFA] p-6 sm:p-8 shadow-sm space-y-6">
                <div>
                    <h2 class="text-lg font-bold text-[#142B47] mb-2">Mô tả công việc</h2>
                    <div class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">{{ $job->description }}</div>
                </div>

                @if ($job->requirements)
                    <div class="pt-5 border-t border-slate-100">
                        <h2 class="text-lg font-bold text-[#142B47] mb-2">Yêu cầu ứng viên</h2>
                        <div class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">{{ $job->requirements }}</div>
                    </div>
                @endif

                @if ($job->benefits)
                    <div class="pt-5 border-t border-slate-100">
                        <h2 class="text-lg font-bold text-[#142B47] mb-2">Quyền lợi & Phúc lợi</h2>
                        <div class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">{{ $job->benefits }}</div>
                    </div>
                @endif

                @if ($job->skills)
                    <div class="pt-5 border-t border-slate-100">
                        <h2 class="text-lg font-bold text-[#142B47] mb-3">Kỹ năng</h2>
                        <div class="flex flex-wrap gap-2">
                            @foreach (array_filter(array_map('trim', explode(',', $job->skills))) as $skill)
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-[#E6F0FA] text-[#142B47]">
                                    {{ $skill }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

