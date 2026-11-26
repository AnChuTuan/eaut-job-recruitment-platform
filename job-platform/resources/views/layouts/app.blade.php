<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title . ' - CVMOI' : 'CVMOI - Nền tảng tuyển dụng & Việc làm chất lượng cao' }}</title>

        <!-- Google Fonts: Montserrat -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet">

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-[#F7FAFC] text-[#142B47] flex flex-col min-h-full selection:bg-[#2A81CB] selection:text-white">
        
        <!-- Navigation Header -->
        <header class="sticky top-0 z-40 bg-white border-b border-gray-200/80 shadow-sm transition-all">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20 items-center">
                    
                    <!-- Left: Brand Logo & Main Navigation -->
                    <div class="flex items-center gap-8">
                        <a href="{{ route('jobs.index') }}" class="flex items-center gap-2 group">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#2A81CB] to-[#142B47] flex items-center justify-center text-white font-bold text-xl shadow-md group-hover:scale-105 transition-transform">
                                C
                            </div>
                            <span class="text-2xl font-black tracking-tight text-[#142B47]">
                                CV<span class="text-[#2A81CB]">MOI</span>
                            </span>
                        </a>

                        <nav class="hidden md:flex items-center gap-1">
                            <a href="{{ route('jobs.index') }}" 
                               class="px-4 py-2 rounded-lg font-medium text-sm transition-colors {{ request()->routeIs('jobs.*') || request()->routeIs('home') ? 'text-[#2A81CB] bg-[#EAF2FA]' : 'text-[#142B47] hover:text-[#2A81CB] hover:bg-[#EAF2FA]/60' }}">
                                Tìm việc làm
                            </a>

                            @auth
                                @if(Auth::user()->isCandidate())
                                    <a href="{{ route('candidate.profile') }}" 
                                       class="px-4 py-2 rounded-lg font-medium text-sm transition-colors {{ request()->routeIs('candidate.profile') ? 'text-[#2A81CB] bg-[#EAF2FA]' : 'text-[#142B47] hover:text-[#2A81CB] hover:bg-[#EAF2FA]/60' }}">
                                        Hồ sơ cá nhân
                                    </a>

                                    <a href="{{ route('candidate.cvs.index') }}" 
                                       class="px-4 py-2 rounded-lg font-medium text-sm transition-colors {{ request()->routeIs('candidate.cvs.*') ? 'text-[#2A81CB] bg-[#EAF2FA]' : 'text-[#142B47] hover:text-[#2A81CB] hover:bg-[#EAF2FA]/60' }}">
                                        Quản lý CV
                                    </a>

                                    <a href="{{ route('candidate.applications.index') }}" 
                                       class="px-4 py-2 rounded-lg font-medium text-sm transition-colors {{ request()->routeIs('candidate.applications.*') ? 'text-[#2A81CB] bg-[#EAF2FA]' : 'text-[#142B47] hover:text-[#2A81CB] hover:bg-[#EAF2FA]/60' }}">
                                        Việc làm đã nộp
                                    </a>
                                @endif
                            @endauth
                        </nav>
                    </div>

                    <!-- Right: Auth / Action Buttons -->
                    <div class="flex items-center gap-4">
                        @auth
                            <!-- Notifications Bell -->
                            <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                                @php
                                    $unreadCount = Auth::user()->unreadNotifications()->count();
                                    $recentNotifications = Auth::user()->notifications()->take(5)->get();
                                @endphp
                                <button @click="open = !open" class="p-2 text-gray-500 hover:text-[#2A81CB] hover:bg-[#EAF2FA] rounded-full relative transition">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                    @if($unreadCount > 0)
                                        <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white">
                                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                        </span>
                                    @endif
                                </button>

                                <!-- Notification Dropdown -->
                                <div x-show="open" 
                                     x-cloak 
                                     x-transition 
                                     class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden z-50">
                                    <div class="px-4 py-3 bg-[#EAF2FA] border-b border-gray-100 flex items-center justify-between">
                                        <h4 class="font-bold text-sm text-[#142B47]">Thông báo</h4>
                                        <span class="text-xs text-gray-500">{{ $unreadCount }} chưa đọc</span>
                                    </div>
                                    <div class="max-h-72 overflow-y-auto divide-y divide-gray-100">
                                        @forelse($recentNotifications as $item)
                                            <div class="p-3 hover:bg-[#F7FAFC] transition">
                                                <p class="font-semibold text-xs text-[#142B47]">{{ $item->title }}</p>
                                                <p class="text-xs text-gray-600 mt-1 line-clamp-2">{{ $item->message }}</p>
                                                <span class="text-[10px] text-gray-400 mt-1 block">{{ $item->created_at->diffForHumans() }}</span>
                                            </div>
                                        @empty
                                            <div class="p-4 text-center text-xs text-gray-500">
                                                Chưa có thông báo nào.
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                            <!-- User Profile Menu -->
                            <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                                <button @click="open = !open" class="flex items-center gap-3 p-1 rounded-full hover:bg-gray-100 transition focus:outline-none">
                                    <div class="w-10 h-10 rounded-full bg-[#EAF2FA] text-[#2A81CB] font-bold flex items-center justify-center border-2 border-[#2A81CB]/30 overflow-hidden">
                                        @if(Auth::user()->candidateProfile && Auth::user()->candidateProfile->avatar_url)
                                            <img src="{{ asset(Auth::user()->candidateProfile->avatar_url) }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                                        @else
                                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                        @endif
                                    </div>
                                    <div class="hidden lg:block text-left">
                                        <span class="block text-sm font-semibold text-[#142B47]">{{ Auth::user()->name }}</span>
                                        <span class="block text-xs text-gray-500 capitalize">{{ Auth::user()->role }}</span>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-400 hidden lg:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <!-- Dropdown Content -->
                                <div x-show="open" 
                                     x-cloak 
                                     x-transition 
                                     class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50">
                                    
                                    @if(Auth::user()->isCandidate())
                                        @php
                                            $score = Auth::user()->candidateProfile ? Auth::user()->candidateProfile->completion_score : 0;
                                        @endphp
                                        <div class="px-4 py-2 border-b border-gray-100">
                                            <div class="flex justify-between items-center text-xs mb-1">
                                                <span class="text-gray-500">Hoàn thiện hồ sơ</span>
                                                <span class="font-bold text-[#2A81CB]">{{ $score }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-100 rounded-full h-1.5">
                                                <div class="bg-[#2A81CB] h-1.5 rounded-full" style="width: {{ $score }}%"></div>
                                            </div>
                                        </div>

                                        <a href="{{ route('candidate.profile') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-[#EAF2FA] hover:text-[#2A81CB] transition">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                            Hồ sơ cá nhân
                                        </a>

                                        <a href="{{ route('candidate.cvs.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-[#EAF2FA] hover:text-[#2A81CB] transition">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                            Quản lý CV (Tối đa 5)
                                        </a>

                                        <a href="{{ route('candidate.applications.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-[#EAF2FA] hover:text-[#2A81CB] transition">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                                            Việc làm đã nộp
                                        </a>
                                    @endif

                                    <div class="border-t border-gray-100 my-1"></div>

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition text-left">
                                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                                            Đăng xuất
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-semibold text-[#142B47] hover:text-[#2A81CB] px-3 py-2 transition">
                                Đăng nhập
                            </a>
                            <a href="{{ route('register') }}" 
                               class="text-sm font-semibold px-4 py-2.5 rounded-xl bg-[#E1EEFA] hover:bg-[#2A81CB] text-[#142B47] hover:text-white transition shadow-sm border border-[#2A81CB]/20">
                                Đăng ký
                            </a>
                        @endauth
                    </div>

                </div>
            </div>
        </header>

        <!-- Flash Notification Messages -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full mt-4">
            @if(session('success'))
                <div class="mb-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between shadow-sm animate-fade-in" x-data="{ show: true }" x-show="show">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-emerald-500 hover:text-emerald-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between shadow-sm animate-fade-in" x-data="{ show: true }" x-show="show">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-rose-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-medium">{{ session('error') }}</span>
                    </div>
                    <button @click="show = false" class="text-rose-500 hover:text-rose-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 shadow-sm animate-fade-in" x-data="{ show: true }" x-show="show">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2 font-semibold text-sm">
                            <svg class="w-5 h-5 text-amber-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            <span>Vui lòng kiểm tra lại thông tin:</span>
                        </div>
                        <button @click="show = false" class="text-amber-500 hover:text-amber-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- Main Body Content -->
        <main class="flex-grow">
            {{ $slot ?? '' }}
            @yield('content')
        </main>

        <!-- Professional Footer (Dark Navy #142B47) -->
        <footer class="bg-[#142B47] text-white mt-20 border-t border-[#142B47]/80">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    
                    <!-- Col 1: About CVMOI -->
                    <div class="space-y-4 md:col-span-1">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-[#2A81CB] flex items-center justify-center font-bold text-white text-base">
                                C
                            </div>
                            <span class="text-xl font-black tracking-tight text-white">
                                CV<span class="text-[#2A81CB]">MOI</span>
                            </span>
                        </div>
                        <p class="text-gray-300 text-xs leading-relaxed">
                            Nền tảng kết nối cơ hội việc làm và tuyển dụng chuyên nghiệp dành cho sinh viên và doanh nghiệp hàng đầu.
                        </p>
                        <div class="text-xs text-gray-400">
                            Hotline: <span class="text-white font-medium">(024) 3783 1234</span>
                        </div>
                    </div>

                    <!-- Col 2: For Candidates -->
                    <div>
                        <h5 class="text-sm font-bold text-white uppercase tracking-wider mb-4 border-b border-white/10 pb-2">Dành cho Ứng viên</h5>
                        <ul class="space-y-2 text-xs text-gray-300">
                            <li><a href="{{ route('jobs.index') }}" class="hover:text-[#2A81CB] transition">Tìm kiếm việc làm</a></li>
                            <li><a href="{{ route('candidate.profile') }}" class="hover:text-[#2A81CB] transition">Tạo & Hoàn thiện hồ sơ</a></li>
                            <li><a href="{{ route('candidate.cvs.index') }}" class="hover:text-[#2A81CB] transition">Quản lý CV (Tối đa 5 CV)</a></li>
                            <li><a href="{{ route('candidate.applications.index') }}" class="hover:text-[#2A81CB] transition">Theo dõi đơn ứng tuyển</a></li>
                        </ul>
                    </div>

                    <!-- Col 3: Popular Categories -->
                    <div>
                        <h5 class="text-sm font-bold text-white uppercase tracking-wider mb-4 border-b border-white/10 pb-2">Ngành nghề phổ biến</h5>
                        <ul class="space-y-2 text-xs text-gray-300">
                            <li><a href="{{ route('jobs.index', ['category' => 'Công nghệ thông tin']) }}" class="hover:text-[#2A81CB] transition">Công nghệ thông tin</a></li>
                            <li><a href="{{ route('jobs.index', ['category' => 'Marketing & Truyền thông']) }}" class="hover:text-[#2A81CB] transition">Marketing & Truyền thông</a></li>
                            <li><a href="{{ route('jobs.index', ['category' => 'Tài chính - Kế toán']) }}" class="hover:text-[#2A81CB] transition">Tài chính - Kế toán</a></li>
                            <li><a href="{{ route('jobs.index', ['category' => 'Thiết kế đồ họa / UI-UX']) }}" class="hover:text-[#2A81CB] transition">Thiết kế đồ họa / UI-UX</a></li>
                        </ul>
                    </div>

                    <!-- Col 4: Top Locations -->
                    <div>
                        <h5 class="text-sm font-bold text-white uppercase tracking-wider mb-4 border-b border-white/10 pb-2">Khu vực nổi bật</h5>
                        <ul class="space-y-2 text-xs text-gray-300">
                            <li><a href="{{ route('jobs.index', ['location' => 'Hà Nội']) }}" class="hover:text-[#2A81CB] transition">Việc làm tại Hà Nội</a></li>
                            <li><a href="{{ route('jobs.index', ['location' => 'TP. Hồ Chí Minh']) }}" class="hover:text-[#2A81CB] transition">Việc làm tại TP. Hồ Chí Minh</a></li>
                            <li><a href="{{ route('jobs.index', ['location' => 'Đà Nẵng']) }}" class="hover:text-[#2A81CB] transition">Việc làm tại Đà Nẵng</a></li>
                            <li><a href="{{ route('jobs.index', ['employment_type' => 'Remote']) }}" class="hover:text-[#2A81CB] transition">Việc làm từ xa (Remote)</a></li>
                        </ul>
                    </div>

                </div>

                <div class="mt-12 pt-8 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between text-xs text-gray-400">
                    <p>&copy; {{ date('Y') }} CVMOI Platform. Phát triển bởi Đoàn (feature/doan).</p>
                    <div class="flex gap-6 mt-4 sm:mt-0">
                        <span class="hover:text-white cursor-pointer">Điều khoản sử dụng</span>
                        <span class="hover:text-white cursor-pointer">Chính sách bảo mật</span>
                        <span class="hover:text-white cursor-pointer">Liên hệ hỗ trợ</span>
                    </div>
                </div>
            </div>
        </footer>

    </body>
</html>
