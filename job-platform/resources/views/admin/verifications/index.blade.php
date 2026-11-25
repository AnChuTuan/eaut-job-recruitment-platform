<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-brand-navy">Duyệt xác minh công ty</h2>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-5 px-4 py-8 sm:px-6 lg:px-8">
        <form method="GET" class="flex items-end gap-3 rounded-lg bg-white p-4 shadow-sm">
            <div>
                <x-input-label for="status" value="Trạng thái" />
                <select id="status" name="status" class="mt-1 rounded-md border-gray-300 shadow-sm">
                    <option value="">Tất cả</option>
                    @foreach (['pending' => 'Đang chờ', 'verified' => 'Đã duyệt', 'rejected' => 'Từ chối'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <x-primary-button>Lọc</x-primary-button>
        </form>

        <div class="overflow-hidden rounded-lg bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                        <tr><th class="px-6 py-3">Công ty</th><th class="px-6 py-3">Mã số thuế</th><th class="px-6 py-3">Gửi lúc</th><th class="px-6 py-3">Trạng thái</th><th class="px-6 py-3"></th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
                        @forelse ($verifications as $verification)
                            <tr>
                                <td class="px-6 py-4 font-medium text-brand-navy">{{ $verification->company->company_name }}</td>
                                <td class="px-6 py-4">{{ $verification->company->tax_code ?: '—' }}</td>
                                <td class="px-6 py-4">{{ $verification->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4">{{ ['pending' => 'Đang chờ', 'verified' => 'Đã duyệt', 'rejected' => 'Từ chối'][$verification->status] ?? $verification->status }}</td>
                                <td class="px-6 py-4"><a class="font-medium text-brand-primary hover:text-brand-navy" href="{{ route('admin.verifications.show', $verification) }}">Xem hồ sơ</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">Không có hồ sơ xác minh.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-gray-200 px-6 py-4">{{ $verifications->links() }}</div>
        </div>
    </div>
</x-app-layout>
