<x-app-layout>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        .antdd-font { font-family: 'Montserrat', sans-serif; }
    </style>

    @php
        $company = $job->company;
        $actionLabels = [
            'approve_job' => ['label' => 'Duyệt tin', 'classes' => 'bg-emerald-50 text-emerald-700 border border-emerald-200'],
            'reject_job' => ['label' => 'Từ chối tin', 'classes' => 'bg-rose-50 text-rose-700 border border-rose-200'],
            'request_revision_job' => ['label' => 'Yêu cầu chỉnh sửa', 'classes' => 'bg-amber-50 text-amber-700 border border-amber-200'],
            'ban_job' => ['label' => 'Khóa (Ban) tin lừa đảo', 'classes' => 'bg-[#142B47] text-white border border-[#142B47]'],
        ];
    @endphp

    <div class="antdd-font min-h-screen bg-[#F7FAFC] py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Top Moderation Bar --}}
            <div class="bg-[#142B47] text-white rounded-2xl p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 shadow-sm">
                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-1.5">
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider bg-[#2A81CB] text-white">
                            ANTDD Admin Moderation • #{{ $job->id }}
                        </span>
                        @include('admin.moderation._status_badge', ['status' => $job->status])
                    </div>
                    <p class="text-sm text-slate-200">
                        Gửi duyệt lúc {{ $job->updated_at?->format('d/m/Y H:i') }} • Tạo lúc {{ $job->created_at?->format('d/m/Y H:i') }}
                    </p>
                </div>

                <a href="{{ route('admin.moderation.index') }}"
                    class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-xs font-semibold bg-white/10 hover:bg-white/20 text-white transition">
                    ← Quay lại danh sách chờ duyệt
                </a>
            </div>

            {{-- Flash messages --}}
            @if (session('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

                {{-- Left Column: Job Content & Company Info & Audit Logs --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Job Header Card --}}
                    <div class="bg-[#FFFFFF] rounded-2xl border border-[#E1EEFA] p-6 sm:p-8 shadow-sm">
                        <div class="flex flex-wrap items-center gap-2.5 mb-2">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-[#EAF2FA] text-[#2A81CB]">
                                {{ $job->category }}
                            </span>
                            @if ($job->employment_type)
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-[#F7FAFC] text-[#142B47] border border-[#E1EEFA]">
                                    {{ $job->employment_type }}
                                </span>
                            @endif
                        </div>

                        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#142B47]">{{ $job->title }}</h1>
                        <p class="text-sm font-semibold text-[#2A81CB] mt-1">
                            {{ $company?->company_name ?? 'Chưa cập nhật công ty' }}
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-6 pt-6 border-t border-[#E1EEFA]">
                            <div class="bg-[#EAF2FA] rounded-xl p-3.5">
                                <div class="text-xs font-medium text-slate-600">Mức lương</div>
                                <div class="text-sm font-bold text-[#142B47] mt-0.5">
                                    @if ($job->salary_min && $job->salary_max)
                                        {{ number_format((float) $job->salary_min) }} - {{ number_format((float) $job->salary_max) }} {{ $job->currency }}
                                    @elseif ($job->salary_min)
                                        Từ {{ number_format((float) $job->salary_min) }} {{ $job->currency }}
                                    @elseif ($job->salary_max)
                                        Đến {{ number_format((float) $job->salary_max) }} {{ $job->currency }}
                                    @else
                                        Thỏa thuận
                                    @endif
                                </div>
                            </div>

                            <div class="bg-[#EAF2FA] rounded-xl p-3.5">
                                <div class="text-xs font-medium text-slate-600">Địa điểm</div>
                                <div class="text-sm font-bold text-[#142B47] mt-0.5">{{ $job->location }}</div>
                            </div>

                            <div class="bg-[#EAF2FA] rounded-xl p-3.5">
                                <div class="text-xs font-medium text-slate-600">Cấp bậc</div>
                                <div class="text-sm font-bold text-[#142B47] mt-0.5">
                                    {{ $job->job_level ?: 'Chưa cập nhật' }}
                                </div>
                            </div>

                            <div class="bg-[#EAF2FA] rounded-xl p-3.5">
                                <div class="text-xs font-medium text-slate-600">Số lượng • Hạn nộp</div>
                                <div class="text-sm font-bold text-[#142B47] mt-0.5">
                                    {{ $job->quantity }} người • {{ \Illuminate\Support\Carbon::parse($job->deadline)->format('d/m/Y') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Job Details Card --}}
                    <div class="bg-[#FFFFFF] rounded-2xl border border-[#E1EEFA] p-6 sm:p-8 shadow-sm space-y-6">
                        <div>
                            <h2 class="text-lg font-bold text-[#142B47] mb-2">Mô tả công việc</h2>
                            <div class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">{{ $job->description }}</div>
                        </div>

                        @if ($job->requirements)
                            <div class="pt-5 border-t border-slate-100">
                                <h2 class="text-lg font-bold text-[#142B47] mb-2">Yêu cầu ứng viên</h2>
                                <div class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">{{ $job->requirements }}</div>
                            </div>
                        @endif

                        @if ($job->benefits)
                            <div class="pt-5 border-t border-slate-100">
                                <h2 class="text-lg font-bold text-[#142B47] mb-2">Quyền lợi</h2>
                                <div class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">{{ $job->benefits }}</div>
                            </div>
                        @endif

                        @if ($job->skills)
                            <div class="pt-5 border-t border-slate-100">
                                <h2 class="text-lg font-bold text-[#142B47] mb-2">Kỹ năng yêu cầu</h2>
                                <div class="flex flex-wrap gap-2">
                                    @foreach (array_filter(array_map('trim', explode(',', $job->skills))) as $skill)
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-[#EAF2FA] text-[#142B47]">
                                            {{ $skill }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Company Verification Info Card --}}
                    @if ($company)
                        <div class="bg-[#FFFFFF] rounded-2xl border border-[#E1EEFA] p-6 sm:p-8 shadow-sm">
                            <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
                                <h2 class="text-lg font-bold text-[#142B47]">Thông tin doanh nghiệp đăng tin</h2>
                                @if ($company->verification_status === 'verified')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Đã xác minh
                                    </span>
                                @elseif ($company->verification_status === 'rejected')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        Xác minh bị từ chối
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        Chưa xác minh
                                    </span>
                                @endif
                            </div>

                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <dt class="text-xs font-medium text-slate-500">Tên công ty</dt>
                                    <dd class="font-semibold text-[#142B47] mt-0.5">{{ $company->company_name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium text-slate-500">Mã số thuế</dt>
                                    <dd class="font-semibold text-[#142B47] mt-0.5">{{ $company->tax_code ?: 'Chưa cập nhật' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium text-slate-500">Email liên hệ</dt>
                                    <dd class="font-semibold text-[#142B47] mt-0.5">{{ $company->email ?: $company->user?->email ?: 'Chưa cập nhật' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium text-slate-500">Số điện thoại</dt>
                                    <dd class="font-semibold text-[#142B47] mt-0.5">{{ $company->phone ?: 'Chưa cập nhật' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium text-slate-500">Địa chỉ</dt>
                                    <dd class="font-semibold text-[#142B47] mt-0.5">{{ $company->address ?: 'Chưa cập nhật' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium text-slate-500">Website</dt>
                                    <dd class="font-semibold text-[#2A81CB] mt-0.5 break-all">{{ $company->website ?: 'Chưa cập nhật' }}</dd>
                                </div>
                            </dl>
                        </div>
                    @endif

                    {{-- Audit Logs History Card --}}
                    <div class="bg-[#FFFFFF] rounded-2xl border border-[#E1EEFA] p-6 sm:p-8 shadow-sm">
                        <h2 class="text-lg font-bold text-[#142B47] mb-4">Lịch sử kiểm duyệt (Audit Logs)</h2>

                        @if ($auditLogs->isEmpty())
                            <p class="text-sm text-slate-500">Chưa có thao tác kiểm duyệt nào được ghi nhận cho tin tuyển dụng này.</p>
                        @else
                            <div class="space-y-3">
                                @foreach ($auditLogs as $log)
                                    @php
                                        $actionMeta = $actionLabels[$log->action] ?? [
                                            'label' => $log->action,
                                            'classes' => 'bg-[#EAF2FA] text-[#142B47] border border-[#E1EEFA]',
                                        ];
                                    @endphp
                                    <div class="rounded-xl border border-[#E1EEFA] bg-[#F7FAFC] p-4">
                                        <div class="flex flex-wrap items-center justify-between gap-2">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $actionMeta['classes'] }}">
                                                {{ $actionMeta['label'] }}
                                            </span>
                                            <span class="text-xs text-slate-500">
                                                {{ $log->created_at?->format('d/m/Y H:i:s') }}
                                            </span>
                                        </div>
                                        <div class="text-xs font-semibold text-[#142B47] mt-2">
                                            Admin thực hiện: {{ $log->admin?->name ?? 'Admin #' . $log->admin_id }}
                                        </div>
                                        @if ($log->reason)
                                            <div class="text-sm text-slate-700 mt-1.5 bg-white rounded-lg border border-slate-200 px-3 py-2">
                                                <span class="font-semibold text-[#142B47]">Lý do / Ghi chú:</span> {{ $log->reason }}
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Right Column: Moderation Action Panels --}}
                <div class="space-y-5 lg:sticky lg:top-6">

                    @if ($job->status === 'banned')
                        <div class="bg-[#142B47] text-white rounded-2xl p-6 shadow-sm">
                            <h2 class="text-base font-extrabold mb-1">Tin đã bị khóa vĩnh viễn (Ban)</h2>
                            <p class="text-xs text-slate-200 leading-relaxed">
                                Tin tuyển dụng này đã được đánh dấu vi phạm / lừa đảo và bị khóa. Không thể thực hiện duyệt, chỉnh sửa hay từ chối lại.
                            </p>
                        </div>
                    @else
                        {{-- 1. Approve Form --}}
                        @if ($job->status !== 'approved')
                            <div class="bg-[#FFFFFF] rounded-2xl border border-[#E1EEFA] p-5 shadow-sm">
                                <h2 class="text-base font-bold text-[#142B47] mb-1">1. Duyệt tin tuyển dụng</h2>
                                <p class="text-xs text-slate-500 mb-3">
                                    Cho phép tin tuyển dụng hiển thị công khai cho ứng viên trên ANTDD.
                                </p>
                                <form method="POST" action="{{ route('admin.moderation.approve', $job) }}" class="space-y-3">
                                    @csrf
                                    @method('PATCH')
                                    <div>
                                        <label for="approve-reason" class="block text-xs font-semibold text-[#142B47] mb-1">
                                            Ghi chú duyệt (không bắt buộc)
                                        </label>
                                        <textarea id="approve-reason" name="reason" rows="2"
                                            placeholder="Ví dụ: Nội dung rõ ràng, doanh nghiệp hợp lệ..."
                                            class="w-full rounded-lg border-slate-300 bg-[#F7FAFC] text-xs text-[#142B47] focus:border-[#2A81CB] focus:ring-[#2A81CB]">{{ old('reason') }}</textarea>
                                        @if ($errors->approve->has('reason'))
                                            <p class="text-xs text-rose-600 mt-1">{{ $errors->approve->first('reason') }}</p>
                                        @endif
                                    </div>
                                    <button type="submit"
                                        class="w-full py-2.5 px-4 rounded-xl bg-[#2A81CB] hover:bg-[#142B47] text-white text-sm font-bold shadow-sm transition">
                                        ✓ Duyệt tin (Approve)
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5 text-emerald-900">
                                <h2 class="text-sm font-bold mb-1">Tin đang ở trạng thái Đã duyệt</h2>
                                <p class="text-xs text-emerald-700">
                                    Nếu phát hiện vấn đề mới, bạn vẫn có thể yêu cầu chỉnh sửa, từ chối hoặc khóa tin lừa đảo bên dưới.
                                </p>
                            </div>
                        @endif

                        {{-- 2. Request Revision Form --}}
                        <div class="bg-[#FFFFFF] rounded-2xl border border-amber-200 p-5 shadow-sm">
                            <h2 class="text-base font-bold text-[#142B47] mb-1">2. Yêu cầu chỉnh sửa</h2>
                            <p class="text-xs text-slate-500 mb-3">
                                Trả tin về trạng thái cần chỉnh sửa để nhà tuyển dụng bổ sung thông tin.
                            </p>
                            <form method="POST" action="{{ route('admin.moderation.request-revision', $job) }}" class="space-y-3">
                                @csrf
                                @method('PATCH')
                                <div>
                                    <label for="revision-reason" class="block text-xs font-semibold text-[#142B47] mb-1">
                                        Lý do yêu cầu chỉnh sửa <span class="text-rose-600">*</span>
                                    </label>
                                    <textarea id="revision-reason" name="reason" rows="3" required
                                        placeholder="Nhập rõ nội dung nhà tuyển dụng cần sửa (tối thiểu 5 ký tự)..."
                                        class="w-full rounded-lg border-slate-300 bg-[#F7FAFC] text-xs text-[#142B47] focus:border-amber-500 focus:ring-amber-500">{{ $errors->revision->any() ? old('reason') : '' }}</textarea>
                                    @if ($errors->revision->has('reason'))
                                        <p class="text-xs text-rose-600 font-medium mt-1">{{ $errors->revision->first('reason') }}</p>
                                    @endif
                                </div>
                                <button type="submit"
                                    class="w-full py-2.5 px-4 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold shadow-sm transition">
                                    ✎ Yêu cầu chỉnh sửa
                                </button>
                            </form>
                        </div>

                        {{-- 3. Reject Form --}}
                        <div class="bg-[#FFFFFF] rounded-2xl border border-rose-200 p-5 shadow-sm">
                            <h2 class="text-base font-bold text-[#142B47] mb-1">3. Từ chối tin tuyển dụng</h2>
                            <p class="text-xs text-slate-500 mb-3">
                                Từ chối tin không đạt tiêu chuẩn đăng tuyển. Bắt buộc nhập lý do.
                            </p>
                            <form method="POST" action="{{ route('admin.moderation.reject', $job) }}" class="space-y-3">
                                @csrf
                                @method('PATCH')
                                <div>
                                    <label for="reject-reason" class="block text-xs font-semibold text-[#142B47] mb-1">
                                        Lý do từ chối <span class="text-rose-600">*</span>
                                    </label>
                                    <textarea id="reject-reason" name="reason" rows="3" required
                                        placeholder="Nhập lý do từ chối tin tuyển dụng (tối thiểu 5 ký tự)..."
                                        class="w-full rounded-lg border-slate-300 bg-[#F7FAFC] text-xs text-[#142B47] focus:border-rose-500 focus:ring-rose-500">{{ $errors->reject->any() ? old('reason') : '' }}</textarea>
                                    @if ($errors->reject->has('reason'))
                                        <p class="text-xs text-rose-600 font-medium mt-1">{{ $errors->reject->first('reason') }}</p>
                                    @endif
                                </div>
                                <button type="submit"
                                    class="w-full py-2.5 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-sm font-bold shadow-sm transition">
                                    ✕ Từ chối tin (Reject)
                                </button>
                            </form>
                        </div>

                        {{-- 4. Ban Scam Job Form --}}
                        <div class="bg-[#FFFFFF] rounded-2xl border-2 border-[#142B47] p-5 shadow-sm">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="px-2 py-0.5 rounded bg-[#142B47] text-white text-[10px] font-bold uppercase">Cảnh báo</span>
                                <h2 class="text-base font-extrabold text-[#142B47]">4. Khóa / Ban tin lừa đảo</h2>
                            </div>
                            <p class="text-xs text-slate-600 mb-3">
                                Khóa vĩnh viễn tin có dấu hiệu lừa đảo, đa cấp, thu phí trái phép. Nhà tuyển dụng sẽ không thể chỉnh sửa hay gửi lại tin này.
                            </p>
                            <form method="POST" action="{{ route('admin.moderation.ban', $job) }}"
                                onsubmit="return confirm('Xác nhận KHÓA VĨNH VIỄN (Ban) tin tuyển dụng này? Hành động này không thể hoàn tác.');"
                                class="space-y-3">
                                @csrf
                                @method('PATCH')
                                <div>
                                    <label for="ban-reason" class="block text-xs font-semibold text-[#142B47] mb-1">
                                        Lý do khóa / dấu hiệu lừa đảo <span class="text-rose-600">*</span>
                                    </label>
                                    <textarea id="ban-reason" name="reason" rows="3" required
                                        placeholder="Ghi rõ bằng chứng hoặc dấu hiệu lừa đảo (tối thiểu 5 ký tự)..."
                                        class="w-full rounded-lg border-slate-300 bg-[#F7FAFC] text-xs text-[#142B47] focus:border-[#142B47] focus:ring-[#142B47]">{{ $errors->ban->any() ? old('reason') : '' }}</textarea>
                                    @if ($errors->ban->has('reason'))
                                        <p class="text-xs text-rose-600 font-medium mt-1">{{ $errors->ban->first('reason') }}</p>
                                    @endif
                                </div>
                                <button type="submit"
                                    class="w-full py-2.5 px-4 rounded-xl bg-[#142B47] hover:bg-slate-900 text-white text-sm font-bold shadow-sm transition">
                                    ⛔ Khóa vĩnh viễn (Ban)
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

