<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\CandidateProfile;
use App\Models\Company;
use App\Models\Cv;
use App\Models\Job;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tạo tài khoản Employer & Công ty
        $employersData = [
            [
                'name' => 'FPT Software HR',
                'email' => 'hr@fpt-software.com',
                'company_name' => 'FPT Software',
                'industry' => 'Công nghệ thông tin',
                'address' => 'Tòa nhà FPT, Phố Duy Tân, Cầu Giấy, Hà Nội',
                'company_size' => '1000+ nhân viên',
                'website' => 'https://fptsoftware.com',
                'description' => 'FPT Software là công ty phần mềm hàng đầu tại Việt Nam và khu vực châu Á - Thái Bình Dương, cung cấp dịch vụ công nghệ toàn diện cho khách hàng toàn cầu.',
                'verification_status' => 'verified',
            ],
            [
                'name' => 'VNG Recruitment',
                'email' => 'tuyendung@vng.com.vn',
                'company_name' => 'VNG Corporation',
                'industry' => 'Công nghệ & Game',
                'address' => 'VNG Campus, Khu chế xuất Tân Thuận, Quận 7, TP. Hồ Chí Minh',
                'company_size' => '500 - 1000 nhân viên',
                'website' => 'https://vng.com.vn',
                'description' => 'VNG là kỳ lân công nghệ đầu tiên tại Việt Nam, sở hữu hệ sinh thái sản phẩm gồm Zalo, ZaloPay, Zing MP3 và các tựa game nổi tiếng.',
                'verification_status' => 'verified',
            ],
            [
                'name' => 'Viettel Telecom HR',
                'email' => 'recruitment@viettel.com.vn',
                'company_name' => 'Tập đoàn Công nghiệp - Viễn thông Quân đội (Viettel)',
                'industry' => 'Viễn thông & Dịch vụ số',
                'address' => 'Số 1 Trần Hữu Dực, Nam Từ Liêm, Hà Nội',
                'company_size' => '5000+ nhân viên',
                'website' => 'https://viettel.vn',
                'description' => 'Tập đoàn viễn thông và công nghệ hàng đầu Việt Nam, kiến tạo xã hội số với các giải pháp thông minh.',
                'verification_status' => 'verified',
            ],
            [
                'name' => 'Shopee Vietnam Talent',
                'email' => 'careers@shopee.vn',
                'company_name' => 'Shopee Việt Nam',
                'industry' => 'Thương mại điện tử',
                'address' => 'Saigon Centre, Quận 1, TP. Hồ Chí Minh',
                'company_size' => '1000 - 3000 nhân viên',
                'website' => 'https://shopee.vn',
                'description' => 'Shopee là sàn thương mại điện tử hàng đầu tại Đông Nam Á và Đài Loan.',
                'verification_status' => 'verified',
            ],
            [
                'name' => 'Techcombank HR',
                'email' => 'tuyendung@techcombank.com.vn',
                'company_name' => 'Ngân hàng TMCP Kỹ thương Việt Nam (Techcombank)',
                'industry' => 'Tài chính - Ngân hàng',
                'address' => '6 Quang Trung, Hoàn Kiếm, Hà Nội',
                'company_size' => '5000+ nhân viên',
                'website' => 'https://techcombank.com',
                'description' => 'Ngân hàng TMCP Kỹ Thương Việt Nam là một trong những ngân hàng thương mại cổ phần lớn nhất Việt Nam.',
                'verification_status' => 'verified',
            ]
        ];

        $companies = [];
        foreach ($employersData as $empData) {
            $user = User::firstOrCreate(
                ['email' => $empData['email']],
                [
                    'name' => $empData['name'],
                    'password' => Hash::make('password'),
                    'role' => 'employer',
                    'account_status' => 'active',
                ]
            );

            $company = Company::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'company_name' => $empData['company_name'],
                    'industry' => $empData['industry'],
                    'address' => $empData['address'],
                    'company_size' => $empData['company_size'],
                    'website' => $empData['website'],
                    'description' => $empData['description'],
                    'verification_status' => $empData['verification_status'],
                ]
            );

            $companies[] = $company;
        }

        // 2. Tạo danh sách Việc làm mẫu (Jobs) cho UC1
        $jobsData = [
            [
                'company_index' => 0,
                'title' => 'Lập trình viên Backend (PHP/Laravel) - Fresher/Junior',
                'category' => 'Công nghệ thông tin',
                'job_level' => 'Fresher',
                'employment_type' => 'Full-time',
                'quantity' => 5,
                'deadline' => now()->addDays(20)->toDateString(),
                'salary_min' => 12000000,
                'salary_max' => 18000000,
                'currency' => 'VND',
                'location' => 'Hà Nội',
                'description' => "Tham gia phát triển các dự án Web Enterprise cho khách hàng quốc tế.\nXây dựng và tối ưu hóa RESTful API, Database schema trên nền tảng Laravel.\nPhối hợp cùng team Frontend và QA để đảm bảo chất lượng phần mềm.",
                'requirements' => "Nắm chắc kiến thức OOP, PHP cơ bản và nâng cao.\nCó kiến thức hoặc từng làm quen với Laravel framework, MySQL.\nBiết sử dụng Git cơ bản.\nTinh thần học hỏi cao, có trách nhiệm với công việc.",
                'benefits' => "Mức lương hấp dẫn từ 12 - 18 triệu theo năng lực.\nĐược đào tạo bài bản từ các Senior Tech Lead.\nThưởng dự án, thưởng tháng 13, xét tăng lương 2 lần/năm.\nBảo hiểm sức khỏe FPT Care cho bản thân và người thân.",
                'skills' => 'PHP, Laravel, MySQL, Git, RESTful API',
                'status' => 'approved',
            ],
            [
                'company_index' => 0,
                'title' => 'Senior Fullstack Developer (Laravel & ReactJS)',
                'category' => 'Công nghệ thông tin',
                'job_level' => 'Senior',
                'employment_type' => 'Hybrid',
                'quantity' => 2,
                'deadline' => now()->addDays(25)->toDateString(),
                'salary_min' => 28000000,
                'salary_max' => 45000000,
                'currency' => 'VND',
                'location' => 'Hà Nội',
                'description' => "Thiết kế kiến trúc hệ thống backend chịu tải cao trên nền tảng PHP Laravel và ReactJS.\nReview code, định hướng kỹ thuật cho các thành viên trong team.\nTối ưu hiệu năng cơ sở dữ liệu và bảo mật hệ thống.",
                'requirements' => "Từ 4 năm kinh nghiệm phát triển web với PHP/Laravel và ReactJS.\nThành thạo Docker, Redis, CI/CD, Microservices.\nKhả năng đọc hiểu tài liệu tiếng Anh tốt.",
                'benefits' => "Mức lương lên tới 45 triệu VND + Thưởng kết quả kinh doanh.\nLàm việc Hybrid (2 ngày WFH/tuần).\nGói bảo hiểm quốc tế cao cấp.",
                'skills' => 'Laravel, ReactJS, Docker, Redis, Microservices, CI/CD',
                'status' => 'approved',
            ],
            [
                'company_index' => 1,
                'title' => 'Chuyên viên UI/UX Designer',
                'category' => 'Thiết kế đồ họa / UI-UX',
                'job_level' => 'Middle',
                'employment_type' => 'Full-time',
                'quantity' => 2,
                'deadline' => now()->addDays(15)->toDateString(),
                'salary_min' => 18000000,
                'salary_max' => 28000000,
                'currency' => 'VND',
                'location' => 'TP. Hồ Chí Minh',
                'description' => "Nghiên cứu hành vi người dùng (User Research) và xây dựng Wireframe, Prototype.\nThiết kế giao diện ứng dụng di động và web theo chuẩn Design System.\nPhối hợp cùng Product Owner và Developer để hiện thực hóa trải nghiệm người dùng.",
                'requirements' => "Từ 2 năm kinh nghiệm thiết kế UI/UX trên Figma, Adobe XD.\nCó Portfolio sản phẩm thực tế đã phát hành.\nHiểu biết về Design System, Usability Testing.",
                'benefits' => "Lương thỏa thuận 18 - 28 triệu tùy năng lực.\nMôi trường VNG Campus hiện đại bậc nhất, miễn phí gym, hồ bơi, cafe.\nThưởng KPI và cổ phiếu ESOP theo thâm niên.",
                'skills' => 'Figma, UI/UX, Design System, Prototyping, Wireframing',
                'status' => 'approved',
            ],
            [
                'company_index' => 1,
                'title' => 'Thực tập sinh Marketing / Content Creator (Intern)',
                'category' => 'Marketing & Truyền thông',
                'job_level' => 'Intern',
                'employment_type' => 'Internship',
                'quantity' => 3,
                'deadline' => now()->addDays(10)->toDateString(),
                'salary_min' => 5000000,
                'salary_max' => 8000000,
                'currency' => 'VND',
                'location' => 'TP. Hồ Chí Minh',
                'description' => "Sáng tạo nội dung trên các kênh Social Media (TikTok, Facebook, LinkedIn).\nHỗ trợ tổ chức các sự kiện tuyển dụng và chiến dịch truyền thông thương hiệu.\nPhân tích số liệu tương tác và đề xuất ý tưởng chiến dịch mới.",
                'requirements' => "Sinh viên năm 3, năm 4 chuyên ngành Marketing, Báo chí, Truyền thông.\nNăng động, bắt trend nhanh, có kỹ năng viết tốt.\nBiết sử dụng Canva hoặc Photoshop/CapCut cơ bản là lợi thế lớn.",
                'benefits' => "Trợ cấp thực tập 5 - 8 triệu/tháng + Dấu mộc thực tập.\nCơ hội trở thành nhân viên chính thức sau 3 tháng.\nĐược đào tạo thực chiến từ các chuyên gia Marketing.",
                'skills' => 'Content Marketing, Copywriting, Social Media, Canva, CapCut',
                'status' => 'approved',
            ],
            [
                'company_index' => 2,
                'title' => 'Kỹ sư Quản trị Hệ thống & DevOps Engineer',
                'category' => 'Công nghệ thông tin',
                'job_level' => 'Middle',
                'employment_type' => 'Full-time',
                'quantity' => 2,
                'deadline' => now()->addDays(18)->toDateString(),
                'salary_min' => 22000000,
                'salary_max' => 35000000,
                'currency' => 'VND',
                'location' => 'Hà Nội',
                'description' => "Vận hành hệ thống Cloud Infrastructure quy mô lớn.\nXây dựng và quản lý pipeline CI/CD, Kubernetes cluster.\nGiám sát và ứng cứu sự cố đảm bảo High Availability 99.99%.",
                'requirements' => "Từ 2 năm kinh nghiệm vị trí DevOps/System Admin.\nThành thạo Linux, Docker, Kubernetes, Ansible, Terraform.\nKinh nghiệm làm việc với hệ thống viễn thông hoặc ngân hàng là lợi thế.",
                'benefits' => "Mức thu nhập 22 - 35 triệu/tháng.\nChế độ phụ cấp công tác, ăn trưa, điện thoại.\nKhám sức khỏe định kỳ tiêu chuẩn quốc tế.",
                'skills' => 'DevOps, Linux, Kubernetes, Docker, CI/CD, Terraform',
                'status' => 'approved',
            ],
            [
                'company_index' => 3,
                'title' => 'Chuyên viên Phân tích Dữ liệu Kinh doanh (Data Analyst)',
                'category' => 'Phân tích dữ liệu',
                'job_level' => 'Junior',
                'employment_type' => 'Remote',
                'quantity' => 2,
                'deadline' => now()->addDays(30)->toDateString(),
                'salary_min' => 15000000,
                'salary_max' => 25000000,
                'currency' => 'VND',
                'location' => 'Đà Nẵng',
                'description' => "Thu thập, làm sạch và phân tích dữ liệu bán hàng và hành vi khách hàng.\nXây dựng Dashboard báo cáo tự động trên PowerBI/Tableau.\nĐưa ra khuyến nghị dựa trên dữ liệu giúp tối ưu chiến lược kinh doanh.",
                'requirements' => "Thành thạo SQL và một công cụ trực quan hóa (PowerBI, Tableau).\nBiết lập trình phân tích dữ liệu với Python (Pandas, Numpy) là điểm cộng.\nTư duy phân tích sắc bén, kỹ năng giải quyết vấn đề tốt.",
                'benefits' => "Lương cạnh tranh 15 - 25 triệu.\nChế độ làm việc linh hoạt từ xa (Remote).\nThưởng voucher mua sắm hàng tháng trên Shopee.",
                'skills' => 'SQL, PowerBI, Python, Data Analysis, Tableau',
                'status' => 'approved',
            ],
            [
                'company_index' => 4,
                'title' => 'Chuyên viên Kế toán Tổng hợp',
                'category' => 'Tài chính - Kế toán',
                'job_level' => 'Middle',
                'employment_type' => 'Full-time',
                'quantity' => 1,
                'deadline' => now()->addDays(14)->toDateString(),
                'salary_min' => 14000000,
                'salary_max' => 20000000,
                'currency' => 'VND',
                'location' => 'Hà Nội',
                'description' => "Kiểm tra, đối chiếu số liệu và lập báo cáo tài chính định kỳ theo chuẩn mực kế toán.\nTheo dõi công nợ, quyết toán thuế, bảo hiểm và tiền lương.\nPhối hợp với cơ quan kiểm toán và cơ quan thuế.",
                'requirements' => "Tốt nghiệp Đại học chuyên ngành Kế toán - Kiểm toán.\nTối thiểu 2 năm kinh nghiệm kế toán tổng hợp.\nSử dụng thành thạo phần mềm MISA, Excel nâng cao.",
                'benefits' => "Lương thỏa thuận 14 - 20 triệu.\nVay vốn ưu đãi dành riêng cho cán bộ nhân viên Techcombank.\nBảo hiểm sức khỏe cao cấp.",
                'skills' => 'Kế toán tổng hợp, MISA, Báo cáo tài chính, Thuế, Excel',
                'status' => 'approved',
            ],
            [
                'company_index' => 0,
                'title' => 'Lập trình viên Flutter Mobile App - Remote',
                'category' => 'Công nghệ thông tin',
                'job_level' => 'Junior',
                'employment_type' => 'Remote',
                'quantity' => 2,
                'deadline' => now()->addDays(22)->toDateString(),
                'salary_min' => 15000000,
                'salary_max' => 22000000,
                'currency' => 'VND',
                'location' => 'Toàn quốc',
                'description' => "Phát triển ứng dụng di động đa nền tảng iOS & Android bằng Flutter.\nTích hợp REST API, Firebase và Third-party SDKs.\nTối ưu hiệu năng, UI animation mượt mà.",
                'requirements' => "Tối thiểu 1 năm kinh nghiệm phát triển Flutter.\nNắm vững State Management (Bloc, Provider hoặc Riverpod).\nĐã có app xuất bản trên App Store / Google Play là lợi thế.",
                'benefits' => "100% Remote, tự do lựa chọn địa điểm làm việc.\nLương 15 - 22 triệu + Thưởng dự án.",
                'skills' => 'Flutter, Dart, Firebase, RESTful API, State Management',
                'status' => 'approved',
            ],
            [
                'company_index' => 3,
                'title' => 'Trưởng phòng Kinh doanh Thương mại Điện tử (Sales Manager)',
                'category' => 'Kinh doanh & Bán hàng',
                'job_level' => 'Manager',
                'employment_type' => 'Full-time',
                'quantity' => 1,
                'deadline' => now()->addDays(12)->toDateString(),
                'salary_min' => 35000000,
                'salary_max' => 60000000,
                'currency' => 'VND',
                'location' => 'TP. Hồ Chí Minh',
                'description' => "Xây dựng và dẫn dắt đội ngũ sales mở rộng tập khách hàng doanh nghiệp trên nền tảng.\nHoạch định chiến lược kinh doanh và đảm bảo đạt chỉ tiêu doanh số năm.",
                'requirements' => "Tối thiểu 3 năm kinh nghiệm quản lý sales trong ngành E-commerce hoặc FMCG.\nKỹ năng đàm phán, chốt sales và xây dựng mối quan hệ xuất sắc.",
                'benefits' => "Lương cứng 35 - 60 triệu + Thưởng hoa hồng doanh số cực cao.\nChế độ phụ cấp xe hơi, du lịch hàng năm.",
                'skills' => 'Sales B2B, Quản lý đội ngũ, Đàm phán, E-commerce',
                'status' => 'approved',
            ]
        ];

        $createdJobs = [];
        foreach ($jobsData as $j) {
            $comp = $companies[$j['company_index']];
            $job = Job::create([
                'company_id' => $comp->id,
                'title' => $j['title'],
                'category' => $j['category'],
                'job_level' => $j['job_level'],
                'employment_type' => $j['employment_type'],
                'quantity' => $j['quantity'],
                'deadline' => $j['deadline'],
                'salary_min' => $j['salary_min'],
                'salary_max' => $j['salary_max'],
                'currency' => $j['currency'],
                'location' => $j['location'],
                'description' => $j['description'],
                'requirements' => $j['requirements'],
                'benefits' => $j['benefits'],
                'skills' => $j['skills'],
                'status' => $j['status'],
            ]);
            $createdJobs[] = $job;
        }

        // 3. Tạo tài khoản Ứng viên mẫu cho Đoàn (Candidate: doan@example.com / password)
        $candidateUser = User::firstOrCreate(
            ['email' => 'doan@example.com'],
            [
                'name' => 'Nguyễn Văn Đoàn',
                'phone' => '0987654321',
                'password' => Hash::make('password'),
                'role' => 'candidate',
                'account_status' => 'active',
            ]
        );

        // Tạo hồ sơ ứng viên (CandidateProfile)
        $candidateProfile = CandidateProfile::firstOrCreate(
            ['user_id' => $candidateUser->id],
            [
                'career_summary' => 'Sinh viên năm cuối ngành Công nghệ Thông tin tại Trường Đại học Công nghệ Đông Á (EAUT). Đam mê phát triển ứng dụng Web với Laravel, PHP và kiến trúc phần mềm sạch.',
                'skills' => 'PHP, Laravel, MySQL, Tailwind CSS, Alpine.js, RESTful API, Git',
                'experience' => 'Dự án: EAUT Job Recruitment Platform (CVMOI) - Đảm nhiệm vai trò Fullstack Developer phụ trách chức năng Tìm kiếm việc làm (UC1), Nộp đơn ứng tuyển (UC2) và Quản lý hồ sơ/CV cá nhân (UC9).',
                'education' => 'Trường Đại học Công nghệ Đông Á (EAUT) - Chuyên ngành Công nghệ Thông tin (2022 - 2026). Điểm GPA: 3.5/4.0.',
                'is_searchable' => true,
                'completion_score' => 85,
            ]
        );

        // Đảm bảo thư mục lưu CV tồn tại
        if (!Storage::disk('public')->exists('cvs')) {
            Storage::disk('public')->makeDirectory('cvs');
        }

        // Tạo 2 tệp CV mẫu
        $dummyCvContent1 = "HỒ SƠ ỨNG VIÊN - NGUYỄN VĂN ĐOÀN\nVị trí: Fullstack PHP/Laravel Developer\nEmail: doan@example.com\nSĐT: 0987654321\nKỹ năng: PHP, Laravel, Tailwind CSS, MySQL, Git";
        $dummyCvContent2 = "RESUME - DOAN NGUYEN VAN\nPosition: Software Engineer\nUniversity: East Asia University of Technology (EAUT)\nGPA: 3.5/4.0";

        $cvPath1 = 'cvs/CV_NguyenVanDoan_PHP_Laravel.txt';
        $cvPath2 = 'cvs/CV_Doan_Software_Engineer_English.txt';

        Storage::disk('public')->put($cvPath1, $dummyCvContent1);
        Storage::disk('public')->put($cvPath2, $dummyCvContent2);

        // Tạo 2 bản ghi CV trong bảng cvs
        $cv1 = Cv::firstOrCreate(
            ['candidate_profile_id' => $candidateProfile->id, 'cv_title' => 'CV Lập trình viên PHP/Laravel'],
            [
                'cv_url' => 'storage/' . $cvPath1,
                'file_size' => strlen($dummyCvContent1),
                'is_default' => true,
            ]
        );

        $cv2 = Cv::firstOrCreate(
            ['candidate_profile_id' => $candidateProfile->id, 'cv_title' => 'CV Software Engineer (English)'],
            [
                'cv_url' => 'storage/' . $cvPath2,
                'file_size' => strlen($dummyCvContent2),
                'is_default' => false,
            ]
        );

        // Tính lại điểm hoàn thiện chuẩn
        $candidateProfile->recalculateCompletionScore();

        // 4. Tạo 1 đơn ứng tuyển mẫu sẵn để test chống nộp trùng (UC2)
        if (!empty($createdJobs)) {
            $sampleJob = $createdJobs[0]; // Vị trí Lập trình viên Backend PHP/Laravel
            Application::firstOrCreate(
                [
                    'job_id' => $sampleJob->id,
                    'candidate_id' => $candidateUser->id,
                ],
                [
                    'cv_id' => $cv1->id,
                    'cv_url' => $cv1->cv_url,
                    'cover_letter' => 'Em rất hứng thú với môi trường làm việc chuyên nghiệp tại FPT Software và tin rằng kỹ năng Laravel của mình có thể đóng góp hiệu quả vào dự án.',
                    'status' => 'pending',
                ]
            );

            // Tạo thông báo mẫu
            Notification::firstOrCreate(
                [
                    'user_id' => $candidateUser->id,
                    'title' => 'Ứng tuyển thành công: ' . $sampleJob->title,
                ],
                [
                    'message' => "Bạn đã nộp đơn ứng tuyển thành công vào vị trí '{$sampleJob->title}' tại {$sampleJob->company->company_name}.",
                    'is_read' => false,
                ]
            );
        }
    }
}
