<x-app-layout>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        .antdd-font { font-family: 'Montserrat', sans-serif; }
    </style>

    <div class="antdd-font min-h-screen bg-[#F7FAFC] py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="bg-[#EAF2FA] border border-[#E1EEFA] rounded-2xl p-6 flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-[#2A81CB]">ANTDD • {{ $company->company_name }}</p>
                    <h1 class="text-2xl font-extrabold text-[#142B47] mt-1">Đăng tin tuyển dụng mới</h1>
                    <p class="text-sm text-slate-600 mt-1">Bạn có thể lưu nháp, xem trước hoặc gửi duyệt ngay.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('employer.jobs.store') }}">
                @csrf
                @include('employer.jobs._form')
            </form>
        </div>
    </div>
</x-app-layout>

