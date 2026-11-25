<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-brand-navy">Xác minh công ty</h2>
    </x-slot>

    <div class="mx-auto max-w-3xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
        @if (session('status'))
            <div class="rounded-md bg-green-50 p-4 text-sm text-green-800">{{ session('status') }}</div>
        @endif

        <div class="rounded-lg bg-white p-6 shadow-sm">
            <h3 class="text-lg font-medium text-brand-navy">{{ $company->company_name }}</h3>
            <p class="mt-2 text-sm text-gray-600">Trạng thái công ty:
                <span class="font-semibold">{{ ['pending' => 'Đang chờ duyệt', 'verified' => 'Đã xác minh', 'rejected' => 'Bị từ chối'][$company->verification_status] ?? $company->verification_status }}</span>
            </p>

            @if ($verification?->status === 'rejected' && $verification->rejection_reason)
                <div class="mt-4 rounded-md bg-red-50 p-4 text-sm text-red-800">
                    Lý do từ chối: {{ $verification->rejection_reason }}
                </div>
            @endif

            <form method="POST" action="{{ route('employer.verification.submit') }}" enctype="multipart/form-data" class="mt-6 space-y-4">
                @csrf
                <div>
                    <x-input-label for="supporting_document" value="Giấy phép kinh doanh (PDF, JPG hoặc PNG; tối đa 10 MB)" />
                    <input id="supporting_document" name="supporting_document" type="file" accept=".pdf,.jpg,.jpeg,.png" required class="mt-1 block w-full text-sm text-gray-700">
                    <x-input-error :messages="$errors->get('supporting_document')" class="mt-2" />
                </div>
                <x-primary-button>{{ $verification?->status === 'pending' ? 'Cập nhật giấy tờ' : 'Gửi xác minh' }}</x-primary-button>
            </form>
        </div>
    </div>
</x-app-layout>
