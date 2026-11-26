<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\AuditLog;
use App\Models\CandidateProfile;
use App\Models\Company;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin mặc định
        $admin = User::firstOrCreate(
            ['email' => 'admin@eaut.edu.vn'],
            [
                'name' => 'Nguyên Admin',
                'phone' => '0988888888',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'account_status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // 2. Nhà tuyển dụng (Employers) & Doanh nghiệp
        $employer1 = User::firstOrCreate(
            ['email' => 'hr@fpt.com'],
            [
                'name' => 'FPT Software HR',
                'phone' => '02473007300',
                'password' => Hash::make('password'),
                'role' => 'employer',
                'account_status' => 'active',
                'email_verified_at' => now(),
                'created_at' => now()->subMonths(3),
            ]
        );

        $company1 = Company::firstOrCreate(
            ['user_id' => $employer1->id],
            [
                'company_name' => 'FPT Software',
                'logo_url' => null,
                'tax_code' => '0101778163',
                'address' => 'Tòa nhà FPT, Cầu Giấy, Hà Nội',
                'email' => 'contact@fpt.com',
                'phone' => '02473007300',
                'website' => 'https://fpt-software.com',
                'industry' => 'Công nghệ thông tin',
                'company_size' => '1000+ nhân viên',
                'description' => 'Tập đoàn công nghệ hàng đầu Việt Nam.',
                'verification_status' => 'verified',
            ]
        );

        $employer2 = User::firstOrCreate(
            ['email' => 'hr@viettelpost.vn'],
            [
                'name' => 'Viettel Post HR',
                'phone' => '02462660000',
                'password' => Hash::make('password'),
                'role' => 'employer',
                'account_status' => 'active',
                'email_verified_at' => now(),
                'created_at' => now()->subMonths(2),
            ]
        );

        $company2 = Company::firstOrCreate(
            ['user_id' => $employer2->id],
            [
                'company_name' => 'Tổng công ty CP Bưu chính Viettel',
                'tax_code' => '0104066063',
                'address' => 'Nam Từ Liêm, Hà Nội',
                'email' => 'tuyendung@viettelpost.vn',
                'phone' => '02462660000',
                'website' => 'https://viettelpost.com.vn',
                'industry' => 'Logistics & Vận chuyển',
                'company_size' => '500-1000 nhân viên',
                'description' => 'Dịch vụ chuyển phát hàng đầu...',
                'verification_status' => 'pending',
            ]
        );

        $employer3 = User::firstOrCreate(
            ['email' => 'hr@vinsmart.vn'],
            [
                'name' => 'Vingroup Retail HR',
                'phone' => '02439749999',
                'password' => Hash::make('password'),
                'role' => 'employer',
                'account_status' => 'active',
                'email_verified_at' => now(),
                'created_at' => now()->subMonth(),
            ]
        );

        $company3 = Company::firstOrCreate(
            ['user_id' => $employer3->id],
            [
                'company_name' => 'Vingroup Corporation',
                'tax_code' => '0101245486',
                'address' => 'Long Biên, Hà Nội',
                'email' => 'hr@vingroup.net',
                'phone' => '02439749999',
                'website' => 'https://vingroup.net',
                'industry' => 'Kinh doanh & Thương mại',
                'company_size' => '1000+ nhân viên',
                'description' => 'Tập đoàn đa ngành lớn nhất...',
                'verification_status' => 'verified',
            ]
        );

        // 3. Tin tuyển dụng đa dạng các ngành nghề & trạng thái (Approved / Pending)
        $jobs = [
            [
                'company_id' => $company1->id,
                'title' => 'Lập trình viên Laravel / PHP (Fresher/Junior)',
                'category' => 'Công nghệ thông tin',
                'job_level' => 'Fresher',
                'employment_type' => 'Full-time',
                'quantity' => 3,
                'deadline' => now()->addDays(25),
                'salary_min' => 10000000,
                'salary_max' => 18000000,
                'currency' => 'VND',
                'location' => 'Hà Nội',
                'description' => 'Phát triển backend web application với Laravel Framework.',
                'requirements' => 'Nắm chắc kiến thức OOP, PHP, hiểu về MVC và Git.',
                'status' => 'approved',
                'created_at' => now()->subMonths(2),
            ],
            [
                'company_id' => $company1->id,
                'title' => 'Kỹ sư AI / Machine Learning Intern',
                'category' => 'Công nghệ thông tin',
                'job_level' => 'Intern',
                'employment_type' => 'Part-time',
                'quantity' => 2,
                'deadline' => now()->addDays(40),
                'salary_min' => 6000000,
                'salary_max' => 10000000,
                'currency' => 'VND',
                'location' => 'Hà Nội',
                'description' => 'Nghiên cứu ứng dụng các mô hình học máy và LLM.',
                'requirements' => 'Biết Python, PyTorch hoặc TensorFlow cơ bản.',
                'status' => 'approved',
                'created_at' => now()->subMonth(),
            ],
            [
                'company_id' => $company2->id,
                'title' => 'Chuyên viên Điều phối Vận tải Logistics',
                'category' => 'Logistics & Vận chuyển',
                'job_level' => 'Junior',
                'employment_type' => 'Full-time',
                'quantity' => 4,
                'deadline' => now()->addDays(15),
                'salary_min' => 9000000,
                'salary_max' => 14000000,
                'currency' => 'VND',
                'location' => 'Hà Nội',
                'description' => 'Quản lý tuyến vận chuyển và điều phối đơn hàng bưu chính.',
                'requirements' => 'Tốt nghiệp chuyên ngành Logistics, Quản trị chuỗi cung ứng.',
                'status' => 'approved',
                'created_at' => now()->subDays(10),
            ],
            [
                'company_id' => $company3->id,
                'title' => 'Chuyên viên Digital Marketing & SEO (Chờ duyệt)',
                'category' => 'Marketing & Truyền thông',
                'job_level' => 'Junior',
                'employment_type' => 'Full-time',
                'quantity' => 2,
                'deadline' => now()->addDays(30),
                'salary_min' => 12000000,
                'salary_max' => 18000000,
                'currency' => 'VND',
                'location' => 'Hà Nội',
                'description' => 'Triển khai chiến dịch quảng cáo và SEO cho hệ thống bán lẻ.',
                'requirements' => 'Kinh nghiệm 1 năm chạy Google Ads, Meta Ads.',
                'status' => 'pending', // Chờ duyệt
                'created_at' => now()->subDays(3),
            ],
            [
                'company_id' => $company3->id,
                'title' => 'Thực tập sinh Kinh doanh / Bán hàng (B2B Sales Intern)',
                'category' => 'Kinh doanh & Bán hàng',
                'job_level' => 'Intern',
                'employment_type' => 'Full-time',
                'quantity' => 5,
                'deadline' => now()->addDays(20),
                'salary_min' => 5000000,
                'salary_max' => 8000000,
                'currency' => 'VND',
                'location' => 'Hà Nội',
                'description' => 'Tìm kiếm khách hàng doanh nghiệp và chăm sóc đối tác.',
                'requirements' => 'Giao tiếp tốt, năng động, ưu tiên sinh viên khối kinh tế.',
                'status' => 'pending', // Chờ duyệt
                'created_at' => now()->subDay(),
            ],
            [
                'company_id' => $company2->id,
                'title' => 'Nhân viên Kế toán nội bộ & Thuế',
                'category' => 'Tài chính & Kế toán',
                'job_level' => 'Junior',
                'employment_type' => 'Full-time',
                'quantity' => 1,
                'deadline' => now()->addDays(35),
                'salary_min' => 10000000,
                'salary_max' => 15000000,
                'currency' => 'VND',
                'location' => 'Hà Nội',
                'description' => 'Theo dõi sổ sách kế toán và lập báo cáo tài chính hàng tháng.',
                'requirements' => 'Tốt nghiệp chuyên ngành Kế toán - Kiểm toán.',
                'status' => 'approved',
                'created_at' => now()->subDays(5),
            ],
        ];

        foreach ($jobs as $jobData) {
            Job::firstOrCreate(
                [
                    'company_id' => $jobData['company_id'],
                    'title' => $jobData['title'],
                ],
                $jobData
            );
        }

        // 4. Ứng viên (Candidates)
        $cand1 = User::firstOrCreate(
            ['email' => 'nguyenvana@eaut.edu.vn'],
            [
                'name' => 'Nguyễn Văn An',
                'phone' => '0912345678',
                'password' => Hash::make('password'),
                'role' => 'candidate',
                'account_status' => 'active',
                'email_verified_at' => now(),
                'created_at' => now()->subMonths(3),
            ]
        );

        CandidateProfile::firstOrCreate(
            ['user_id' => $cand1->id],
            [
                'career_summary' => 'Sinh viên K12 khoa CNTT - Đại học Công nghệ Đông Á.',
                'skills' => 'PHP, Laravel, MySQL, HTML, CSS, JavaScript',
                'experience' => 'Thực tập sinh Backend 3 tháng tại công ty ABC.',
                'education' => 'Đại học Công nghệ Đông Á (EAUT) - Chuyên ngành Kỹ thuật Phần mềm.',
                'completion_score' => 85,
                'is_searchable' => true,
            ]
        );

        $cand2 = User::firstOrCreate(
            ['email' => 'tranthib@eaut.edu.vn'],
            [
                'name' => 'Trần Thị Bình',
                'phone' => '0923456789',
                'password' => Hash::make('password'),
                'role' => 'candidate',
                'account_status' => 'active',
                'email_verified_at' => now(),
                'created_at' => now()->subMonths(2),
            ]
        );

        CandidateProfile::firstOrCreate(
            ['user_id' => $cand2->id],
            [
                'career_summary' => 'Sinh viên khoa Quản trị Kinh doanh EAUT.',
                'skills' => 'Content Marketing, SEO, Kỹ năng giao tiếp',
                'education' => 'Đại học Công nghệ Đông Á (EAUT)',
                'completion_score' => 60,
                'is_searchable' => true,
            ]
        );

        $cand3 = User::firstOrCreate(
            ['email' => 'hoangvanh@eaut.edu.vn'],
            [
                'name' => 'Hoàng Văn Hùng',
                'phone' => '0933333333',
                'password' => Hash::make('password'),
                'role' => 'candidate',
                'account_status' => 'active',
                'email_verified_at' => now(),
                'created_at' => now()->subMonth(),
            ]
        );

        CandidateProfile::firstOrCreate(
            ['user_id' => $cand3->id],
            [
                'career_summary' => 'Sinh viên khoa Điện - Cơ điện tử EAUT.',
                'skills' => 'AutoCAD, PLC, Lập trình C',
                'education' => 'Đại học Công nghệ Đông Á (EAUT)',
                'completion_score' => 70,
                'is_searchable' => true,
            ]
        );

        // Nộp đơn ứng tuyển mẫu (Applications)
        $firstJob = Job::where('status', 'approved')->first();
        if ($firstJob) {
            Application::firstOrCreate(
                [
                    'job_id' => $firstJob->id,
                    'candidate_id' => $cand1->id,
                ],
                [
                    'cv_url' => 'cvs/sample-cv-an.pdf',
                    'cover_letter' => 'Em rất mong muốn được ứng tuyển vị trí này.',
                    'status' => 'pending',
                ]
            );

            Application::firstOrCreate(
                [
                    'job_id' => $firstJob->id,
                    'candidate_id' => $cand2->id,
                ],
                [
                    'cv_url' => 'cvs/sample-cv-binh.pdf',
                    'cover_letter' => 'Em có định hướng phát triển sự nghiệp trong ngành.',
                    'status' => 'reviewing',
                ]
            );
        }

        // 5. Tài khoản vi phạm & Audit logs
        $candSuspended = User::firstOrCreate(
            ['email' => 'lequangc@example.com'],
            [
                'name' => 'Lê Quang Cường (Tạm khóa)',
                'phone' => '0934567890',
                'password' => Hash::make('password'),
                'role' => 'candidate',
                'account_status' => 'suspended',
                'email_verified_at' => now(),
            ]
        );

        AuditLog::firstOrCreate(
            [
                'admin_id' => $admin->id,
                'target_type' => 'User',
                'target_id' => $candSuspended->id,
                'action' => 'suspend',
            ],
            [
                'reason' => 'Spam nộp hồ sơ hàng loạt trong thời gian ngắn không phù hợp.',
                'created_at' => now()->subDays(2),
            ]
        );

        $candBanned = User::firstOrCreate(
            ['email' => 'phamthid@example.com'],
            [
                'name' => 'Phạm Thị Dung (Đã cấm vĩnh viễn)',
                'phone' => '0945678901',
                'password' => Hash::make('password'),
                'role' => 'candidate',
                'account_status' => 'banned',
                'email_verified_at' => now(),
            ]
        );

        AuditLog::firstOrCreate(
            [
                'admin_id' => $admin->id,
                'target_type' => 'User',
                'target_id' => $candBanned->id,
                'action' => 'ban',
            ],
            [
                'reason' => 'Tải lên tài liệu chứng chỉ giả mạo và thông tin cá nhân sai sự thật.',
                'created_at' => now()->subDay(),
            ]
        );
    }
}
