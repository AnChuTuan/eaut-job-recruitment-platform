<?php

namespace Tests\Feature\Admin;

use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $anotherAdmin;
    protected User $candidate;
    protected User $employer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin_test@eaut.edu.vn',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'account_status' => 'active',
        ]);

        $this->anotherAdmin = User::create([
            'name' => 'Super Admin Test',
            'email' => 'superadmin_test@eaut.edu.vn',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'account_status' => 'active',
        ]);

        $this->candidate = User::create([
            'name' => 'Nguyen Van Candidate',
            'email' => 'candidate_test@eaut.edu.vn',
            'phone' => '0912345678',
            'password' => bcrypt('password'),
            'role' => 'candidate',
            'account_status' => 'active',
        ]);

        $this->employer = User::create([
            'name' => 'FPT Recruiter',
            'email' => 'employer_test@example.com',
            'phone' => '0987654321',
            'password' => bcrypt('password'),
            'role' => 'employer',
            'account_status' => 'active',
        ]);
    }

    // ------------------------------------------------------------------------
    // MAIN FLOW: VIEW, SEARCH, FILTER, VIEW DETAILS
    // ------------------------------------------------------------------------

    public function test_guest_is_redirected_when_accessing_users_list(): void
    {
        $response = $this->get(route('admin.users.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_user_cannot_access_admin_portal(): void
    {
        $response = $this->actingAs($this->candidate)->get(route('admin.users.index'));
        $response->assertStatus(403);

        $responseEmployer = $this->actingAs($this->employer)->get(route('admin.dashboard'));
        $responseEmployer->assertStatus(403);
    }

    public function test_admin_can_view_users_list(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertSee('Quản lý tài khoản người dùng');
        $response->assertSee($this->candidate->name);
        $response->assertSee($this->candidate->email);
    }

    public function test_admin_can_search_and_filter_users(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users.index', [
            'search' => 'Nguyen Van Candidate',
            'role' => 'candidate',
        ]));

        $response->assertStatus(200);
        $response->assertSee($this->candidate->name);
        $response->assertDontSee($this->employer->name);
    }

    public function test_admin_can_view_account_details(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users.show', $this->candidate));

        $response->assertStatus(200);
        $response->assertSee($this->candidate->name);
        $response->assertSee('Thông tin tài khoản');
        $response->assertSee('Kiểm duyệt (Moderate)');
        $response->assertSee('Khôi phục (Recovery)');
    }

    // ------------------------------------------------------------------------
    // ALTERNATIVE FLOW A1: MODERATE ACCOUNT (WARN, SUSPEND, BAN, REACTIVATE)
    // ------------------------------------------------------------------------

    public function test_admin_can_warn_user(): void
    {
        $reason = 'Cảnh báo lần 1 về hành vi spam hồ sơ.';

        $response = $this->actingAs($this->admin)->post(route('admin.users.moderate', $this->candidate), [
            'action' => 'warn',
            'reason' => $reason,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Audit Log
        $this->assertDatabaseHas('audit_logs', [
            'admin_id' => $this->admin->id,
            'target_type' => 'User',
            'target_id' => $this->candidate->id,
            'action' => 'warn',
            'reason' => $reason,
        ]);

        // Notification
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->candidate->id,
            'title' => 'Cảnh báo vi phạm từ Quản trị viên',
            'message' => $reason,
        ]);
    }

    public function test_admin_can_suspend_user_and_sessions_are_invalidated(): void
    {
        // Giả lập phiên đăng nhập trong bảng sessions
        DB::table('sessions')->insert([
            'id' => 'session_test_123',
            'user_id' => $this->candidate->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'TestBrowser',
            'payload' => 'dummy',
            'last_activity' => time(),
        ]);

        $reason = 'Tạm khóa do báo cáo nghi vấn gian lận.';

        $response = $this->actingAs($this->admin)->post(route('admin.users.moderate', $this->candidate), [
            'action' => 'suspend',
            'duration' => '7_days',
            'reason' => $reason,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $this->candidate->id,
            'account_status' => 'suspended',
        ]);

        // Security check: Session phải bị xóa ngay lập tức
        $this->assertDatabaseMissing('sessions', [
            'user_id' => $this->candidate->id,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'target_id' => $this->candidate->id,
            'action' => 'suspend',
        ]);
    }

    public function test_admin_can_ban_and_reactivate_user(): void
    {
        // 1. Ban
        $this->actingAs($this->admin)->post(route('admin.users.moderate', $this->candidate), [
            'action' => 'ban',
            'reason' => 'Vi phạm nghiêm trọng quy chế hệ thống.',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->candidate->id,
            'account_status' => 'banned',
        ]);

        // 2. Reactivate
        $this->actingAs($this->admin)->post(route('admin.users.moderate', $this->candidate), [
            'action' => 'reactivate',
            'reason' => 'Mở khóa tài khoản sau khi khiếu nại thành công.',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->candidate->id,
            'account_status' => 'active',
        ]);
    }

    // ------------------------------------------------------------------------
    // ALTERNATIVE FLOWS A2, A3, A4: ASSIST ACCOUNT RECOVERY
    // ------------------------------------------------------------------------

    public function test_admin_can_send_password_reset_link_with_ticket_id(): void
    {
        $ticketId = 'TK-10294';

        $response = $this->actingAs($this->admin)->post(route('admin.users.password-reset-link', $this->candidate), [
            'ticket_id' => $ticketId,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('audit_logs', [
            'target_id' => $this->candidate->id,
            'action' => 'send_password_reset',
        ]);
    }

    public function test_admin_can_manual_verify_identity_with_ticket_id(): void
    {
        $ticketId = 'TK-99881';
        $this->candidate->update(['email_verified_at' => null]);

        $response = $this->actingAs($this->admin)->post(route('admin.users.manual-verify', $this->candidate), [
            'ticket_id' => $ticketId,
            'verification_notes' => 'Đã đối chiếu căn cước công dân và thẻ sinh viên trùng khớp.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertNotNull($this->candidate->fresh()->email_verified_at);

        $this->assertDatabaseHas('audit_logs', [
            'target_id' => $this->candidate->id,
            'action' => 'manual_verify',
        ]);
    }

    public function test_admin_can_update_login_email_with_ticket_id(): void
    {
        $ticketId = 'TK-55443';
        $newEmail = 'new_candidate_email@eaut.edu.vn';

        $response = $this->actingAs($this->admin)->patch(route('admin.users.update-email', $this->candidate), [
            'ticket_id' => $ticketId,
            'new_email' => $newEmail,
            'reason' => 'Người dùng mất SIM và email cũ, có đơn xác nhận của nhà trường.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals($newEmail, $this->candidate->fresh()->email);

        $this->assertDatabaseHas('audit_logs', [
            'target_id' => $this->candidate->id,
            'action' => 'update_login_email',
        ]);
    }

    // ------------------------------------------------------------------------
    // EXCEPTION HANDLING A5
    // ------------------------------------------------------------------------

    public function test_recovery_fails_with_invalid_ticket_id_format(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.users.password-reset-link', $this->candidate), [
            'ticket_id' => 'INVALID_TICKET_NO_TK',
        ]);

        $response->assertSessionHasErrors('ticket_id');
    }

    public function test_admin_cannot_moderate_equal_or_higher_privilege_admin(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.users.moderate', $this->anotherAdmin), [
            'action' => 'ban',
            'reason' => 'Thử cấm admin khác',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertEquals('active', $this->anotherAdmin->fresh()->account_status);
    }
}
