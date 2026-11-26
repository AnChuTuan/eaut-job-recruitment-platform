<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\CandidateProfile;
use App\Models\Company;
use App\Models\Cv;
use App\Models\Job;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DoanCandidateFlowTest extends TestCase
{
    /**
     * UC1: Test Job Search, Filter, and Job Description Page.
     */
    public function test_can_view_jobs_list_and_search(): void
    {
        $response = $this->get('/jobs');
        $response->assertStatus(200);
        $response->assertSee('Tìm Kiếm Việc Làm');
        $response->assertSee('CVMOI');

        // Test search by keyword
        $searchResponse = $this->get('/jobs?keyword=Laravel');
        $searchResponse->assertStatus(200);

        // Test filter by location
        $locationResponse = $this->get('/jobs?location=Hà+Nội');
        $locationResponse->assertStatus(200);

        // Test view job description
        $job = Job::first();
        if ($job) {
            $jobResponse = $this->get('/jobs/' . $job->id);
            $jobResponse->assertStatus(200);
            $jobResponse->assertSee($job->title);
            $jobResponse->assertSee('Mô tả công việc');
        }
    }

    /**
     * UC2: Test Apply for Job & Duplicate Application Prevention & Notifications.
     */
    public function test_candidate_can_apply_for_job_and_duplicate_is_prevented(): void
    {
        Storage::fake('public');

        // Create employer & company
        $employer = User::create([
            'name' => 'Tech Corp Employer',
            'email' => 'employer_test_' . time() . '@example.com',
            'password' => bcrypt('password'),
            'role' => 'employer',
        ]);

        $company = Company::create([
            'user_id' => $employer->id,
            'company_name' => 'Tech Corp',
            'verification_status' => 'verified',
        ]);

        $job = Job::create([
            'company_id' => $company->id,
            'title' => 'QA Tester ' . time(),
            'category' => 'Công nghệ thông tin',
            'deadline' => now()->addDays(15),
            'location' => 'Hà Nội',
            'description' => 'Test description',
            'status' => 'approved',
        ]);

        // Create candidate
        $candidate = User::create([
            'name' => 'Candidate Tester',
            'email' => 'candidate_test_' . time() . '@example.com',
            'password' => bcrypt('password'),
            'role' => 'candidate',
        ]);

        $profile = CandidateProfile::create([
            'user_id' => $candidate->id,
            'completion_score' => 50,
        ]);

        $cvFile = UploadedFile::fake()->create('my_resume.pdf', 500, 'application/pdf');

        // 1. Submit application with new CV
        $response = $this->actingAs($candidate)->post('/jobs/' . $job->id . '/apply', [
            'cv_option' => 'new',
            'new_cv' => $cvFile,
            'cover_letter' => 'Em rất mong muốn ứng tuyển vị trí này.',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('applications', [
            'job_id' => $job->id,
            'candidate_id' => $candidate->id,
            'status' => 'pending',
        ]);

        // Verify Notifications sent to both candidate and employer
        $this->assertDatabaseHas('notifications', [
            'user_id' => $candidate->id,
        ]);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $employer->id,
        ]);

        // 2. Chống nộp trùng (Duplicate Prevention): Try applying again
        $duplicateResponse = $this->actingAs($candidate)->post('/jobs/' . $job->id . '/apply', [
            'cv_option' => 'new',
            'new_cv' => UploadedFile::fake()->create('my_resume_2.pdf', 500, 'application/pdf'),
        ]);

        $duplicateResponse->assertSessionHas('error');
    }

    /**
     * UC9: Test Candidate Profile, Completion % Score Calculation, and Max 5 CV limit.
     */
    public function test_candidate_profile_and_max_5_cv_limit(): void
    {
        Storage::fake('public');

        $candidate = User::create([
            'name' => 'Đoàn Test Profile',
            'email' => 'doan_profile_' . time() . '@example.com',
            'phone' => '0912345678',
            'password' => bcrypt('password'),
            'role' => 'candidate',
        ]);

        // 1. View profile page
        $response = $this->actingAs($candidate)->get('/candidate/profile');
        $response->assertStatus(200);
        $response->assertSee('Hồ Sơ Cá Nhân');
        $response->assertSee('Độ hoàn thiện hồ sơ');

        // 2. Update profile
        $updateResponse = $this->actingAs($candidate)->put('/candidate/profile', [
            'name' => 'Đoàn Nguyễn Updated',
            'phone' => '0988888888',
            'career_summary' => 'Lập trình viên nhiệt huyết.',
            'skills' => 'PHP, Laravel, MySQL',
            'experience' => 'Làm việc 2 năm tại dự án EAUT CVMOI',
            'education' => 'Đại học Công nghệ Đông Á',
            'is_searchable' => 1,
        ]);

        $updateResponse->assertRedirect('/candidate/profile');
        $updateResponse->assertSessionHas('success');

        $profile = $candidate->fresh()->candidateProfile;
        $this->assertGreaterThan(50, $profile->completion_score);

        // 3. Test CV Management & Max 5 CVs limit
        // Upload 5 CVs
        for ($i = 1; $i <= 5; $i++) {
            $cvFile = UploadedFile::fake()->create("cv_test_{$i}.pdf", 100, 'application/pdf');
            $uploadResponse = $this->actingAs($candidate)->post('/candidate/cvs', [
                'cv_title' => "CV Phiên bản {$i}",
                'cv_file' => $cvFile,
            ]);
            $uploadResponse->assertSessionHas('success');
        }

        $this->assertEquals(5, $profile->cvs()->count());

        // 4. Try uploading 6th CV -> Must be BLOCKED with error message
        $sixthCvFile = UploadedFile::fake()->create("cv_test_6.pdf", 100, 'application/pdf');
        $overflowResponse = $this->actingAs($candidate)->post('/candidate/cvs', [
            'cv_title' => "CV Phiên bản 6",
            'cv_file' => $sixthCvFile,
        ]);

        $overflowResponse->assertSessionHas('error');
        $this->assertEquals(5, $profile->cvs()->count());

        // 5. Test set default and delete
        $firstCv = $profile->cvs()->first();
        $secondCv = $profile->cvs()->skip(1)->first();

        // Set default
        $this->actingAs($candidate)->post("/candidate/cvs/{$secondCv->id}/default");
        $this->assertTrue($secondCv->fresh()->is_default);

        // Delete CV
        $deleteResponse = $this->actingAs($candidate)->delete("/candidate/cvs/{$firstCv->id}");
        $deleteResponse->assertSessionHas('success');
        $this->assertEquals(4, $profile->cvs()->count());
    }
}
