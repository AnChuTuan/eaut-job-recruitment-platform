<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
{
    /**
     * UC1: Search Job - Danh sách việc làm, tìm kiếm, lọc, phân trang.
     */
    public function index(Request $request)
    {
        $query = Job::with('company')->active();

        // 1. Tìm theo từ khóa (Tiêu đề, Kỹ năng, Tên công ty, Mô tả)
        if ($request->filled('keyword')) {
            $keyword = trim($request->keyword);
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('skills', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%")
                  ->orWhereHas('company', function ($compQ) use ($keyword) {
                      $compQ->where('company_name', 'like', "%{$keyword}%");
                  });
            });
        }

        // 2. Lọc theo Địa điểm
        if ($request->filled('location')) {
            $location = trim($request->location);
            $query->where('location', 'like', "%{$location}%");
        }

        // 3. Lọc theo Ngành nghề / Danh mục
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // 4. Lọc theo Cấp bậc (Intern, Fresher, Junior, Middle, Senior, Leader, Manager)
        if ($request->filled('job_level')) {
            $query->where('job_level', $request->job_level);
        }

        // 5. Lọc theo Hình thức làm việc (Full-time, Part-time, Remote, Hybrid, Internship)
        if ($request->filled('employment_type')) {
            $query->where('employment_type', $request->employment_type);
        }

        // 6. Lọc theo Mức lương
        if ($request->filled('salary_range')) {
            switch ($request->salary_range) {
                case 'under_10':
                    $query->where(function ($q) {
                        $q->where('salary_max', '<=', 10000000)
                          ->orWhere(function ($sub) {
                              $sub->whereNull('salary_max')
                                  ->where('salary_min', '<', 10000000);
                          });
                    });
                    break;
                case '10_20':
                    $query->where(function ($q) {
                        $q->whereBetween('salary_min', [10000000, 20000000])
                          ->orWhereBetween('salary_max', [10000000, 20000000]);
                    });
                    break;
                case '20_30':
                    $query->where(function ($q) {
                        $q->whereBetween('salary_min', [20000000, 30000000])
                          ->orWhereBetween('salary_max', [20000000, 30000000]);
                    });
                    break;
                case 'over_30':
                    $query->where(function ($q) {
                        $q->where('salary_min', '>=', 30000000)
                          ->orWhere('salary_max', '>=', 30000000);
                    });
                    break;
                case 'negotiable':
                    $query->whereNull('salary_min')->whereNull('salary_max');
                    break;
            }
        }

        // 7. Sắp xếp (Sorting)
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'salary_desc':
                $query->orderByRaw('COALESCE(salary_max, salary_min, 0) DESC');
                break;
            case 'deadline':
                $query->orderBy('deadline', 'asc');
                break;
            case 'latest':
            default:
                $query->latest('id');
                break;
        }

        // Phân trang bảo toàn query parameters
        $jobs = $query->paginate(9)->withQueryString();

        // Dữ liệu cho các bộ lọc
        $categories = Job::approved()->distinct()->pluck('category')->filter()->values();
        $locations = Job::approved()->distinct()->pluck('location')->filter()->values();
        $employmentTypes = ['Full-time', 'Part-time', 'Remote', 'Hybrid', 'Internship'];
        $jobLevels = ['Intern', 'Fresher', 'Junior', 'Middle', 'Senior', 'Leader', 'Manager'];

        $totalActiveJobs = Job::active()->count();

        return view('jobs.index', compact(
            'jobs',
            'categories',
            'locations',
            'employmentTypes',
            'jobLevels',
            'totalActiveJobs'
        ));
    }

    /**
     * UC1: Xem chi tiết công việc (Job Description - JD).
     */
    public function show(Job $job)
    {
        $job->load('company');

        // Kiểm tra xem ứng viên đã đăng nhập và đã ứng tuyển chưa (UC2)
        $hasApplied = false;
        $userApplication = null;
        $userCvs = collect();

        if (Auth::check()) {
            $user = Auth::user();
            $userApplication = Application::where('job_id', $job->id)
                ->where('candidate_id', $user->id)
                ->first();

            $hasApplied = !is_null($userApplication);

            if ($user->candidateProfile) {
                $userCvs = $user->candidateProfile->cvs;
            }
        }

        // Việc làm liên quan (Cùng ngành nghề hoặc cùng công ty)
        $relatedJobs = Job::with('company')
            ->active()
            ->where('id', '!=', $job->id)
            ->where(function ($q) use ($job) {
                $q->where('category', $job->category)
                  ->orWhere('company_id', $job->company_id);
            })
            ->take(4)
            ->get();

        return view('jobs.show', compact(
            'job',
            'hasApplied',
            'userApplication',
            'userCvs',
            'relatedJobs'
        ));
    }
}
