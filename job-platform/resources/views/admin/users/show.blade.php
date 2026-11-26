@extends('layouts.admin')

@section('title', 'Hồ Sơ & Quản Trị Người Dùng #' . $user->id)
@section('page_title', 'Chi tiết tài khoản #' . $user->id)

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb & Back button -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.users.index') }}" class="p-2 bg-white rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition" title="Quay lại danh sách">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                <p class="text-xs text-gray-500">Mã tài khoản: #{{ $user->id }} • Tham gia từ: {{ $user->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <div>
            @if($user->account_status === 'active')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                    ● Trạng thái: Hoạt động (Active)
                </span>
            @elseif($user->account_status === 'suspended')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                    ▲ Trạng thái: Tạm khóa (Suspended)
                </span>
            @else
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-800">
                    ✕ Trạng thái: Cấm vĩnh viễn (Banned)
                </span>
            @endif
        </div>
    </div>

    <!-- 2 Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- LEFT COLUMN: Account Details & Role Information (2 cols) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Account Info Card -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-base font-bold text-gray-900 mb-4 pb-2 border-b border-gray-100 flex items-center justify-between">
                    <span>Thông tin tài khoản</span>
                    <span class="text-xs text-gray-400 font-normal">Cập nhật lần cuối: {{ $user->updated_at->diffForHumans() }}</span>
                </h2>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-xs text-gray-500">Họ và tên</dt>
                        <dd class="font-semibold text-gray-900 mt-0.5">{{ $user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Địa chỉ Email</dt>
                        <dd class="font-medium text-gray-900 mt-0.5">
                            {{ $user->email }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Số điện thoại</dt>
                        <dd class="font-medium text-gray-900 mt-0.5">
                            {{ $user->phone ?? 'Chưa cập nhật SĐT' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Vai trò (Role)</dt>
                        <dd class="font-semibold mt-0.5">
                            @if($user->role === 'admin')
                                <span class="text-rose-600">👑 Quản trị viên</span>
                            @elseif($user->role === 'employer')
                                <span class="text-purple-600">🏢 Nhà tuyển dụng</span>
                            @else
                                <span class="text-blue-600">🎓 Ứng viên</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Trạng thái xác thực Email / Danh tính</dt>
                        <dd class="mt-0.5">
                            @if($user->email_verified_at)
                                <span class="text-xs font-semibold text-emerald-600">✓ Đã xác thực ({{ $user->email_verified_at->format('d/m/Y H:i') }})</span>
                            @else
                                <span class="text-xs font-semibold text-amber-600">Chưa xác thực</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Mật khẩu</dt>
                        <dd class="text-xs text-gray-400 mt-0.5 italic">
                            •••••••• (Mã hóa một chiều Bcrypt - Admin không thể xem)
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Role Context: Candidate Profile -->
            @if($user->isCandidate())
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h2 class="text-base font-bold text-gray-900 mb-4 pb-2 border-b border-gray-100 flex items-center justify-between">
                        <span>Hồ sơ ứng viên (Candidate Profile)</span>
                        <span class="text-xs font-normal text-indigo-600">Số lượt ứng tuyển: {{ $user->applications_count ?? 0 }}</span>
                    </h2>
                    @if($user->candidateProfile)
                        <div class="space-y-3 text-sm">
                            <div>
                                <span class="text-xs text-gray-500 font-medium">Tóm tắt nghề nghiệp:</span>
                                <p class="mt-1 text-gray-800 bg-gray-50 p-3 rounded-lg">{{ $user->candidateProfile->career_summary ?? 'Chưa có thông tin.' }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 font-medium">Kỹ năng chuyên môn:</span>
                                <p class="mt-1 text-indigo-700 font-medium">{{ $user->candidateProfile->skills ?? 'Chưa cập nhật' }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 font-medium">Học vấn & Bằng cấp:</span>
                                <p class="mt-1 text-gray-700">{{ $user->candidateProfile->education ?? 'Chưa cập nhật' }}</p>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-400">Ứng viên chưa khởi tạo hồ sơ trực tuyến.</p>
                    @endif
                </div>
            @endif

            <!-- Role Context: Employer Company -->
            @if($user->isEmployer())
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h2 class="text-base font-bold text-gray-900 mb-4 pb-2 border-b border-gray-100 flex items-center justify-between">
                        <span>Thông tin Doanh nghiệp</span>
                        @if($user->company)
                            <span class="text-xs px-2.5 py-0.5 rounded-full font-semibold {{ $user->company->verification_status === 'verified' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                Xác thực: {{ strtoupper($user->company->verification_status) }}
                            </span>
                        @endif
                    </h2>
                    @if($user->company)
                        <div class="space-y-3 text-sm">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <span class="text-xs text-gray-500">Tên công ty:</span>
                                    <p class="font-semibold text-gray-900">{{ $user->company->company_name }}</p>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500">Mã số thuế:</span>
                                    <p class="font-mono text-gray-800">{{ $user->company->tax_code ?? 'Chưa cập nhật' }}</p>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500">Lĩnh vực:</span>
                                    <p class="text-gray-800">{{ $user->company->industry ?? 'Chưa cập nhật' }}</p>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500">Tin tuyển dụng:</span>
                                    <p class="font-semibold text-indigo-600">{{ $user->company->jobs->count() }} tin đăng</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-400">Nhà tuyển dụng chưa thiết lập thông tin công ty.</p>
                    @endif
                </div>
            @endif

            <!-- Audit Logs History for this User -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-base font-bold text-gray-900 mb-4 pb-2 border-b border-gray-100 flex items-center justify-between">
                    <span class="flex items-center">
                        <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 mr-2"></span>
                        Nhật ký thao tác nghiệp vụ (Audit Log History)
                    </span>
                    <span class="text-xs text-gray-400">{{ $user->auditLogsReceived->count() }} sự kiện</span>
                </h2>

                @if($user->auditLogsReceived->isEmpty())
                    <p class="text-sm text-gray-400 py-3 text-center">Chưa có bản ghi nhật ký kiểm duyệt nào cho tài khoản này.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-xs divide-y divide-gray-100">
                            <thead>
                                <tr class="font-semibold text-gray-500 uppercase bg-gray-50">
                                    <th class="py-2.5 px-3">Quản trị viên</th>
                                    <th class="py-2.5 px-3">Hành động</th>
                                    <th class="py-2.5 px-3">Chi tiết & Lý do</th>
                                    <th class="py-2.5 px-3">Thời điểm</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($user->auditLogsReceived as $log)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-2.5 px-3 font-semibold text-gray-900">
                                            {{ $log->admin?->name ?? 'Admin #'.$log->admin_id }}
                                        </td>
                                        <td class="py-2.5 px-3">
                                            @if($log->action === 'warn')
                                                <span class="px-2 py-0.5 rounded font-bold bg-yellow-100 text-yellow-800">Cảnh cáo</span>
                                            @elseif($log->action === 'suspend')
                                                <span class="px-2 py-0.5 rounded font-bold bg-amber-100 text-amber-800">Tạm khóa</span>
                                            @elseif($log->action === 'ban')
                                                <span class="px-2 py-0.5 rounded font-bold bg-rose-100 text-rose-800">Cấm vĩnh viễn</span>
                                            @elseif($log->action === 'reactivate')
                                                <span class="px-2 py-0.5 rounded font-bold bg-emerald-100 text-emerald-800">Kích hoạt lại</span>
                                            @elseif($log->action === 'send_password_reset')
                                                <span class="px-2 py-0.5 rounded font-bold bg-blue-100 text-blue-800">Gửi link reset</span>
                                            @elseif($log->action === 'manual_verify')
                                                <span class="px-2 py-0.5 rounded font-bold bg-purple-100 text-purple-800">Xác minh thủ công</span>
                                            @elseif($log->action === 'update_login_email')
                                                <span class="px-2 py-0.5 rounded font-bold bg-indigo-100 text-indigo-800">Đổi email</span>
                                            @elseif($log->action === 'unmask_sensitive_data')
                                                <span class="px-2 py-0.5 rounded font-bold bg-slate-100 text-slate-700">Mở che dữ liệu</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded font-bold bg-gray-100 text-gray-800">{{ $log->action }}</span>
                                            @endif
                                        </td>
                                        <td class="py-2.5 px-3 text-gray-700 max-w-sm truncate" title="{{ $log->reason }}">
                                            {{ $log->reason ?? '—' }}
                                        </td>
                                        <td class="py-2.5 px-3 text-gray-400 whitespace-nowrap">
                                            {{ $log->created_at->format('d/m/Y H:i') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- RIGHT COLUMN: Operations Panel (Moderate & Recovery) (1 col) -->
        <div class="space-y-6">
            @if($user->isAdmin() && $user->id !== auth()->id())
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 text-xs text-gray-500">
                    <p class="font-bold text-gray-800 mb-1">Quy định bảo mật:</p>
                    Không thể thực hiện kiểm duyệt hoặc khôi phục đối với tài khoản có quyền Quản trị viên (đặc quyền ngang hoặc cao hơn).
                </div>
            @elseif($user->id === auth()->id())
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 text-xs text-gray-500">
                    <p class="font-bold text-gray-800 mb-1">Tài khoản hiện tại:</p>
                    Bạn đang đăng nhập bằng tài khoản này. Không thể thao tác kiểm duyệt trên chính mình.
                </div>
            @else
                <!-- TAB PANEL: 1. Moderate Account / 2. Assist Account Recovery -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden" x-data="{ tab: 'moderate' }">
                    <div class="flex border-b border-gray-200 bg-gray-50 text-xs font-semibold">
                        <button type="button" @click="tab = 'moderate'" 
                                :class="tab === 'moderate' ? 'bg-white text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-500 hover:text-gray-700'"
                                class="flex-1 py-3 px-4 text-center transition">
                            ⚖️ Kiểm duyệt (Moderate)
                        </button>
                        <button type="button" @click="tab = 'recovery'" 
                                :class="tab === 'recovery' ? 'bg-white text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-500 hover:text-gray-700'"
                                class="flex-1 py-3 px-4 text-center transition">
                            🔑 Khôi phục (Recovery)
                        </button>
                    </div>

                    <!-- TAB 1: MODERATE ACCOUNT (A1) -->
                    <div x-show="tab === 'moderate'" class="p-6">
                        <h3 class="text-sm font-bold text-gray-900 mb-1">Kiểm duyệt trạng thái tài khoản</h3>
                        <p class="text-xs text-gray-500 mb-4">Áp dụng các chế tài cảnh cáo, tạm khóa, cấm vĩnh viễn hoặc kích hoạt lại.</p>

                        <form method="POST" action="{{ route('admin.users.moderate', $user) }}" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Thao tác kiểm duyệt *</label>
                                <select name="action" id="actionSelect" onchange="checkDuration()" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    @if($user->account_status === 'active')
                                        <option value="suspend" selected>1. Tạm khóa (Suspend) - Khóa tài khoản</option>
                                        <option value="ban">2. Cấm vĩnh viễn (Ban) - Khóa vĩnh viễn</option>
                                        <option value="warn">3. Cảnh cáo (Warn) - Nhắc nhở (giữ nguyên hoạt động)</option>
                                    @else
                                        <option value="reactivate" selected>1. Kích hoạt lại (Reactivate) - Mở khóa tài khoản</option>
                                        <option value="ban" {{ $user->account_status === 'banned' ? 'selected' : '' }}>2. Cấm vĩnh viễn (Ban)</option>
                                        <option value="suspend" {{ $user->account_status === 'suspended' ? 'selected' : '' }}>3. Tạm khóa (Suspend)</option>
                                        <option value="warn">4. Cảnh cáo (Warn)</option>
                                    @endif
                                </select>
                            </div>

                            <div id="durationDiv">
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Thời hạn tạm khóa</label>
                                <select name="duration" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="3_days">3 ngày</option>
                                    <option value="7_days" selected>7 ngày</option>
                                    <option value="14_days">14 ngày</option>
                                    <option value="30_days">30 ngày</option>
                                    <option value="indefinite">Không thời hạn</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Lý do kiểm duyệt (Tùy chọn)</label>
                                <textarea name="reason" rows="3" placeholder="Nhập lý do cụ thể (nếu có) để lưu vào Audit Log..." class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                            </div>

                            <button type="submit" class="w-full py-2 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-lg shadow-sm transition">
                                Xác nhận kiểm duyệt & Ghi Audit Log
                            </button>
                        </form>
                    </div>

                    <!-- TAB 2: ASSIST ACCOUNT RECOVERY (A2, A3, A4) -->
                    <div x-show="tab === 'recovery'" class="p-6 space-y-6">
                        <div class="border-b border-gray-100 pb-2">
                            <h3 class="text-sm font-bold text-gray-900">Hỗ trợ khôi phục tài khoản</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Tất cả thao tác khôi phục bắt buộc phải có mã <strong>Support Ticket ID</strong> (VD: TK-10294).</p>
                        </div>

                        <!-- A2. Send Password Reset Link -->
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <h4 class="text-xs font-bold text-gray-900 mb-1">1. Gửi liên kết đặt lại mật khẩu (A2)</h4>
                            <p class="text-[11px] text-gray-500 mb-3">Tạo token bảo mật đơn kỳ (Single-use token) và gửi tới email đăng ký của người dùng.</p>
                            <form method="POST" action="{{ route('admin.users.password-reset-link', $user) }}" class="space-y-2">
                                @csrf
                                <div>
                                    <input type="text" name="ticket_id" required placeholder="Mã Support Ticket (VD: TK-10294)" class="w-full text-xs rounded border-gray-300">
                                </div>
                                <button type="submit" class="w-full py-1.5 px-3 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded transition">
                                    Tạo Token & Gửi link Reset
                                </button>
                            </form>
                        </div>

                        <!-- A3. Manual Verification -->
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <h4 class="text-xs font-bold text-gray-900 mb-1">2. Xác minh danh tính thủ công (A3)</h4>
                            <p class="text-[11px] text-gray-500 mb-3">Xác thực danh tính người dùng/doanh nghiệp dựa trên giấy tờ khiếu nại.</p>
                            <form method="POST" action="{{ route('admin.users.manual-verify', $user) }}" class="space-y-2">
                                @csrf
                                <div>
                                    <input type="text" name="ticket_id" required placeholder="Mã Support Ticket (VD: TK-10294)" class="w-full text-xs rounded border-gray-300">
                                </div>
                                <div>
                                    <textarea name="verification_notes" rows="2" required placeholder="Ghi chú đối soát tài liệu / giấy tờ..." class="w-full text-xs rounded border-gray-300"></textarea>
                                </div>
                                <button type="submit" class="w-full py-1.5 px-3 bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold rounded transition">
                                    Xác nhận danh tính thủ công
                                </button>
                            </form>
                        </div>

                        <!-- A4. Update Login Email -->
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <h4 class="text-xs font-bold text-gray-900 mb-1">3. Cập nhật Email đăng nhập (A4)</h4>
                            <p class="text-[11px] text-gray-500 mb-3">Hỗ trợ đổi email đăng nhập khi người dùng mất quyền truy cập hòm thư cũ.</p>
                            <form method="POST" action="{{ route('admin.users.update-email', $user) }}" class="space-y-2">
                                @csrf
                                @method('PATCH')
                                <div>
                                    <input type="text" name="ticket_id" required placeholder="Mã Support Ticket (VD: TK-10294)" class="w-full text-xs rounded border-gray-300">
                                </div>
                                <div>
                                    <input type="email" name="new_email" required placeholder="Email mới (chưa có trong hệ thống)" class="w-full text-xs rounded border-gray-300">
                                </div>
                                <div>
                                    <input type="text" name="reason" required placeholder="Lý do đổi email đăng nhập..." class="w-full text-xs rounded border-gray-300">
                                </div>
                                <button type="submit" class="w-full py-1.5 px-3 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded transition">
                                    Cập nhật Email đăng nhập
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    function checkDuration() {
        const val = document.getElementById('actionSelect').value;
        const durDiv = document.getElementById('durationDiv');
        if (durDiv) {
            durDiv.style.display = (val === 'suspend') ? 'block' : 'none';
        }
    }
    document.addEventListener('DOMContentLoaded', checkDuration);
</script>
@endsection
