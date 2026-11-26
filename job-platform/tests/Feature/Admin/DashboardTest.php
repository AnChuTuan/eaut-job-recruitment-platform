<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Dash Test',
            'email' => 'admin_dash_test@eaut.edu.vn',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'account_status' => 'active',
        ]);
    }

    public function test_guest_is_redirected_when_accessing_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_view_dashboard_with_all_tree_metrics_and_charts(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Bảng điều khiển Quản trị viên');

        // Kiểm tra 5 chỉ số nhánh tổng quan
        $response->assertSee('Tổng số Candidate');
        $response->assertSee('Tổng số Employer');
        $response->assertSee('Job Post đang hoạt động');
        $response->assertSee('Job Post chờ duyệt');
        $response->assertSee('Tổng lượt Application');

        // Kiểm tra 3 biểu đồ nhánh xu hướng
        $response->assertSee('Biểu đồ tăng trưởng người dùng theo thời gian');
        $response->assertSee('Biểu đồ số lượng tin tuyển dụng theo thời gian');
        $response->assertSee('Biểu đồ tỷ lệ phân bố ngành nghề');
    }
}
