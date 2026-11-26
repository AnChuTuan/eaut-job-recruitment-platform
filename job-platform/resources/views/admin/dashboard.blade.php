@extends('layouts.admin')

@section('title', 'Admin Dashboard - Thống Kê & Xu Hướng')
@section('page_title', 'Bảng điều khiển & Phân tích xu hướng')

@section('content')
<div class="space-y-8">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Bảng điều khiển Quản trị viên (Dashboard)</h1>
            <p class="text-sm text-gray-500 mt-1">Hệ thống đo lường các chỉ số cốt lõi và biểu đồ xu hướng theo thời gian thực.</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 shadow-sm transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                Quản lý người dùng
            </a>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- NHÁNH 1: XEM CHỈ SỐ TỔNG QUAN (5 CHỈ SỐ YÊU CẦU THEO SƠ ĐỒ CÂY) -->
    <!-- ============================================================== -->
    <div>
        <div class="flex items-center space-x-2 mb-4">
            <span class="w-2.5 h-6 bg-indigo-600 rounded-full"></span>
            <h2 class="text-lg font-bold text-gray-900">1. Chỉ số tổng quan hệ thống</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- 1. Tổng số Candidate -->
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Tổng số Candidate</span>
                    <div class="w-9 h-9 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center text-lg">
                        🎓
                    </div>
                </div>
                <p class="text-3xl font-extrabold text-blue-600 mt-2">{{ $metrics['total_candidates'] }}</p>
                <p class="text-xs text-gray-400 mt-1">Ứng viên đã đăng ký</p>
            </div>

            <!-- 2. Tổng số Employer -->
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Tổng số Employer</span>
                    <div class="w-9 h-9 bg-purple-50 text-purple-600 rounded-lg flex items-center justify-center text-lg">
                        🏢
                    </div>
                </div>
                <p class="text-3xl font-extrabold text-purple-600 mt-2">{{ $metrics['total_employers'] }}</p>
                <p class="text-xs text-gray-400 mt-1">Nhà tuyển dụng / Doanh nghiệp</p>
            </div>

            <!-- 3. Job Post đang hoạt động -->
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Job Post đang hoạt động</span>
                    <div class="w-9 h-9 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center text-lg">
                        💼
                    </div>
                </div>
                <p class="text-3xl font-extrabold text-emerald-600 mt-2">{{ $metrics['active_jobs'] }}</p>
                <p class="text-xs text-emerald-600 font-medium mt-1">Đã duyệt & Đang mở tuyển</p>
            </div>

            <!-- 4. Job Post đang chờ duyệt -->
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Job Post chờ duyệt</span>
                    <div class="w-9 h-9 bg-amber-50 text-amber-600 rounded-lg flex items-center justify-center text-lg">
                        ⏳
                    </div>
                </div>
                <p class="text-3xl font-extrabold text-amber-600 mt-2">{{ $metrics['pending_jobs'] }}</p>
                <p class="text-xs text-amber-600 font-medium mt-1">Cần Admin xét duyệt</p>
            </div>

            <!-- 5. Tổng số lượt Application -->
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Tổng lượt Application</span>
                    <div class="w-9 h-9 bg-rose-50 text-rose-600 rounded-lg flex items-center justify-center text-lg">
                        📄
                    </div>
                </div>
                <p class="text-3xl font-extrabold text-rose-600 mt-2">{{ $metrics['total_applications'] }}</p>
                <p class="text-xs text-gray-400 mt-1">Hồ sơ ứng tuyển đã nộp</p>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- NHÁNH 2: XEM BIỂU ĐỒ XU HƯỚNG (3 BIỂU ĐỒ YÊU CẦU THEO SƠ ĐỒ CÂY) -->
    <!-- ============================================================== -->
    <div>
        <div class="flex items-center space-x-2 mb-4">
            <span class="w-2.5 h-6 bg-emerald-600 rounded-full"></span>
            <h2 class="text-lg font-bold text-gray-900">2. Biểu đồ phân tích xu hướng</h2>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Biểu đồ 1: Tăng trưởng người dùng (Candidate vs Employer) -->
            <div class="lg:col-span-2 bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 flex items-center">
                            <span class="w-2 h-2 rounded-full bg-blue-500 mr-2"></span>
                            Biểu đồ tăng trưởng người dùng theo thời gian
                        </h3>
                        <p class="text-xs text-gray-400 mt-0.5">So sánh tốc độ gia tăng giữa Ứng viên (Candidate) và Nhà tuyển dụng (Employer) 6 tháng qua</p>
                    </div>
                </div>
                <div class="relative h-72">
                    <canvas id="userGrowthChart"></canvas>
                </div>
            </div>

            <!-- Biểu đồ 3: Tỷ lệ phân bố ngành nghề -->
            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 flex items-center">
                            <span class="w-2 h-2 rounded-full bg-amber-500 mr-2"></span>
                            Biểu đồ tỷ lệ phân bố ngành nghề
                        </h3>
                        <p class="text-xs text-gray-400 mt-0.5">Tỷ trọng các nhóm ngành có nhu cầu tuyển dụng</p>
                    </div>
                </div>
                <div class="relative h-72 flex items-center justify-center">
                    <canvas id="industryDistributionChart"></canvas>
                </div>
            </div>

            <!-- Biểu đồ 2: Số lượng tin tuyển dụng theo thời gian -->
            <div class="lg:col-span-3 bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 flex items-center">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2"></span>
                            Biểu đồ số lượng tin tuyển dụng theo thời gian
                        </h3>
                        <p class="text-xs text-gray-400 mt-0.5">Xu hướng đăng tin tuyển dụng mới của các doanh nghiệp qua từng tháng</p>
                    </div>
                </div>
                <div class="relative h-64">
                    <canvas id="jobTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- PHẦN HỖ TRỢ: NHẬT KÝ HOẠT ĐỘNG & THÀNH VIÊN MỚI -->
    <!-- ============================================================== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 pt-2">
        <!-- Recent Audit Logs -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-900 flex items-center">
                    <span class="w-2 h-2 rounded-full bg-slate-700 mr-2"></span>
                    Nhật ký kiểm duyệt của Quản trị viên (Audit Logs)
                </h3>
                <span class="text-xs text-gray-400">Ghi nhận vi phạm & phân quyền</span>
            </div>

            @if($recentAuditLogs->isEmpty())
                <div class="text-center py-8 text-gray-400 text-xs">Chưa có nhật ký hoạt động nào.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-xs divide-y divide-gray-100">
                        <thead class="bg-gray-50 text-gray-500 font-semibold uppercase">
                            <tr>
                                <th class="py-2.5 px-3">Admin</th>
                                <th class="py-2.5 px-3">Hành động</th>
                                <th class="py-2.5 px-3">Đối tượng</th>
                                <th class="py-2.5 px-3">Lý do</th>
                                <th class="py-2.5 px-3">Thời gian</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($recentAuditLogs as $log)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-2.5 px-3 font-semibold text-gray-800">{{ $log->admin?->name ?? 'Admin #'.$log->admin_id }}</td>
                                    <td class="py-2.5 px-3">
                                        @if($log->action === 'ban')
                                            <span class="px-2 py-0.5 rounded font-bold bg-rose-100 text-rose-800">Cấm (Ban)</span>
                                        @elseif($log->action === 'suspend')
                                            <span class="px-2 py-0.5 rounded font-bold bg-amber-100 text-amber-800">Tạm khóa</span>
                                        @elseif($log->action === 'active')
                                            <span class="px-2 py-0.5 rounded font-bold bg-emerald-100 text-emerald-800">Kích hoạt</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded font-bold bg-gray-100 text-gray-800">{{ ucfirst($log->action) }}</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-3 text-gray-600">{{ $log->target_type }} #{{ $log->target_id }}</td>
                                    <td class="py-2.5 px-3 text-gray-500 truncate max-w-xs" title="{{ $log->reason }}">{{ $log->reason ?? '—' }}</td>
                                    <td class="py-2.5 px-3 text-gray-400">{{ $log->created_at->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Latest Users -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-900 flex items-center">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2"></span>
                    Thành viên mới gia nhập
                </h3>
                <a href="{{ route('admin.users.index') }}" class="text-xs text-indigo-600 hover:underline">Xem tất cả</a>
            </div>

            <div class="space-y-3">
                @foreach($latestUsers as $u)
                    <div class="flex items-center justify-between p-2.5 rounded-lg bg-gray-50 hover:bg-gray-100 transition">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-xs">
                                {{ strtoupper(substr($u->name, 0, 1)) }}
                            </div>
                            <div>
                                <a href="{{ route('admin.users.show', $u) }}" class="text-xs font-semibold text-gray-900 hover:text-indigo-600 block truncate max-w-[130px]">
                                    {{ $u->name }}
                                </a>
                                <p class="text-[11px] text-gray-500 truncate max-w-[130px]">{{ $u->email }}</p>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 text-[11px] font-semibold rounded {{ $u->role === 'employer' ? 'bg-purple-100 text-purple-700' : ($u->role === 'admin' ? 'bg-rose-100 text-rose-700' : 'bg-blue-100 text-blue-700') }}">
                            {{ ucfirst($u->role) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Tích hợp Chart.js để vẽ 3 biểu đồ xu hướng -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chartData = @json($chartData);

        // ==========================================
        // 1. BIỂU ĐỒ TĂNG TRƯỞNG NGƯỜI DÙNG (Line/Bar)
        // ==========================================
        const ctxUser = document.getElementById('userGrowthChart').getContext('2d');
        new Chart(ctxUser, {
            type: 'line',
            data: {
                labels: chartData.months,
                datasets: [
                    {
                        label: 'Ứng viên (Candidate)',
                        data: chartData.candidate_growth,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.35,
                        fill: true,
                        pointBackgroundColor: '#2563eb',
                        pointRadius: 4,
                    },
                    {
                        label: 'Nhà tuyển dụng (Employer)',
                        data: chartData.employer_growth,
                        borderColor: '#8b5cf6',
                        backgroundColor: 'rgba(139, 92, 246, 0.1)',
                        tension: 0.35,
                        fill: true,
                        pointBackgroundColor: '#7c3aed',
                        pointRadius: 4,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { font: { size: 12, family: 'sans-serif' } }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, precision: 0 }
                    }
                }
            }
        });

        // ==========================================
        // 2. BIỂU ĐỒ SỐ LƯỢNG TIN TUYỂN DỤNG THEO THỜI GIAN
        // ==========================================
        const ctxJob = document.getElementById('jobTrendChart').getContext('2d');
        new Chart(ctxJob, {
            type: 'bar',
            data: {
                labels: chartData.months,
                datasets: [{
                    label: 'Số lượng tin đăng tuyển mới',
                    data: chartData.job_trends,
                    backgroundColor: 'rgba(16, 185, 129, 0.75)',
                    borderColor: '#10b981',
                    borderWidth: 1,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { font: { size: 12 } }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, precision: 0 }
                    }
                }
            }
        });

        // ==========================================
        // 3. BIỂU ĐỒ TỶ LỆ PHÂN BỐ NGÀNH NGHỀ (Doughnut)
        // ==========================================
        const ctxIndustry = document.getElementById('industryDistributionChart').getContext('2d');
        new Chart(ctxIndustry, {
            type: 'doughnut',
            data: {
                labels: chartData.category_labels,
                datasets: [{
                    data: chartData.category_counts,
                    backgroundColor: [
                        '#6366f1',
                        '#3b82f6',
                        '#10b981',
                        '#f59e0b',
                        '#ec4899',
                        '#8b5cf6',
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { font: { size: 11 }, boxWidth: 12 }
                    }
                },
                cutout: '60%',
            }
        });
    });
</script>
@endsection
