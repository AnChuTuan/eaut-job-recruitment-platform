<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-brand-navy">Hồ sơ ứng viên</h2>
    </x-slot>

    <div class="mx-auto max-w-4xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
        @if (session('status'))
            <div class="rounded-md bg-green-50 p-4 text-sm text-green-800">{{ session('status') }}</div>
        @endif

        <section class="rounded-lg bg-white p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-brand-navy">{{ $application->candidate->name }}</h3>
            <dl class="mt-4 grid gap-4 sm:grid-cols-2">
                <div><dt class="text-sm text-gray-500">Email</dt><dd class="mt-1">{{ $application->candidate->email }}</dd></div>
                <div><dt class="text-sm text-gray-500">Điện thoại</dt><dd class="mt-1">{{ $application->candidate->phone ?: 'Chưa cập nhật' }}</dd></div>
                <div><dt class="text-sm text-gray-500">Vị trí ứng tuyển</dt><dd class="mt-1">{{ $application->job->title }}</dd></div>
                <div><dt class="text-sm text-gray-500">Ngày nộp</dt><dd class="mt-1">{{ $application->created_at->format('d/m/Y H:i') }}</dd></div>
            </dl>
            @if ($application->cover_letter)
                <div class="mt-5">
                    <h4 class="text-sm font-medium text-gray-900">Thư giới thiệu</h4>
                    <p class="mt-2 whitespace-pre-line text-sm text-gray-700">{{ $application->cover_letter }}</p>
                </div>
            @endif
            <div class="mt-5">
                @if ($application->cv_url)
                    <a href="{{ route('employer.applicants.cv', $application) }}" class="font-medium text-brand-primary hover:text-brand-navy">Tải CV ứng viên</a>
                @else
                    <span class="text-sm text-gray-500">Ứng viên chưa đính kèm CV.</span>
                @endif
            </div>
        </section>

        <section class="rounded-lg bg-white p-6 shadow-sm">
            <h3 class="font-medium text-gray-900">Cập nhật trạng thái</h3>
            <form method="POST" action="{{ route('employer.applicants.status', $application) }}" class="mt-4 flex flex-wrap items-end gap-3">
                @csrf
                @method('PATCH')
                <div>
                    <x-input-label for="status" value="Trạng thái" />
                    <select id="status" name="status" class="mt-1 rounded-md border-gray-300 shadow-sm">
                        @foreach (['pending' => 'Mới', 'reviewing' => 'Đang xem xét', 'shortlisted' => 'Phù hợp', 'rejected' => 'Từ chối', 'hired' => 'Đã tuyển'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $application->status) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <x-primary-button>Lưu trạng thái</x-primary-button>
            </form>
            <x-input-error :messages="$errors->get('status')" class="mt-2" />
            <a href="{{ route('employer.applicants.index') }}" class="mt-5 inline-block text-sm text-brand-primary hover:text-brand-navy">← Quay lại danh sách</a>
        </section>
    </div>
</x-app-layout>
