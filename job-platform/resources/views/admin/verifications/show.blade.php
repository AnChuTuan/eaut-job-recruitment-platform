<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-brand-navy">Hồ sơ xác minh công ty</h2>
    </x-slot>

    <div class="mx-auto max-w-3xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
        @if (session('status'))
            <div class="rounded-md bg-green-50 p-4 text-sm text-green-800">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="rounded-md bg-red-50 p-4 text-sm text-red-800">{{ $errors->first() }}</div>
        @endif

        <section class="rounded-lg bg-white p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-brand-navy">{{ $verification->company->company_name }}</h3>
            <dl class="mt-4 grid gap-4 sm:grid-cols-2">
                <div><dt class="text-sm text-gray-500">Mã số thuế</dt><dd class="mt-1">{{ $verification->company->tax_code ?: '—' }}</dd></div>
                <div><dt class="text-sm text-gray-500">Email</dt><dd class="mt-1">{{ $verification->company->email ?: '—' }}</dd></div>
                <div><dt class="text-sm text-gray-500">Địa chỉ</dt><dd class="mt-1">{{ $verification->company->address ?: '—' }}</dd></div>
                <div><dt class="text-sm text-gray-500">Trạng thái</dt><dd class="mt-1">{{ ['pending' => 'Đang chờ', 'verified' => 'Đã duyệt', 'rejected' => 'Từ chối'][$verification->status] ?? $verification->status }}</dd></div>
            </dl>

            @if ($verification->rejection_reason)
                <p class="mt-4 rounded bg-red-50 p-3 text-sm text-red-800">Lý do từ chối trước đó: {{ $verification->rejection_reason }}</p>
            @endif

            <a href="{{ route('admin.verifications.document', $verification) }}" class="mt-5 inline-block font-medium text-brand-primary hover:text-brand-navy">Tải giấy phép kinh doanh</a>
        </section>

        @if ($verification->status === 'pending')
            <section class="grid gap-4 sm:grid-cols-2">
                <form method="POST" action="{{ route('admin.verifications.approve', $verification) }}" class="rounded-lg bg-white p-6 shadow-sm">
                    @csrf
                    @method('PATCH')
                    <h3 class="font-medium text-gray-900">Duyệt hồ sơ</h3>
                    <p class="mt-2 text-sm text-gray-600">Xác nhận doanh nghiệp đã được kiểm tra hợp lệ.</p>
                    <x-primary-button class="mt-4">Duyệt công ty</x-primary-button>
                </form>
                <form method="POST" action="{{ route('admin.verifications.reject', $verification) }}" class="rounded-lg bg-white p-6 shadow-sm">
                    @csrf
                    @method('PATCH')
                    <h3 class="font-medium text-gray-900">Từ chối hồ sơ</h3>
                    <label for="rejection_reason" class="mt-3 block text-sm text-gray-700">Lý do từ chối *</label>
                    <textarea id="rejection_reason" name="rejection_reason" rows="3" required maxlength="2000" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('rejection_reason') }}</textarea>
                    <x-primary-button class="mt-4">Từ chối</x-primary-button>
                </form>
            </section>
        @endif

        <a href="{{ route('admin.verifications.index') }}" class="inline-block text-sm text-brand-primary hover:text-brand-navy">← Quay lại danh sách</a>
    </div>
</x-app-layout>
