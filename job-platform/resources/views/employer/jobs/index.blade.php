<x-app-layout>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        .antdd-font { font-family: 'Montserrat', sans-serif; }
    </style>

    <div class="antdd-font min-h-screen bg-[#F7FAFC] py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Header Banner --}}
            <div class="bg-[#EAF2FA] border border-[#E1EEFA] rounded-2xl p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-[#2A81CB] mb-1">
                        <span>ANTDD Employer Portal</span>
                        @if ($company)
                            <span>•</span>
                            <span class="text-[#142B47]">{{ $company->company_name }}</span>
                        @endif
                    </div>
                    <h1 class="text-2xl font-extrabold text-[#142B47]">Quản lý tin tuyển dụng</h1>
                    <p class="text-sm text-slate-600 mt-1">
                        Tạo mới, lưu nháp, xem trước và gửi tin tuyển dụng lên hệ thống ANTDD để Admin kiểm duyệt.
                    </p>
                </div>

                @if ($company)
                    <a href="{{ route('employer.jobs.create') }}"
                        class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-[#2A81CB] hover:bg-[#142B47] text-white text-sm font-semibold shadow-sm transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Đăng tin mới
                    </a>
                @endif
            </div>

            {{-- Flash messages --}}
            @if (session('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800">
                    {{ session('error') }}
                </div>
            @endif

            @if (! $company)
                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-[#142B47]">
                    <h2 class="text-base font-bold mb-1">Chưa có hồ sơ công ty</h2>
                    <p class="text-sm text-slate-700">
                        Tài khoản nhà tuyển dụng của bạn chưa liên kết với hồ sơ công ty nào trong bảng <code>companies</code>. Vui lòng hoàn thiện hồ sơ công ty trước khi đăng tin tuyển dụng.
                    </p>
                </div>
            @else
                {{-- Status Tabs --}}
                @php
                    $tabs = [
                        '' => ['label' => 'Tất cả', 'count' => $statusCounts['all']],
                        'draft' => ['label' => 'Bản nháp', 'count' => $statusCounts['draft']],
                        'pending' => ['label' => 'Chờ duyệt', 'count' => $statusCounts['pending']],
                        'approved' => ['label' => 'Đã duyệt', 'count' => $statusCounts['approved']],
                        'needs_revision' => ['label' => 'Cần sửa', 'count' => $statusCounts['needs_revision']],
                        'rejected' => ['label' => 'Từ chối', 'count' => $statusCounts['rejected']],
                        'banned' => ['label' => 'Bị khóa', 'count' => $statusCounts['banned']],
                    ];
                @endphp

                <div class="bg-[#FFFFFF] rounded-2xl border border-[#E1EEFA] p-4 shadow-sm space-y-4">
                    <div class="flex flex-wrap items-center gap-2">
                        @foreach ($tabs as $key => $tab)
                            @php
                                $isActive = ($statusFilter ?? '') === $key;
                            @endphp
                            <a href="{{ route('employer.jobs.index', array_filter(['status' => $key ?: null, 'search' => $search ?: null])) }}"
                                class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold transition {{ $isActive ? 'bg-[#2A81CB] text-white' : 'bg-[#EAF2FA] text-[#142B47] hover:bg-[#E6F0FA]' }}">
                                <span>{{ $tab['label'] }}</span>
                                <span class="px-1.5 py-0.5 rounded-full text-[11px] {{ $isActive ? 'bg-white/20 text-white' : 'bg-white text-[#2A81CB]' }}">
                                    {{ $tab['count'] }}
                                </span>
                            </a>
                        @endforeach
                    </div>

                    <form method="GET" action="{{ route('employer.jobs.index') }}" class="flex flex-col sm:flex-row gap-3">
                        @if ($statusFilter)
                            <input type="hidden" name="status" value="{{ $statusFilter }}">
                        @endif
                        <input type="text" name="search" value="{{ $search }}"
                            placeholder="Tìm theo tiêu đề, ngành nghề, địa điểm..."
                            class="flex-1 rounded-lg border-slate-300 bg-[#F7FAFC] text-sm text-[#142B47] focus:border-[#2A81CB] focus:ring-[#2A81CB]">
                        <button type="submit"
                            class="px-4 py-2 rounded-lg bg-[#142B47] hover:bg-[#2A81CB] text-white text-sm font-semibold transition">
                            Tìm kiếm
                        </button>
                        @if ($search !== '' || $statusFilter)
                            <a href="{{ route('employer.jobs.index') }}"
                                class="px-4 py-2 rounded-lg border border-slate-300 bg-white hover:bg-[#E6F0FA] text-sm font-semibold text-[#142B47] text-center transition">
                                Xóa lọc
                            </a>
                        @endif
                    </form>
                </div>

                {{-- Jobs Table --}}
                <div class="bg-[#FFFFFF] rounded-2xl border border-[#E1EEFA] shadow-sm overflow-hidden">
                    @if ($jobs->isEmpty())
                        <div class="p-12 text-center">
                            <p class="text-base font-semibold text-[#142B47]">Chưa có tin tuyển dụng nào</p>
                            <p class="text-sm text-slate-500 mt-1 mb-5">
                                Bắt đầu tạo tin tuyển dụng đầu tiên để thu hút ứng viên trên ANTDD.
                            </p>
                            <a href="{{ route('employer.jobs.create') }}"
                                class="inline-flex items-center px-4 py-2.5 rounded-xl bg-[#2A81CB] hover:bg-[#142B47] text-white text-sm font-semibold transition">
                                + Tạo tin tuyển dụng
                            </a>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-[#E1EEFA] text-sm">
                                <thead class="bg-[#EAF2FA] text-[#142B47]">
                                    <tr>
                                        <th class="px-5 py-3.5 text-left font-bold">Tin tuyển dụng</th>
                                        <th class="px-4 py-3.5 text-left font-bold">Mức lương</th>
                                        <th class="px-4 py-3.5 text-left font-bold">Hạn nộp</th>
                                        <th class="px-4 py-3.5 text-left font-bold">Trạng thái</th>
                                        <th class="px-5 py-3.5 text-right font-bold">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($jobs as $job)
                                        <tr class="hover:bg-[#F7FAFC] transition">
                                            <td class="px-5 py-4">
                                                <div class="font-bold text-[#142B47]">{{ $job->title }}</div>
                                                <div class="text-xs text-slate-500 mt-0.5 flex flex-wrap gap-x-3 gap-y-1">
                                                    <span>{{ $job->category }}</span>
                                                    <span>• {{ $job->location }}</span>
                                                    <span>• SL: {{ $job->quantity }}</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-4 text-slate-700 font-medium whitespace-nowrap">
                                                @if ($job->salary_min && $job->salary_max)
                                                    {{ number_format((float) $job->salary_min) }} - {{ number_format((float) $job->salary_max) }} {{ $job->currency }}
                                                @elseif ($job->salary_min)
                                                    Từ {{ number_format((float) $job->salary_min) }} {{ $job->currency }}
                                                @elseif ($job->salary_max)
                                                    Đến {{ number_format((float) $job->salary_max) }} {{ $job->currency }}
                                                @else
                                                    Thỏa thuận
                                                @endif
                                            </td>
                                            <td class="px-4 py-4 text-slate-600 whitespace-nowrap">
                                                {{ \Illuminate\Support\Carbon::parse($job->deadline)->format('d/m/Y') }}
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                @include('employer.jobs._status_badge', ['status' => $job->status])
                                            </td>
                                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                                <div class="inline-flex items-center gap-2">
                                                    <a href="{{ route('employer.jobs.preview', $job) }}"
                                                        class="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-[#EAF2FA] text-[#2A81CB] hover:bg-[#E6F0FA] transition">
                                                        Xem trước
                                                    </a>

                                                    @if ($job->status !== 'banned')
                                                        <a href="{{ route('employer.jobs.edit', $job) }}"
                                                            class="px-2.5 py-1.5 rounded-lg text-xs font-semibold border border-slate-300 text-[#142B47] hover:bg-[#E6F0FA] transition">
                                                            Sửa
                                                        </a>
                                                    @endif

                                                    @if (in_array($job->status, ['draft', 'needs_revision', 'rejected'], true))
                                                        <form method="POST" action="{{ route('employer.jobs.submit', $job) }}" class="inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit"
                                                                class="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-[#2A81CB] text-white hover:bg-[#142B47] transition">
                                                                Gửi duyệt
                                                            </button>
                                                        </form>
                                                    @endif

                                                    <form method="POST" action="{{ route('employer.jobs.destroy', $job) }}"
                                                        onsubmit="return confirm('Bạn chắc chắn muốn xóa tin tuyển dụng này?');" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="px-2.5 py-1.5 rounded-lg text-xs font-semibold text-rose-600 hover:bg-rose-50 transition">
                                                            Xóa
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if ($jobs->hasPages())
                            <div class="px-5 py-4 border-t border-[#E1EEFA] bg-[#F7FAFC]">
                                {{ $jobs->links() }}
                            </div>
                        @endif
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

