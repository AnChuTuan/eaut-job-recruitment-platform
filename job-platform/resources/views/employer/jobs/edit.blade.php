<x-app-layout>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        .antdd-font { font-family: 'Montserrat', sans-serif; }
    </style>

    <div class="antdd-font min-h-screen bg-[#F7FAFC] py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="bg-[#EAF2FA] border border-[#E1EEFA] rounded-2xl p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-[#2A81CB]">ANTDD • {{ $company->company_name }}</p>
                    <h1 class="text-2xl font-extrabold text-[#142B47] mt-1">Chỉnh sửa tin tuyển dụng</h1>
                    <p class="text-sm text-slate-600 mt-1">{{ $job->title }}</p>
                </div>
                <div>
                    @include('employer.jobs._status_badge', ['status' => $job->status])
                </div>
            </div>

            @if (session('error'))
                <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('employer.jobs.update', $job) }}">
                @csrf
                @method('PUT')
                @include('employer.jobs._form')
            </form>
        </div>
    </div>
</x-app-layout>

