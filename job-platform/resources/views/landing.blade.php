<!DOCTYPE html>
<html lang="vi">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>ANTDD | Nền tảng tuyển dụng</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=montserrat:400,500,600,700&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-brand-canvas">
            <header class="border-b border-brand-accent bg-white">
                <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                    <a href="{{ url('/') }}" aria-label="ANTDD trang chủ">
                        <x-application-logo class="text-xl" />
                    </a>
                    <nav class="flex items-center gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}" class="rounded-lg px-4 py-2 text-sm font-semibold text-brand-navy hover:bg-brand-accent">Trang quản lý</a>
                        @else
                            <a href="{{ route('login') }}" class="rounded-lg px-4 py-2 text-sm font-semibold text-brand-navy hover:bg-brand-accent">Đăng nhập</a>
                            <a href="{{ route('register') }}" class="rounded-lg bg-brand-accent px-4 py-2 text-sm font-semibold text-brand-navy transition hover:bg-brand-primary hover:text-white">Đăng ký</a>
                        @endauth
                    </nav>
                </div>
            </header>

            <main>
                <section class="bg-brand-section">
                    <div class="mx-auto grid max-w-7xl items-center gap-12 px-4 py-16 sm:px-6 sm:py-24 lg:grid-cols-2 lg:px-8">
                        <div>
                            <span class="inline-flex rounded-full bg-brand-accent px-4 py-2 text-xs font-bold uppercase tracking-widest text-brand-primary">Nền tảng tuyển dụng EAUT</span>
                            <h1 class="mt-6 max-w-2xl text-4xl font-bold leading-tight text-brand-navy sm:text-5xl">
                                Kết nối cơ hội nghề nghiệp với <span class="text-brand-primary">nhân tài phù hợp</span>
                            </h1>
                            <p class="mt-6 max-w-xl text-lg leading-8 text-brand-navy/75">
                                ANTDD đồng hành cùng ứng viên trên hành trình tìm việc và giúp doanh nghiệp tiếp cận những ứng viên tiềm năng.
                            </p>
                            <div class="mt-8 flex flex-wrap gap-4">
                                @guest
                                    <a href="{{ route('register') }}" class="rounded-lg bg-brand-primary px-6 py-3 font-semibold text-white shadow-sm transition hover:bg-brand-navy">Bắt đầu ngay</a>
                                    <a href="{{ route('login') }}" class="rounded-lg border border-brand-accent bg-white px-6 py-3 font-semibold text-brand-navy transition hover:bg-brand-accent">Đăng nhập</a>
                                @else
                                    <a href="{{ route('dashboard') }}" class="rounded-lg bg-brand-primary px-6 py-3 font-semibold text-white shadow-sm transition hover:bg-brand-navy">Mở trang quản lý</a>
                                @endguest
                            </div>
                        </div>

                        <div class="rounded-3xl border border-white bg-white p-6 shadow-xl shadow-brand-navy/10 sm:p-8">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-brand-primary">ANTDD CAREER</p>
                                    <h2 class="mt-2 text-2xl font-bold text-brand-navy">Cơ hội tiếp theo bắt đầu từ đây</h2>
                                </div>
                                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-accent text-xl font-bold text-brand-primary">A</span>
                            </div>
                            <div class="mt-8 space-y-4">
                                <div class="rounded-xl border border-brand-accent bg-brand-canvas p-4">
                                    <p class="font-semibold text-brand-navy">Dành cho ứng viên</p>
                                    <p class="mt-1 text-sm leading-6 text-brand-navy/70">Tạo tài khoản để xây dựng hồ sơ và sẵn sàng ứng tuyển.</p>
                                </div>
                                <div class="rounded-xl border border-brand-accent bg-brand-canvas p-4">
                                    <p class="font-semibold text-brand-navy">Dành cho doanh nghiệp</p>
                                    <p class="mt-1 text-sm leading-6 text-brand-navy/70">Quản lý thông tin công ty và theo dõi hồ sơ ứng viên.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="mx-auto max-w-7xl px-4 py-12 text-center sm:px-6 lg:px-8">
                    <p class="text-sm font-semibold uppercase tracking-widest text-brand-primary">ANTDD</p>
                    <h2 class="mt-3 text-2xl font-bold text-brand-navy">Tìm đúng cơ hội. Kết nối đúng nhân tài.</h2>
                </section>
            </main>

            <footer class="border-t border-brand-accent bg-white">
                <div class="mx-auto max-w-7xl px-4 py-5 text-sm text-brand-navy/70 sm:px-6 lg:px-8">
                    © {{ date('Y') }} ANTDD — Nền tảng tuyển dụng và tìm kiếm việc làm EAUT.
                </div>
            </footer>
        </div>
    </body>
</html>
