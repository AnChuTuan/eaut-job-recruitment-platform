@extends('layouts.admin')

@section('title', 'Quản Lý Tài Khoản Người Dùng')
@section('page_title', 'Quản lý tài khoản (Manage User Accounts)')

@section('content')
<div class="space-y-6">
    <!-- Header Title & Data Privacy Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Quản lý tài khoản người dùng</h1>
            <p class="text-sm text-gray-500 mt-1">Tìm kiếm, lọc, kiểm duyệt trạng thái (Moderate) và hỗ trợ khôi phục tài khoản (Account Recovery).</p>
        </div>
    </div>

    <!-- Quick Filter Tabs -->
    <div class="flex flex-wrap gap-2 pt-1">
        <a href="{{ route('admin.users.index') }}" 
           class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition {{ !request('role') && !request('status') ? 'bg-indigo-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' }}">
            Tất cả ({{ $stats['total'] }})
        </a>
        <a href="{{ route('admin.users.index', ['role' => 'candidate']) }}" 
           class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition {{ request('role') === 'candidate' ? 'bg-blue-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' }}">
            Ứng viên ({{ $stats['candidate'] }})
        </a>
        <a href="{{ route('admin.users.index', ['role' => 'employer']) }}" 
           class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition {{ request('role') === 'employer' ? 'bg-purple-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' }}">
            Nhà tuyển dụng ({{ $stats['employer'] }})
        </a>
        <a href="{{ route('admin.users.index', ['role' => 'admin']) }}" 
           class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition {{ request('role') === 'admin' ? 'bg-rose-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' }}">
            Admin ({{ $stats['admin'] }})
        </a>
        <a href="{{ route('admin.users.index', ['status' => 'suspended']) }}" 
           class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition {{ request('status') === 'suspended' ? 'bg-amber-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' }}">
            Đang tạm khóa ({{ $stats['suspended'] }})
        </a>
        <a href="{{ route('admin.users.index', ['status' => 'banned']) }}" 
           class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition {{ request('status') === 'banned' ? 'bg-red-700 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' }}">
            Cấm vĩnh viễn ({{ $stats['banned'] }})
        </a>
    </div>

    <!-- Filter & Search Toolbar (Main Flow 3-4) -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search Keyword -->
            <div class="md:col-span-2">
                <label for="search" class="block text-xs font-medium text-gray-700 mb-1">Tìm kiếm từ khóa</label>
                <div class="relative">
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                           placeholder="Nhập tên, email hoặc số điện thoại..." 
                           class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 pl-9 py-2">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Role Filter -->
            <div>
                <label for="role" class="block text-xs font-medium text-gray-700 mb-1">Vai trò (Role)</label>
                <select name="role" id="role" class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 py-2">
                    <option value="">-- Tất cả vai trò --</option>
                    <option value="candidate" {{ request('role') === 'candidate' ? 'selected' : '' }}>Ứng viên (Candidate)</option>
                    <option value="employer" {{ request('role') === 'employer' ? 'selected' : '' }}>Nhà tuyển dụng (Employer)</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Quản trị viên (Admin)</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label for="status" class="block text-xs font-medium text-gray-700 mb-1">Trạng thái (Status)</label>
                <select name="status" id="status" class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 py-2">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Hoạt động (Active)</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Tạm khóa (Suspended)</option>
                    <option value="banned" {{ request('status') === 'banned' ? 'selected' : '' }}>Cấm vĩnh viễn (Banned)</option>
                </select>
            </div>

            <!-- Submit buttons -->
            <div class="md:col-span-4 flex items-center justify-end space-x-2 pt-1 border-t border-gray-100">
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 text-xs font-medium text-gray-600 hover:text-gray-900 transition">
                    Xóa bộ lọc
                </a>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                    Lọc dữ liệu
                </button>
            </div>
        </form>
    </div>

    <!-- Users Table (Main Flow 1-2) -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <!-- Exception Handling A5.1: Không có kết quả trùng khớp -->
        @if($users->isEmpty())
            <div class="p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-semibold text-gray-900">Không tìm thấy tài khoản người dùng phù hợp</h3>
                <p class="mt-1 text-xs text-gray-500">Vui lòng kiểm tra lại từ khóa hoặc các điều kiện lọc đang chọn.</p>
                <div class="mt-4">
                    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-lg text-xs font-semibold transition">
                        Đặt lại tìm kiếm
                    </a>
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                    <thead class="bg-gray-50 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <tr>
                            <th class="py-3.5 px-4">Tài khoản & ID</th>
                            <th class="py-3.5 px-4">Thông tin liên hệ</th>
                            <th class="py-3.5 px-4">Vai trò (Role)</th>
                            <th class="py-3.5 px-4">Trạng thái (Status)</th>
                            <th class="py-3.5 px-4">Ngày đăng ký</th>
                            <th class="py-3.5 px-4 text-right">Thao tác nghiệp vụ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($users as $user)
                            <tr class="hover:bg-gray-50 transition">
                                <!-- User ID & Full Name -->
                                <td class="py-4 px-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-700 font-bold flex items-center justify-center flex-shrink-0">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.users.show', $user) }}" class="font-semibold text-gray-900 hover:text-indigo-600 transition">
                                                {{ $user->name }}
                                            </a>
                                            <p class="text-xs text-gray-400">User ID: #{{ $user->id }}</p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Contact Information -->
                                <td class="py-4 px-4 text-xs">
                                    <p class="font-medium text-gray-900">{{ $user->email }}</p>
                                    <p class="text-gray-500 mt-0.5">{{ $user->phone ?? 'Chưa cập nhật SĐT' }}</p>
                                </td>

                                <!-- Role -->
                                <td class="py-4 px-4">
                                    @if($user->role === 'admin')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-rose-100 text-rose-800">
                                            👑 Quản trị viên
                                        </span>
                                    @elseif($user->role === 'employer')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-purple-100 text-purple-800">
                                            🏢 Nhà tuyển dụng
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-blue-100 text-blue-800">
                                            🎓 Ứng viên
                                        </span>
                                    @endif
                                </td>

                                <!-- Status -->
                                <td class="py-4 px-4">
                                    @if($user->account_status === 'active')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                            ● Hoạt động
                                        </span>
                                    @elseif($user->account_status === 'suspended')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                            ▲ Tạm khóa
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-rose-100 text-rose-800">
                                            ✕ Cấm vĩnh viễn
                                        </span>
                                    @endif
                                </td>

                                <!-- Registered date -->
                                <td class="py-4 px-4 text-xs text-gray-500">
                                    {{ $user->created_at->format('d/m/Y') }}
                                    <span class="block text-gray-400 text-[11px]">{{ $user->created_at->diffForHumans() }}</span>
                                </td>

                                <!-- Operations (Main Flow 7-8: Moderate or Assist Recovery) -->
                                <td class="py-4 px-4 text-right space-x-1.5 whitespace-nowrap">
                                    <a href="{{ route('admin.users.show', $user) }}" class="inline-flex items-center px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition" title="Xem hồ sơ & khôi phục">
                                        Chi tiết
                                    </a>

                                    @if(!$user->isAdmin())
                                        <!-- Nút mở nhanh Modal Kiểm duyệt (Moderate) -->
                                        <button type="button" 
                                                onclick="openModerateModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->account_status }}')"
                                                class="inline-flex items-center px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-medium rounded-lg transition" title="Kiểm duyệt trạng thái">
                                            Kiểm duyệt
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>

