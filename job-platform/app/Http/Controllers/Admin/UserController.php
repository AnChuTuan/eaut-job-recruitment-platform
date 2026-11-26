<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Main Flow 1-4: Hiển thị danh sách tài khoản người dùng với Tìm kiếm, Lọc và Phân trang.
     * Masked data mặc định theo Special Requirement (Data Privacy).
     */
    public function index(Request $request): View
    {
        $query = User::query();

        // Tìm kiếm theo từ khóa (tên, email, phone)
        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Lọc theo Role
        if ($role = $request->input('role')) {
            if (in_array($role, ['candidate', 'employer', 'admin'])) {
                $query->where('role', $role);
            }
        }

        // Lọc theo Status
        if ($status = $request->input('status')) {
            if (in_array($status, ['active', 'suspended', 'banned'])) {
                $query->where('account_status', $status);
            }
        }

        $users = $query->with(['candidateProfile', 'company'])
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total' => User::count(),
            'candidate' => User::where('role', 'candidate')->count(),
            'employer' => User::where('role', 'employer')->count(),
            'admin' => User::where('role', 'admin')->count(),
            'suspended' => User::where('account_status', 'suspended')->count(),
            'banned' => User::where('account_status', 'banned')->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    /**
     * Main Flow 5-6: Xem chi tiết tài khoản kèm hồ sơ cá nhân và lịch sử Audit Logs.
     */
    public function show(User $user): View
    {
        $user->load([
            'candidateProfile',
            'company.jobs',
            'auditLogsReceived.admin',
            'notifications',
        ]);

        if ($user->isCandidate()) {
            $user->loadCount('applications');
        }

        return view('admin.users.show', compact('user'));
    }

    /**
     * Alternative Flow A1: Moderate Account (Warn, Suspend, Ban, Reactivate)
     * Thỏa mãn Security & Session Control: Hủy toàn bộ Refresh Token/Session khi Suspend/Ban.
     */
    public function moderate(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'action' => 'required|in:warn,suspend,ban,reactivate',
            'reason' => 'nullable|string|max:1000',
            'duration' => 'nullable|string|max:50',
        ]);

        $action = $validated['action'];
        $reason = !empty(trim($validated['reason'] ?? '')) ? trim($validated['reason']) : 'Thao tác kiểm duyệt bởi Quản trị viên.';
        $duration = $validated['duration'] ?? '7_days';
        $currentAdmin = $request->user();

        // Exception Handling A5.7: Chặn thao tác với tài khoản có đặc quyền ngang hoặc cao hơn
        if ($user->isAdmin()) {
            return back()->with('error', 'Không được phép thực hiện kiểm duyệt trên tài khoản Quản trị viên (đặc quyền ngang hoặc cao hơn).');
        }

        if ($user->id === $currentAdmin?->id) {
            return back()->with('error', 'Bạn không thể tự thực hiện kiểm duyệt trên tài khoản của chính mình.');
        }

        switch ($action) {
            case 'warn':
                // Ghi nhận cảnh cáo vào Audit Log
                AuditLog::create([
                    'admin_id' => $currentAdmin ? $currentAdmin->id : 1,
                    'target_type' => 'User',
                    'target_id' => $user->id,
                    'action' => 'warn',
                    'reason' => $reason,
                ]);

                // Thông báo tới người dùng
                Notification::create([
                    'user_id' => $user->id,
                    'title' => 'Cảnh báo vi phạm từ Quản trị viên',
                    'message' => $reason,
                ]);

                return back()->with('success', "Đã ghi nhận cảnh báo và gửi thông báo vi phạm tới tài khoản {$user->name}.");

            case 'suspend':
                $durationMap = [
                    '3_days' => '3 ngày',
                    '7_days' => '7 ngày',
                    '14_days' => '14 ngày',
                    '30_days' => '30 ngày',
                    'indefinite' => 'cho đến khi có thông báo mới',
                ];
                $durationText = $durationMap[$duration] ?? $duration;

                $user->account_status = 'suspended';
                $user->save();

                // Security & Session Control: Hủy toàn bộ phiên đăng nhập của người dùng ngay lập tức
                DB::table('sessions')->where('user_id', $user->id)->delete();

                // Ghi Audit Log
                AuditLog::create([
                    'admin_id' => $currentAdmin ? $currentAdmin->id : 1,
                    'target_type' => 'User',
                    'target_id' => $user->id,
                    'action' => 'suspend',
                    'reason' => "[Thời hạn: {$durationText}] {$reason}",
                ]);

                // Thông báo tới người dùng
                Notification::create([
                    'user_id' => $user->id,
                    'title' => 'Tài khoản của bạn đã bị tạm khóa',
                    'message' => "Tài khoản của bạn bị tạm khóa trong thời hạn: {$durationText}. Lý do: {$reason}",
                ]);

                return back()->with('success', "Đã tạm khóa tài khoản {$user->name} ({$durationText}) và vô hiệu hóa toàn bộ phiên đăng nhập.");

            case 'ban':
                $user->account_status = 'banned';
                $user->save();

                // Hủy toàn bộ phiên đăng nhập
                DB::table('sessions')->where('user_id', $user->id)->delete();

                AuditLog::create([
                    'admin_id' => $currentAdmin ? $currentAdmin->id : 1,
                    'target_type' => 'User',
                    'target_id' => $user->id,
                    'action' => 'ban',
                    'reason' => $reason,
                ]);

                Notification::create([
                    'user_id' => $user->id,
                    'title' => 'Tài khoản của bạn đã bị cấm vĩnh viễn',
                    'message' => "Lý do: {$reason}. Vui lòng liên hệ hỗ trợ nếu cần khiếu nại.",
                ]);

                return back()->with('success', "Đã cấm vĩnh viễn tài khoản {$user->name} và tước quyền truy cập hệ thống.");

            case 'reactivate':
                $user->account_status = 'active';
                $user->save();

                AuditLog::create([
                    'admin_id' => $currentAdmin ? $currentAdmin->id : 1,
                    'target_type' => 'User',
                    'target_id' => $user->id,
                    'action' => 'reactivate',
                    'reason' => $reason ?: 'Khôi phục trạng thái hoạt động bình thường.',
                ]);

                Notification::create([
                    'user_id' => $user->id,
                    'title' => 'Tài khoản đã được mở khóa',
                    'message' => 'Tài khoản của bạn đã được quản trị viên khôi phục trạng thái hoạt động bình thường.',
                ]);

                return back()->with('success', "Đã kích hoạt lại tài khoản {$user->name} thành công.");
        }

        return back()->with('info', 'Không có hành động nào được thực hiện.');
    }

    /**
     * Alternative Flow A2: Send Password Reset Link (Hỗ trợ khôi phục mật khẩu).
     * Bắt buộc có mã Support Ticket ID hợp lệ và token đơn kỳ an toàn.
     */
    public function sendPasswordResetLink(Request $request, User $user): RedirectResponse
    {
        // Exception Handling: Kiểm tra Support Ticket ID (VD: TK-10294, TK-ABCD)
        $validated = $request->validate([
            'ticket_id' => ['required', 'string', 'regex:/^TK-[A-Z0-9]{4,12}$/i'],
        ], [
            'ticket_id.required' => 'Bắt buộc nhập mã Support Ticket ID để thực hiện hỗ trợ khôi phục tài khoản.',
            'ticket_id.regex' => 'Mã Support Ticket không hợp lệ! Định dạng yêu cầu: TK-XXXXX (Ví dụ: TK-10294, TK-99881).',
        ]);

        $ticketId = strtoupper($validated['ticket_id']);
        $currentAdmin = $request->user();

        if ($user->isAdmin()) {
            return back()->with('error', 'Không được phép thực hiện khôi phục tài khoản của Quản trị viên.');
        }

        // Tạo token đặt lại mật khẩu an toàn (Single-use token)
        $token = Password::broker()->createToken($user);
        $user->sendPasswordResetNotification($token);

        // Ghi Audit Log bất biến
        AuditLog::create([
            'admin_id' => $currentAdmin ? $currentAdmin->id : 1,
            'target_type' => 'User',
            'target_id' => $user->id,
            'action' => 'send_password_reset',
            'reason' => "[Support Ticket: {$ticketId}] Tạo token bảo mật đơn kỳ và gửi link đặt lại mật khẩu tới email đăng ký: {$user->email}.",
        ]);

        return back()->with('success', "Đã tạo token bảo mật và gửi link đặt lại mật khẩu tới {$user->email} (Ticket: {$ticketId}).");
    }

    /**
     * Alternative Flow A3: Manual Verification (Xác minh danh tính thủ công).
     * Bắt buộc có mã Support Ticket ID hợp lệ.
     */
    public function manualVerify(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'ticket_id' => ['required', 'string', 'regex:/^TK-[A-Z0-9]{4,12}$/i'],
            'verification_notes' => 'required|string|min:5|max:1000',
        ], [
            'ticket_id.required' => 'Bắt buộc nhập mã Support Ticket ID.',
            'ticket_id.regex' => 'Mã Support Ticket không hợp lệ! Định dạng yêu cầu: TK-XXXXX (Ví dụ: TK-10294).',
            'verification_notes.required' => 'Vui lòng cung cấp ghi chú xác minh tài liệu danh tính.',
        ]);

        $ticketId = strtoupper($validated['ticket_id']);
        $notes = $validated['verification_notes'];
        $currentAdmin = $request->user();

        // Cập nhật trạng thái xác thực
        $user->email_verified_at = now();
        $user->save();

        if ($user->isEmployer() && $user->company) {
            $user->company->update(['verification_status' => 'verified']);
        }

        // Ghi Audit Log
        AuditLog::create([
            'admin_id' => $currentAdmin ? $currentAdmin->id : 1,
            'target_type' => 'User',
            'target_id' => $user->id,
            'action' => 'manual_verify',
            'reason' => "[Support Ticket: {$ticketId}] Xác minh danh tính thủ công thành công. Ghi chú: {$notes}",
        ]);

        Notification::create([
            'user_id' => $user->id,
            'title' => 'Tài khoản đã được xác minh danh tính',
            'message' => 'Hồ sơ và thông tin danh tính của bạn đã được Quản trị viên xét duyệt xác minh thủ công.',
        ]);

        return back()->with('success', "Đã xác minh danh tính thủ công cho tài khoản {$user->name} (Ticket: {$ticketId}).");
    }

    /**
     * Alternative Flow A4: Update Login Email (Cập nhật email đăng nhập theo hỗ trợ người dùng).
     * Bắt buộc có mã Support Ticket ID, kiểm tra trùng email và ghi Audit Log.
     */
    public function updateLoginEmail(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'ticket_id' => ['required', 'string', 'regex:/^TK-[A-Z0-9]{4,12}$/i'],
            'new_email' => 'required|email|unique:users,email',
            'reason' => 'required|string|min:5|max:1000',
        ], [
            'ticket_id.required' => 'Bắt buộc nhập mã Support Ticket ID.',
            'ticket_id.regex' => 'Mã Support Ticket không hợp lệ! Định dạng yêu cầu: TK-XXXXX (Ví dụ: TK-10294).',
            'new_email.required' => 'Vui lòng nhập địa chỉ email mới.',
            'new_email.unique' => 'Địa chỉ email này đã được sử dụng bởi một tài khoản khác trong hệ thống.',
            'reason.required' => 'Vui lòng cung cấp lý do cập nhật email đăng nhập.',
        ]);

        if ($user->isAdmin()) {
            return back()->with('error', 'Không được phép thay đổi email của Quản trị viên khác.');
        }

        $ticketId = strtoupper($validated['ticket_id']);
        $oldEmail = $user->email;
        $newEmail = strtolower($validated['new_email']);

        if ($newEmail === strtolower($oldEmail)) {
            return back()->with('error', 'Email mới phải khác với email hiện tại của người dùng.');
        }

        $currentAdmin = $request->user();

        $user->email = $newEmail;
        $user->save();

        // Hủy phiên đăng nhập cũ để người dùng đăng nhập bằng email mới
        DB::table('sessions')->where('user_id', $user->id)->delete();

        // Ghi Audit Log bất biến
        AuditLog::create([
            'admin_id' => $currentAdmin ? $currentAdmin->id : 1,
            'target_type' => 'User',
            'target_id' => $user->id,
            'action' => 'update_login_email',
            'reason' => "[Support Ticket: {$ticketId}] Cập nhật email đăng nhập từ [{$oldEmail}] thành [{$newEmail}]. Lý do: {$validated['reason']}",
        ]);

        return back()->with('success', "Đã cập nhật email đăng nhập từ [{$oldEmail}] sang [{$newEmail}] thành công (Ticket: {$ticketId}).");
    }
}
