<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-brand-navy">Quản lý ứng viên</h2>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-5 px-4 py-8 sm:px-6 lg:px-8">
        @if (session('status'))
            <div class="rounded-md bg-green-50 p-4 text-sm text-green-800">{{ session('status') }}</div>
        @endif

        <form method="GET" class="flex flex-wrap items-end gap-3 rounded-lg bg-white p-4 shadow-sm">
            <div class="min-w-60 flex-1">
                <x-input-label for="search" value="Tìm ứng viên hoặc vị trí" />
                <x-text-input id="search" name="search" class="mt-1 block w-full" :value="request('search')" />
            </div>
            <div>
                <x-input-label for="status" value="Trạng thái" />
                <select id="status" name="status" class="mt-1 rounded-md border-gray-300 shadow-sm">
                    <option value="">Tất cả</option>
                    @foreach (['pending' => 'Mới', 'reviewing' => 'Đang xem xét', 'shortlisted' => 'Phù hợp', 'rejected' => 'Từ chối', 'hired' => 'Đã tuyển'] as $value => $label)
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
                        <tr>
                            <th class="px-6 py-3">Ứng viên</th>
                            <th class="px-6 py-3">Vị trí</th>
                            <th class="px-6 py-3">Ngày nộp</th>
                            <th class="px-6 py-3">Trạng thái</th>
                            <th class="px-6 py-3">Chi tiết</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
                        @forelse ($applications as $application)
                            <tr>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="font-medium text-brand-navy">{{ $application->candidate->name }}</div>
                                    <div>{{ $application->candidate->email }}</div>
                                </td>
                                <td class="px-6 py-4">{{ $application->job->title }}</td>
                                <td class="whitespace-nowrap px-6 py-4">{{ $application->created_at->format('d/m/Y') }}</td>
                                <td class="whitespace-nowrap px-6 py-4">{{ ['pending' => 'Mới', 'reviewing' => 'Đang xem xét', 'shortlisted' => 'Phù hợp', 'rejected' => 'Từ chối', 'hired' => 'Đã tuyển'][$application->status] ?? $application->status }}</td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <a class="font-medium text-brand-primary hover:text-brand-navy" href="{{ route('employer.applicants.show', $application) }}">Xem hồ sơ</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">Chưa có hồ sơ ứng tuyển phù hợp.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-gray-200 px-6 py-4">{{ $applications->links() }}</div>
        </div>
    </div>
</x-app-layout>
