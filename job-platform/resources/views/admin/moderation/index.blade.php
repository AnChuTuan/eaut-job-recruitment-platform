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
                        <span>ANTDD Admin Moderation</span>
                        <span>•</span>
                        <span class="text-[#142B47]">UC8</span>
                    </div>
                    <h1 class="text-2xl font-extrabold text-[#142B47]">Kiểm duyệt tin tuyển dụng</h1>
                    <p class="text-sm text-slate-600 mt-1">
                        Xem xét nội dung tin tuyển dụng từ nhà tuyển dụng, duyệt, yêu cầu chỉnh sửa, từ chối hoặc khóa tin lừa đảo.
                    </p>
                </div>

                @if ($statusFilter === 'pending')
                    <div class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#FFFFFF] border border-[#E1EEFA] text-xs font-bold text-[#142B47] shadow-sm">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#2A81CB]"></span>
                        <span>Hàng đợi FIFO: Tin gửi trước lên trước</span>
                    </div>
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

            {{-- Filter Tabs & Search --}}
            @php
                $tabs = [
                    'pending' => ['label' => 'Chờ duyệt (FIFO)', 'count' => $statusCounts['pending']],
                    'approved' => ['label' => 'Đã duyệt', 'count' => $statusCounts['approved']],
                    'needs_revision' => ['label' => 'Cần chỉnh sửa', 'count' => $statusCounts['needs_revision']],
                    'rejected' => ['label' => 'Từ chối', 'count' => $statusCounts['rejected']],
                    'banned' => ['label' => 'Đã khóa (Ban)', 'count' => $statusCounts['banned']],
                    'all' => ['label' => 'Tất cả', 'count' => $statusCounts['all']],
                ];
            @endphp

            <div class="bg-[#FFFFFF] rounded-2xl border border-[#E1EEFA] p-4 shadow-sm space-y-4">
                <div class="flex flex-wrap items-center gap-2">
                    @foreach ($tabs as $key => $tab)
                        @php
                            $isActive = $statusFilter === $key;
                        @endphp
                        <a href="{{ route('admin.moderation.index', array_filter(['status' => $key, 'search' => $search ?: null])) }}"
                            class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold transition {{ $isActive ? 'bg-[#2A81CB] text-white shadow-sm' : 'bg-[#EAF2FA] text-[#142B47] hover:bg-[#E6F0FA]' }}">
                            <span>{{ $tab['label'] }}</span>
                            <span class="px-1.5 py-0.5 rounded-full text-[11px] {{ $isActive ? 'bg-white/20 text-white' : 'bg-white text-[#2A81CB]' }}">
                                {{ $tab['count'] }}
                            </span>
                        </a>
                    @endforeach
                </div>

                <form method="GET" action="{{ route('admin.moderation.index') }}" class="flex flex-col sm:flex-row gap-3">
                    <input type="hidden" name="status" value="{{ $statusFilter }}">
                    <input type="text" name="search" value="{{ $search }}"
                        placeholder="Tìm theo tiêu đề tin, tên công ty, ngành nghề, địa điểm..."
                        class="flex-1 rounded-lg border-slate-300 bg-[#F7FAFC] text-sm text-[#142B47] focus:border-[#2A81CB] focus:ring-[#2A81CB]">
                    <button type="submit"
                        class="px-5 py-2 rounded-lg bg-[#142B47] hover:bg-[#2A81CB] text-white text-sm font-semibold transition">
                        Tìm kiếm
                    </button>
                    @if ($search !== '' || $statusFilter !== 'pending')
                        <a href="{{ route('admin.moderation.index') }}"
                            class="px-4 py-2 rounded-lg border border-slate-300 bg-white hover:bg-[#E6F0FA] text-sm font-semibold text-[#142B47] text-center transition">
                            Mặc định (Chờ duyệt)
                        </a>
                    @endif
                </form>
            </div>

            {{-- Moderation Queue Table --}}
            <div class="bg-[#FFFFFF] rounded-2xl border border-[#E1EEFA] shadow-sm overflow-hidden">
                @if ($jobs->isEmpty())
                    <div class="p-12 text-center">
                        <p class="text-base font-bold text-[#142B47]">Không có tin tuyển dụng nào trong mục này</p>
                        <p class="text-sm text-slate-500 mt-1">
                            @if ($statusFilter === 'pending')
                                Hàng đợi kiểm duyệt hiện đang trống. Mọi tin tuyển dụng đã được xử lý!
                            @else
                                Thử thay đổi bộ lọc trạng thái hoặc từ khóa tìm kiếm.
                            @endif
                        </p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-[#E1EEFA] text-sm">
                            <thead class="bg-[#EAF2FA] text-[#142B47]">
                                <tr>
                                    <th class="px-4 py-3.5 text-left font-bold w-16">
                                        {{ $statusFilter === 'pending' ? 'FIFO' : 'ID' }}
                                    </th>
                                    <th class="px-5 py-3.5 text-left font-bold">Tin tuyển dụng</th>
                                    <th class="px-4 py-3.5 text-left font-bold">Công ty</th>
                                    <th class="px-4 py-3.5 text-left font-bold">Thời gian gửi</th>
                                    <th class="px-4 py-3.5 text-left font-bold">Trạng thái</th>
                                    <th class="px-5 py-3.5 text-right font-bold">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($jobs as $index => $job)
                                    @php
                                        $fifoNumber = ($jobs->currentPage() - 1) * $jobs->perPage() + $index + 1;
                                        $company = $job->company;
                                    @endphp
                                    <tr class="hover:bg-[#F7FAFC] transition">
                                        <td class="px-4 py-4 font-bold text-[#2A81CB] whitespace-nowrap">
                                            @if ($statusFilter === 'pending')
                                                #{{ $fifoNumber }}
                                            @else
                                                #{{ $job->id }}
                                            @endif
                                        </td>
                                        <td class="px-5 py-4">
                                            <a href="{{ route('admin.moderation.show', $job) }}"
                                                class="font-bold text-[#142B47] hover:text-[#2A81CB] transition">
                                                {{ $job->title }}
                                            </a>
                                            <div class="text-xs text-slate-500 mt-0.5 flex flex-wrap gap-x-3 gap-y-1">
                                                <span>{{ $job->category }}</span>
                                                <span>• {{ $job->location }}</span>
                                                <span>• SL: {{ $job->quantity }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="font-semibold text-[#142B47]">
                                                {{ $company?->company_name ?? 'Chưa rõ công ty' }}
                                            </div>
                                            @if ($company)
                                                <div class="text-[11px] mt-0.5">
                                                    @if ($company->verification_status === 'verified')
                                                        <span class="text-emerald-700 font-semibold">Đã xác minh</span>
                                                    @elseif ($company->verification_status === 'rejected')
                                                        <span class="text-rose-600 font-semibold">Xác minh bị từ chối</span>
                                                    @else
                                                        <span class="text-amber-600 font-semibold">Chưa xác minh</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 text-xs text-slate-600 whitespace-nowrap">
                                            <div class="font-semibold text-[#142B47]">
                                                {{ $job->updated_at?->format('d/m/Y H:i') }}
                                            </div>
                                            <div class="text-slate-400">
                                                Tạo: {{ $job->created_at?->format('d/m/Y H:i') }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            @include('admin.moderation._status_badge', ['status' => $job->status])
                                        </td>
                                        <td class="px-5 py-4 text-right whitespace-nowrap">
                                            <a href="{{ route('admin.moderation.show', $job) }}"
                                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-[#2A81CB] hover:bg-[#142B47] text-white text-xs font-semibold transition">
                                                <span>Xem & Kiểm duyệt</span>
                                            </a>
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
        </div>
    </div>
</x-app-layout>