<!-- ======================================================= -->
<!-- MODAL KIỂM DUYỆT TÀI KHOẢN (A1: MODERATE ACCOUNT MODAL) -->
<!-- ======================================================= -->
<div id="moderateModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full overflow-hidden">
        <form id="moderateForm" method="POST" action="">
            @csrf
            <div class="p-6">
                <h3 class="text-lg font-bold text-gray-900 flex items-center">
                    <span class="text-amber-500 mr-2">⚖️</span> Kiểm duyệt tài khoản (Moderate Account)
                </h3>
                <p class="text-xs text-gray-500 mt-1">
                    Thao tác trên tài khoản: <span id="modUserName" class="font-bold text-gray-900"></span>.
                </p>

                <div class="mt-4 space-y-4 text-left">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Hành động kiểm duyệt *</label>
                        <select name="action" id="modAction" onchange="toggleDurationField()" required class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="suspend">1. Tạm khóa (Suspend) - Vô hiệu hóa Session</option>
                            <option value="ban">2. Cấm vĩnh viễn (Ban) - Thu hồi toàn bộ quyền</option>
                            <option value="reactivate">3. Kích hoạt lại (Reactivate) - Mở khóa tài khoản</option>
                            <option value="warn">4. Cảnh cáo (Warn) - Gửi thông báo (giữ nguyên trạng thái)</option>
                        </select>
                    </div>

                    <!-- Thời hạn tạm khóa (chỉ hiện khi suspend) -->
                    <div id="durationContainer">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Thời hạn tạm khóa</label>
                        <select name="duration" class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="3_days">3 ngày</option>
                            <option value="7_days" selected>7 ngày</option>
                            <option value="14_days">14 ngày</option>
                            <option value="30_days">30 ngày</option>
                            <option value="indefinite">Cho đến khi có quyết định mới</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Lý do kiểm duyệt (Tùy chọn)</label>
                        <textarea name="reason" rows="3" placeholder="Nhập lý do chi tiết để lưu Audit Log (tùy chọn)..." class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>

                    <p class="text-[11px] text-amber-700 bg-amber-50 p-2.5 rounded-lg border border-amber-200">
                        ⚡ <strong>Lưu ý:</strong> Khi chọn Suspend hoặc Ban, toàn bộ phiên đăng nhập của người dùng sẽ bị vô hiệu hóa ngay lập tức. Thao tác được ghi vào Audit Log bất biến.
                    </p>
                </div>
            </div>

            <div class="px-6 py-3 bg-gray-50 flex items-center justify-end space-x-2 border-t border-gray-100">
                <button type="button" onclick="closeModerateModal()" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:text-gray-800 transition">
                    Hủy bỏ
                </button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold shadow transition">
                    Xác nhận thực hiện
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModerateModal(userId, userName, currentStatus) {
        const modal = document.getElementById('moderateModal');
        const form = document.getElementById('moderateForm');
        document.getElementById('modUserName').textContent = userName;
        const baseUrl = "{{ url('admin/users') }}";
        form.action = `${baseUrl}/${userId}/moderate`;
        
        const actionSelect = document.getElementById('modAction');
        if (currentStatus === 'suspended' || currentStatus === 'banned') {
            actionSelect.value = 'reactivate';
        } else {
            actionSelect.value = 'suspend';
        }
        toggleDurationField();
        modal.classList.remove('hidden');
    }

    function closeModerateModal() {
        document.getElementById('moderateModal').classList.add('hidden');
    }

    function toggleDurationField() {
        const action = document.getElementById('modAction').value;
        const durationDiv = document.getElementById('durationContainer');
        if (action === 'suspend') {
            durationDiv.classList.remove('hidden');
        } else {
            durationDiv.classList.add('hidden');
        }
    }
</script>
@endsection
