<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#f2f2f2]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? __('Buyer Greece → Georgia | Выкуп и доставка товаров из Греции') }}</title>
    @if(isset($description))
        <meta name="description" content="{{ $description }}">
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex flex-col min-h-full text-[#1d1d1f] antialiased font-sans bg-[#f2f2f2]" x-data="{ mobileNavOpen: false }">

    <!-- PUBLIC NAVIGATION HEADER -->
    <header class="bg-white text-[#1d1d1f] sticky top-0 z-50 border-b border-black/[0.08]">
        <div class="px-4 sm:px-6 lg:px-8 w-full">
            <div class="flex items-center justify-between h-16">
                
                <!-- Logo (Extreme Left) -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="font-bold text-lg tracking-tight text-[#1d1d1f] flex items-center gap-1.5">
                        <span class="text-[#0071e3]">Buyer</span>Greece
                    </a>
                </div>

                <!-- Right Actions: Login & Hamburger (Extreme Right) -->
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-xs font-semibold px-4 py-2 bg-black/[0.04] hover:bg-black/[0.08] text-[#1d1d1f] rounded-full transition-colors">
                            {{ __('Личный кабинет') }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-xs font-semibold px-4 py-2 bg-black/[0.04] hover:bg-black/[0.08] text-[#1d1d1f] rounded-full transition-colors">
                            {{ __('Войти') }}
                        </a>
                    @endauth

                    <!-- Hamburger Button -->
                    <button @click="mobileNavOpen = true" type="button" class="p-2 text-[#1d1d1f] hover:bg-black/[0.04] rounded-xl transition-colors focus:outline-none cursor-pointer" aria-label="Открыть меню">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>

            </div>
        </div>

        <!-- RIGHT SIDEBAR / APPLE DRAWER -->
        <div 
            x-show="mobileNavOpen" 
            x-transition:enter="transition-opacity ease-linear duration-300" 
            x-transition:enter-start="opacity-0" 
            x-transition:enter-end="opacity-100" 
            x-transition:leave="transition-opacity ease-linear duration-300" 
            x-transition:leave-start="opacity-100" 
            x-transition:leave-end="opacity-0" 
            x-cloak 
            class="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm"
            @click="mobileNavOpen = false"
        ></div>

        <div 
            x-show="mobileNavOpen" 
            x-transition:enter="transition ease-in-out duration-300 transform" 
            x-transition:enter-start="translate-x-full" 
            x-transition:enter-end="translate-x-0" 
            x-transition:leave="transition ease-in-out duration-300 transform" 
            x-transition:leave-start="translate-x-0" 
            x-transition:leave-end="translate-x-full" 
            x-cloak 
            class="fixed inset-y-0 right-0 z-50 w-full sm:w-80 bg-white/95 backdrop-blur-2xl border-l border-black/[0.08] shadow-2xl flex flex-col justify-between py-5 px-5"
        >
            <!-- Top: Compact Menu with Close Button beside the first item -->
            <div class="space-y-3">
                <div class="flex items-center gap-2">
                    <a href="{{ route('home') }}" @click="mobileNavOpen = false" class="flex-grow flex items-center gap-3 px-4 py-3 rounded-2xl transition-all {{ request()->routeIs('home') ? 'bg-[#0071e3] text-white font-semibold shadow-xs' : 'text-[#1d1d1f] hover:bg-black/[0.04]' }}">
                        {{ __('Главная') }}
                    </a>
                    <button @click="mobileNavOpen = false" type="button" class="p-2 text-gray-500 hover:text-[#1d1d1f] rounded-full hover:bg-black/[0.04] transition-colors cursor-pointer shrink-0" aria-label="Закрыть">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Navigation Links inside Drawer -->
                <nav class="space-y-1 text-sm font-medium">
                    <a href="{{ route('buy-for-me') }}" @click="mobileNavOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all {{ request()->routeIs('buy-for-me') ? 'bg-[#0071e3] text-white font-semibold shadow-xs' : 'text-[#1d1d1f] hover:bg-black/[0.04]' }}">
                        {{ __('Выкупите за меня') }}
                    </a>
                    <a href="{{ route('forwarding') }}" @click="mobileNavOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all {{ request()->routeIs('forwarding') ? 'bg-[#0071e3] text-white font-semibold shadow-xs' : 'text-[#1d1d1f] hover:bg-black/[0.04]' }}">
                        {{ __('Форвардинг (Адрес в Греции)') }}
                    </a>
                    <a href="{{ route('help') }}" @click="mobileNavOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all {{ request()->routeIs('help') ? 'bg-[#0071e3] text-white font-semibold shadow-xs' : 'text-[#1d1d1f] hover:bg-black/[0.04]' }}">
                        {{ __('Помощь / FAQ') }}
                    </a>

                    @guest
                        <a href="{{ route('login') }}" @click="mobileNavOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all text-[#1d1d1f] hover:bg-black/[0.04]">
                            {{ __('Войти в аккаунт') }}
                        </a>
                    @endguest
                </nav>
            </div>

            <!-- Bottom: Language Switcher as the very last element -->
            <div class="pt-4 border-t border-black/[0.08]">
                <div class="bg-black/[0.03] p-3 rounded-2xl border border-black/[0.06] flex items-center justify-between">
                    <span class="text-xs text-[#86868b] font-medium">{{ __('Язык интерфейса') }}</span>
                    <x-language-switcher theme="light" direction="up" />
                </div>
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main class="flex-grow">
        {{ $slot }}
    </main>

    <!-- FOOTER -->
    <footer class="bg-white border-t border-black/[0.06] py-12 text-xs text-[#86868b]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div>
                © {{ date('Y') }} Buyer Greece • {{ __('Все права защищены.') }}
            </div>
            <div class="flex items-center gap-6">
                <a href="{{ route('help') }}" class="hover:text-[#1d1d1f] transition-colors">{{ __('Помощь / FAQ') }}</a>
                <x-messenger type="telegram" size="small">Telegram</x-messenger>
                <x-messenger type="whatsapp" size="small">WhatsApp</x-messenger>
            </div>
        </div>
    </footer>

</body>
</html>