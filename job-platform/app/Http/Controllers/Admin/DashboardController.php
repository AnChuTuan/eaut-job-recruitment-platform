<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\AuditLog;
use App\Models\Company;
use App\Models\Job;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Màn hình tổng quan thống kê hệ thống (Admin Dashboard).
     * Cấu trúc tuân thủ chuẩn theo Sơ đồ cây yêu cầu:
     * 1. Chỉ số tổng quan: Candidate, Employer, Job Post hoạt động, Job Post chờ duyệt, Tổng lượt Application
     * 2. Biểu đồ xu hướng: Tăng trưởng người dùng, Số lượng tin tuyển dụng theo thời gian, Tỷ lệ phân bố ngành nghề
     */
    public function index(): View
    {
        // ----------------------------------------------------
        // NHÁNH 1: XEM CHỈ SỐ TỔNG QUAN
        // ----------------------------------------------------
        // 1. Xem tổng số Candidate
        $totalCandidates = User::where('role', 'candidate')->count();

        // 2. Xem tổng số Employer
        $totalEmployers = User::where('role', 'employer')->count();

        // 3. Xem số lượng Job Post đang hoạt động (Đã duyệt & Chưa hết hạn)
        $activeJobsCount = Job::where('status', 'approved')
            ->where('deadline', '>=', now()->toDateString())
            ->count();

        // 4. Xem số lượng Job Post đang chờ duyệt (Pending)
        $pendingJobsCount = Job::where('status', 'pending')->count();

        // 5. Xem tổng số lượt Application
        $totalApplications = Application::count();

        $metrics = [
            'total_candidates' => $totalCandidates,
            'total_employers' => $totalEmployers,
            'active_jobs' => $activeJobsCount,
            'pending_jobs' => $pendingJobsCount,
            'total_applications' => $totalApplications,

            // Chỉ số phụ trợ
            'total_users' => User::count(),
            'total_companies' => Company::count(),
            'pending_companies' => Company::where('verification_status', 'pending')->count(),
            'total_jobs' => Job::count(),
        ];

        // ----------------------------------------------------
        // NHÁNH 2: XEM BIỂU ĐỒ XU HƯỚNG
        // ----------------------------------------------------
        // Biểu đồ 1: Tăng trưởng người dùng theo 6 tháng gần nhất (Candidate vs Employer)
        // Biểu đồ 2: Số lượng tin tuyển dụng theo thời gian (6 tháng gần nhất)
        $months = [];
        $candidateGrowth = [];
        $employerGrowth = [];
        $jobTrends = [];

        for ($i = 5; $i >= 0; $i--) {
            $targetDate = now()->subMonths($i);
            $monthLabel = 'Tháng ' . $targetDate->format('m/Y');
            $months[] = $monthLabel;

            $candCount = User::where('role', 'candidate')
                ->whereYear('created_at', $targetDate->year)
                ->whereMonth('created_at', $targetDate->month)
                ->count();
            $candidateGrowth[] = $candCount;

            $empCount = User::where('role', 'employer')
                ->whereYear('created_at', $targetDate->year)
                ->whereMonth('created_at', $targetDate->month)
                ->count();
            $employerGrowth[] = $empCount;

            $jobCount = Job::whereYear('created_at', $targetDate->year)
                ->whereMonth('created_at', $targetDate->month)
                ->count();
            $jobTrends[] = $jobCount;
        }

        // Biểu đồ 3: Tỷ lệ phân bố ngành nghề (Category Distribution)
        $categoryData = Job::select('category', DB::raw('count(*) as count'))
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->groupBy('category')
            ->orderByDesc('count')
            ->get();

        $categoryLabels = $categoryData->pluck('category')->toArray();
        $categoryCounts = $categoryData->pluck('count')->toArray();

        if (empty($categoryLabels)) {
            $categoryLabels = ['Công nghệ thông tin', 'Marketing', 'Kinh doanh', 'Logistics', 'Kế toán'];
            $categoryCounts = [5, 3, 2, 2, 1];
        }

        $chartData = [
            'months' => $months,
            'candidate_growth' => $candidateGrowth,
            'employer_growth' => $employerGrowth,
            'job_trends' => $jobTrends,
            'category_labels' => $categoryLabels,
            'category_counts' => $categoryCounts,
        ];

        // Hoạt động gần đây (Audit Logs) & Người dùng mới nhất
        $recentAuditLogs = AuditLog::with('admin')->latest('id')->take(6)->get();
        $latestUsers = User::latest('id')->take(5)->get();

        return view('admin.dashboard', compact('metrics', 'chartData', 'recentAuditLogs', 'latestUsers'));
    }
}
