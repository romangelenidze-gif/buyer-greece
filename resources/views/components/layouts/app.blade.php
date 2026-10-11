<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#f2f2f2]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- STRICT NOINDEX FOR CUSTOMER CABINET -->
    <meta name="robots" content="noindex, nofollow">

    <title>{{ $title ?? __('Личный кабинет') . ' | Buyer Greece' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex flex-col min-h-full text-[#1d1d1f] antialiased font-sans bg-[#f2f2f2]">

    <!-- CABINET NAVIGATION HEADER -->
    <header x-data="{ sidebarOpen: false }" class="bg-[#1d1d1f] text-white sticky top-0 z-45 border-b border-white/10">
        <div class="px-4 sm:px-6 lg:px-8 w-full">
            <div class="flex items-center justify-between h-16">
                
                <!-- Logo (Extreme Left) -->
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="font-bold text-lg tracking-tight text-white flex items-center gap-1.5">
                        <span class="text-[#0071e3]">Buyer</span>Greece
                        <span class="hidden sm:inline-block text-xs font-normal text-gray-400 bg-white/10 px-2.5 py-0.5 rounded-full ml-1">{{ __('Личный кабинет') }}</span>
                    </a>
                </div>

                <!-- Right Actions: Profile & Hamburger (Extreme Right) -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('profile.show') }}" class="hidden sm:flex items-center gap-2 text-xs font-medium text-gray-300 hover:text-white bg-white/5 hover:bg-white/10 px-3.5 py-1.5 rounded-full border border-white/10 transition-colors">
                        <span class="w-5 h-5 rounded-full bg-[#0071e3] text-white font-bold flex items-center justify-center text-[10px]">
                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                        </span>
                        <span class="max-w-[120px] truncate">{{ Auth::user()->name ?? __('Клиент') }}</span>
                    </a>

                    <!-- Hamburger Button -->
                    <button @click="sidebarOpen = true" type="button" class="p-2 text-gray-300 hover:text-white rounded-xl hover:bg-white/5 transition-colors focus:outline-none cursor-pointer" aria-label="Открыть меню">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>

            </div>
        </div>

        <!-- RIGHT SIDEBAR / APPLE DRAWER -->
        <!-- Backdrop Blur -->
        <div 
            x-show="sidebarOpen" 
            x-transition:enter="transition-opacity ease-linear duration-300" 
            x-transition:enter-start="opacity-0" 
            x-transition:enter-end="opacity-100" 
            x-transition:leave="transition-opacity ease-linear duration-300" 
            x-transition:leave-start="opacity-100" 
            x-transition:leave-end="opacity-0" 
            x-cloak 
            class="fixed inset-0 z-50 bg-black/60 backdrop-blur-md"
            @click="sidebarOpen = false"
        ></div>

        <!-- Sidebar Panel Sliding from Right -->
        <div 
            x-show="sidebarOpen" 
            x-transition:enter="transition ease-in-out duration-300 transform" 
            x-transition:enter-start="translate-x-full" 
            x-transition:enter-end="translate-x-0" 
            x-transition:leave="transition ease-in-out duration-300 transform" 
            x-transition:leave-start="translate-x-0" 
            x-transition:leave-end="translate-x-full" 
            x-cloak 
            class="fixed inset-y-0 right-0 z-50 w-80 bg-[#1d1d1f]/95 backdrop-blur-2xl border-l border-white/10 shadow-2xl flex flex-col justify-between py-5 px-5"
        >
            <!-- Top: User info & Close Button beside it -->
            <div class="space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-white/10 gap-2">
                    <div class="flex items-center gap-2.5 flex-grow min-w-0">
                        <span class="w-8 h-8 rounded-full bg-[#0071e3] text-white font-bold flex items-center justify-center text-xs shrink-0">
                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <div class="text-xs font-semibold text-white truncate">{{ Auth::user()->name ?? __('Клиент') }}</div>
                            <div class="text-[10px] text-gray-400 truncate">{{ Auth::user()->email ?? '' }}</div>
                        </div>
                    </div>
                    <button @click="sidebarOpen = false" type="button" class="p-2 text-gray-400 hover:text-white rounded-full hover:bg-white/10 transition-colors cursor-pointer shrink-0" aria-label="Закрыть">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Navigation Links inside Drawer -->
                <nav class="space-y-1 text-sm font-medium">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all {{ request()->routeIs('dashboard') ? 'bg-[#0071e3] text-white font-semibold shadow-sm' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">
                        <svg class="w-5 h-5 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        {{ __('Дашборд') }}
                    </a>
                    <a href="{{ route('orders.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all {{ request()->routeIs('orders.*') ? 'bg-[#0071e3] text-white font-semibold shadow-sm' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">
                        <svg class="w-5 h-5 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        {{ __('Мои заказы') }}
                    </a>
                    <a href="{{ route('packages.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all {{ request()->routeIs('packages.*') ? 'bg-[#0071e3] text-white font-semibold shadow-sm' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">
                        <svg class="w-5 h-5 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        {{ __('Посылки') }}
                    </a>
                    <a href="{{ route('shipments.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all {{ request()->routeIs('shipments.*') ? 'bg-[#0071e3] text-white font-semibold shadow-sm' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">
                        <svg class="w-5 h-5 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        {{ __('Отправки Camex') }}
                    </a>
                    <a href="{{ route('buy-for-me') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all {{ request()->routeIs('buy-for-me') ? 'bg-[#0071e3] text-white font-semibold shadow-sm' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">
                        <svg class="w-5 h-5 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        {{ __('Выкупите за меня') }}
                    </a>
                    <a href="{{ route('profile.show') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all {{ request()->routeIs('profile.*') ? 'bg-[#0071e3] text-white font-semibold shadow-sm' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">
                        <svg class="w-5 h-5 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        {{ __('Мой профиль') }}
                    </a>
                    <a href="{{ route('help') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all {{ request()->routeIs('help') ? 'bg-[#0071e3] text-white font-semibold shadow-sm' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">
                        <svg class="w-5 h-5 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ __('Помощь / FAQ') }}
                    </a>

                    <!-- Logout button -->
                    <form method="POST" action="{{ route('logout') }}" class="pt-1">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl text-rose-400 hover:bg-rose-500/15 font-medium transition-colors cursor-pointer text-sm">
                            <svg class="w-5 h-5 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            {{ __('Выйти') }}
                        </button>
                    </form>
                </nav>
            </div>

            <!-- Bottom: Language Switcher as the very last element -->
            <div class="pt-4 border-t border-white/10">
                <div class="bg-white/5 p-3 rounded-2xl border border-white/10 flex items-center justify-between">
                    <span class="text-xs text-gray-400 font-medium">{{ __('Язык интерфейса') }}</span>
                    <x-language-switcher theme="dark" direction="up" />
                </div>
            </div>
        </div>
    </header>

    <!-- SYSTEM ALERTS -->
    @if(session('success'))
        <div class="bg-emerald-600 text-white text-xs font-medium py-2.5 px-4 text-center shadow-inner">
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
    <footer class="bg-white border-t border-black/[0.06] py-6 text-xs text-[#86868b]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row justify-between items-center gap-3">
            <div>
                © {{ date('Y') }} Buyer Greece • {{ __('Личный кабинет') }}
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('help') }}" class="hover:text-[#1d1d1f] transition-colors">{{ __('Помощь / FAQ') }}</a>
                <span>•</span>
                <x-messenger type="telegram" size="small">Telegram Support</x-messenger>
            </div>
        </div>
    </footer>

</body>
</html>