<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Личный кабинет | Buyer Greece' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex flex-col min-h-full text-slate-800 font-sans antialiased">

    <!-- CABINET NAVIGATION HEADER -->
    <header x-data="{ userMenuOpen: false, mobileNavOpen: false }" class="bg-slate-900 text-white sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                <!-- Brand / Logo -->
                <div class="flex items-center gap-6">
                    <a href="{{ route('dashboard') }}" class="font-extrabold text-lg tracking-tight text-white flex items-center gap-1.5">
                        <span class="text-brand-500">Buyer</span>Greece
                        <span class="text-xs font-normal text-slate-400 bg-slate-800 px-2 py-0.5 rounded ml-1">Кабинет</span>
                    </a>

                    <!-- Desktop Cabinet Navigation -->
                    <nav class="hidden md:flex items-center space-x-1 text-sm font-medium">
                        <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' }}">
                            Дашборд
                        </a>
                        <a href="{{ route('orders.index') }}" class="px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('orders.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' }}">
                            Заказы
                        </a>
                        <a href="{{ route('packages.index') }}" class="px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('packages.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' }}">
                            Посылки
                        </a>
                        <a href="{{ route('shipments.index') }}" class="px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('shipments.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:text-white hover:bg-slate-800/50' }}">
                            Отправки Camex
                        </a>
                    </nav>
                </div>

                <!-- Right Actions & Profile Menu -->
                <div class="hidden md:flex items-center gap-4">
                    <a href="{{ route('buy-for-me') }}" class="px-3 py-1.5 bg-brand-600 hover:bg-brand-500 text-white font-semibold text-xs rounded-lg transition-colors">
                        + Выкупите за меня
                    </a>

                    <!-- Profile Dropdown -->
                    <div class="relative" @click.away="userMenuOpen = false">
                        <button @click="userMenuOpen = !userMenuOpen" type="button" class="flex items-center gap-2 text-sm font-medium text-slate-200 hover:text-white focus:outline-none">
                            <span class="w-8 h-8 rounded-full bg-slate-700 text-brand-400 font-bold flex items-center justify-center text-xs">
                                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                            </span>
                            <span class="max-w-[120px] truncate">{{ Auth::user()->name ?? 'Клиент' }}</span>
                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="userMenuOpen" x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-200 py-1 text-slate-700 text-xs z-50">
                            <a href="{{ route('profile.show') }}" class="block px-4 py-2 hover:bg-slate-50 font-medium">Мой профиль</a>
                            <a href="{{ route('help') }}" class="block px-4 py-2 hover:bg-slate-50">Помощь / FAQ</a>
                            <a href="{{ route('home') }}" class="block px-4 py-2 hover:bg-slate-50 border-t border-slate-100">Перейти на сайт</a>
                            <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-rose-600 hover:bg-rose-50 font-medium">Выйти</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex md:hidden items-center">
                    <button @click="mobileNavOpen = !mobileNavOpen" type="button" class="text-slate-400 hover:text-white p-2">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>

            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div x-show="mobileNavOpen" x-cloak class="md:hidden border-t border-slate-800 bg-slate-900 px-4 py-4 space-y-2">
            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-slate-200 hover:bg-slate-800">Дашборд</a>
            <a href="{{ route('orders.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-slate-200 hover:bg-slate-800">Мои заказы</a>
            <a href="{{ route('packages.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-slate-200 hover:bg-slate-800">Посылки</a>
            <a href="{{ route('shipments.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-slate-200 hover:bg-slate-800">Отправки Camex</a>
            <a href="{{ route('profile.show') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-slate-200 hover:bg-slate-800 border-t border-slate-800 pt-3">Профиль</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-3 py-2 text-rose-400 font-medium text-sm">Выйти</button>
            </form>
        </div>
    </header>

    <!-- SYSTEM ALERTS / MESSAGES -->
    @if(session('success'))
        <div class="bg-emerald-500 text-white text-xs font-semibold py-2.5 px-4 text-center shadow-inner">
            {{ session('success') }}
        </div>
    @endif

    <!-- MAIN CABINET CONTENT -->
    <main class="flex-grow py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{ $slot }}
        </div>
    </main>

    <!-- FOOTER -->
    <footer class="bg-white border-t border-slate-200 py-6 text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row justify-between items-center gap-3">
            <div>
                © {{ date('Y') }} Buyer Greece • Личный кабинет
            </div>
            <div class="flex gap-4">
                <a href="{{ route('help') }}" class="hover:text-slate-800">Центр помощи</a>
                <span>•</span>
                <a href="https://t.me/your_support_bot" target="_blank" class="hover:text-brand-600">Telegram Support</a>
            </div>
        </div>
    </footer>

</body>
</html>