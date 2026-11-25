<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-brand-navy">Hồ sơ công ty</h2>
    </x-slot>

    <div class="mx-auto max-w-4xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
        @if (session('status'))
            <div class="rounded-md bg-green-50 p-4 text-sm text-green-800">{{ session('status') }}</div>
        @endif

        <div class="rounded-lg bg-white p-6 shadow-sm">
            <form method="POST" action="{{ route('employer.company.update') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <x-input-label for="company_name" value="Tên công ty *" />
                    <x-text-input id="company_name" name="company_name" class="mt-1 block w-full" :value="old('company_name', $company?->company_name)" required maxlength="200" />
                    <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <x-input-label for="tax_code" value="Mã số thuế" />
                        <x-text-input id="tax_code" name="tax_code" class="mt-1 block w-full" :value="old('tax_code', $company?->tax_code)" maxlength="50" />
                        <x-input-error :messages="$errors->get('tax_code')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="industry" value="Lĩnh vực" />
                        <x-text-input id="industry" name="industry" class="mt-1 block w-full" :value="old('industry', $company?->industry)" maxlength="100" />
                        <x-input-error :messages="$errors->get('industry')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="email" value="Email công ty" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $company?->email)" maxlength="150" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="phone" value="Số điện thoại" />
                        <x-text-input id="phone" name="phone" type="tel" class="mt-1 block w-full" :value="old('phone', $company?->phone)" maxlength="20" />
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="website" value="Website" />
                        <x-text-input id="website" name="website" type="url" class="mt-1 block w-full" :value="old('website', $company?->website)" placeholder="https://" />
                        <x-input-error :messages="$errors->get('website')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="company_size" value="Quy mô công ty" />
                        <x-text-input id="company_size" name="company_size" class="mt-1 block w-full" :value="old('company_size', $company?->company_size)" maxlength="50" placeholder="Ví dụ: 50-100 nhân viên" />
                        <x-input-error :messages="$errors->get('company_size')" class="mt-2" />
                    </div>
                </div>

                <div>
                    <x-input-label for="address" value="Địa chỉ" />
                    <x-text-input id="address" name="address" class="mt-1 block w-full" :value="old('address', $company?->address)" maxlength="255" />
                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="description" value="Giới thiệu công ty" />
                    <textarea id="description" name="description" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-primary focus:ring-brand-primary">{{ old('description', $company?->description) }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="logo" value="Logo (PNG/JPG, tối đa 2 MB)" />
                    <input id="logo" name="logo" type="file" accept="image/*" class="mt-1 block w-full text-sm text-gray-700">
                    <x-input-error :messages="$errors->get('logo')" class="mt-2" />
                    @if ($company?->logo_url)
                        <img src="{{ asset('storage/'.$company->logo_url) }}" alt="Logo {{ $company->company_name }}" class="mt-3 h-20 w-20 rounded border object-contain">
                    @endif
                </div>

                <div class="flex flex-wrap gap-3">
                    <x-primary-button>Lưu hồ sơ</x-primary-button>
                    <a href="{{ route('employer.verification.create') }}" class="inline-flex items-center rounded-md border border-brand-accent px-4 py-2 text-sm text-brand-navy hover:bg-brand-accent">Xác minh công ty</a>
                    <a href="{{ route('employer.applicants.index') }}" class="inline-flex items-center rounded-md border border-brand-accent px-4 py-2 text-sm text-brand-navy hover:bg-brand-accent">Quản lý ứng viên</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
