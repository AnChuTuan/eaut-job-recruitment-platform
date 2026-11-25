<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Company;
use App\Models\CompanyVerification;
use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EmployerManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_employer_can_create_company_profile_and_upload_logo(): void
    {
        Storage::fake('public');
        $employer = User::factory()->create(['role' => 'employer']);

        $response = $this->actingAs($employer)->put(route('employer.company.update'), [
            'company_name' => 'Acme Vietnam',
            'email' => 'contact@acme.test',
            'industry' => 'Technology',
            'logo' => UploadedFile::fake()->createWithContent(
                'logo.png',
                base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/l0cAAAAASUVORK5CYII=')
            ),
        ]);

        $response->assertRedirect(route('employer.company.edit'));
        $this->assertDatabaseHas('companies', [
            'user_id' => $employer->id,
            'company_name' => 'Acme Vietnam',
            'email' => 'contact@acme.test',
        ]);
        Storage::disk('public')->assertExists($employer->fresh()->company->logo_url);
        $this->actingAs($employer)->get(route('employer.company.edit'))
            ->assertOk()
            ->assertSee('Acme Vietnam');
        $this->get(route('employer.verification.create'))->assertOk();
    }

    public function test_employer_can_submit_and_admin_can_approve_or_reject_verification(): void
    {
        Storage::fake('local');
        $employer = User::factory()->create(['role' => 'employer']);
        $company = $this->createCompany($employer);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($employer)->post(route('employer.verification.submit'), [
            'supporting_document' => UploadedFile::fake()->create('license.pdf', 100, 'application/pdf'),
        ])->assertRedirect();

        $verification = CompanyVerification::firstOrFail();
        Storage::disk('local')->assertExists($verification->supporting_documents);

        $this->actingAs($admin)->get(route('admin.verifications.index'))->assertOk();
        $this->get(route('admin.verifications.show', $verification))->assertOk();
        $this->get(route('admin.verifications.document', $verification))->assertOk();

        $this->actingAs($admin)->patch(route('admin.verifications.approve', $verification))
            ->assertRedirect();
        $this->assertDatabaseHas('companies', ['id' => $company->id, 'verification_status' => 'verified']);
        $this->assertDatabaseHas('company_verifications', [
            'id' => $verification->id,
            'status' => 'verified',
            'reviewed_by' => $admin->id,
        ]);

        $this->actingAs($employer)->post(route('employer.verification.submit'), [
            'supporting_document' => UploadedFile::fake()->create('updated-license.pdf', 100, 'application/pdf'),
        ])->assertRedirect();
        $pendingVerification = CompanyVerification::where('status', 'pending')->firstOrFail();

        $this->actingAs($admin)->patch(route('admin.verifications.reject', $pendingVerification), [
            'rejection_reason' => 'Thông tin giấy phép chưa rõ.',
        ])->assertRedirect();
        $this->assertDatabaseHas('companies', ['id' => $company->id, 'verification_status' => 'rejected']);
        $this->assertDatabaseHas('company_verifications', [
            'id' => $pendingVerification->id,
            'status' => 'rejected',
            'rejection_reason' => 'Thông tin giấy phép chưa rõ.',
        ]);
    }

    public function test_employer_applicants_are_scoped_to_company_and_cv_is_downloaded_through_authorized_route(): void
    {
        Storage::fake('public');
        $employer = User::factory()->create(['role' => 'employer']);
        $otherEmployer = User::factory()->create(['role' => 'employer']);
        $company = $this->createCompany($employer);
        $otherCompany = $this->createCompany($otherEmployer, 'Other Company');
        $job = $this->createJob($company, 'Developer');
        $otherJob = $this->createJob($otherCompany, 'Secret Position');
        $candidate = User::factory()->create();
        $otherCandidate = User::factory()->create(['name' => 'Private Candidate']);

        $application = Application::create([
            'job_id' => $job->id,
            'candidate_id' => $candidate->id,
            'cv_url' => 'cvs/candidate.pdf',
            'status' => 'pending',
        ]);
        $otherApplication = Application::create([
            'job_id' => $otherJob->id,
            'candidate_id' => $otherCandidate->id,
            'cv_url' => 'cvs/private.pdf',
            'status' => 'pending',
        ]);
        $this->assertSame($candidate->id, $application->user->id);
        $this->assertSame($company->id, $application->job->company->id);
        Storage::disk('public')->put('cvs/candidate.pdf', 'candidate cv');

        $this->actingAs($employer)->get(route('employer.applicants.index', ['search' => 'Secret Position']))
            ->assertOk()
            ->assertDontSee('Private Candidate');
        $this->actingAs($employer)->get(route('employer.applicants.show', $otherApplication))
            ->assertNotFound();
        $this->get(route('employer.applicants.show', $application))->assertOk();

        $this->actingAs($employer)->patch(route('employer.applicants.status', $application), [
            'status' => 'shortlisted',
        ])->assertRedirect();
        $this->assertDatabaseHas('applications', ['id' => $application->id, 'status' => 'shortlisted']);

        $this->actingAs($employer)->get(route('employer.applicants.cv', $application))
            ->assertOk()
            ->assertHeader('content-disposition');
    }

    public function test_candidate_cannot_access_employer_or_admin_tools(): void
    {
        $candidate = User::factory()->create(['role' => 'candidate']);

        $this->actingAs($candidate)->get(route('employer.company.edit'))->assertForbidden();
        $this->actingAs($candidate)->get(route('admin.verifications.index'))->assertForbidden();
    }

    public function test_registration_can_create_employers_but_never_admins(): void
    {
        $this->post(route('register'), [
            'name' => 'Admin Attempt',
            'email' => 'admin-attempt@example.test',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'admin',
        ])->assertSessionHasErrors('role');

        $this->post(route('register'), [
            'name' => 'Hiring Team',
            'email' => 'employer@example.test',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'employer',
        ])->assertRedirect(route('dashboard', absolute: false));

        $this->assertDatabaseHas('users', [
            'email' => 'employer@example.test',
            'role' => 'employer',
        ]);
    }

    private function createCompany(User $employer, string $name = 'Test Company'): Company
    {
        return Company::create([
            'user_id' => $employer->id,
            'company_name' => $name,
        ]);
    }

    private function createJob(Company $company, string $title): Job
    {
        $job = new Job;
        $job->company_id = $company->id;
        $job->title = $title;
        $job->category = 'Engineering';
        $job->quantity = 1;
        $job->deadline = now()->addMonth()->toDateString();
        $job->location = 'Hanoi';
        $job->description = 'Job description';
        $job->status = 'approved';
        $job->save();

        return $job;
    }
}
