<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Buyer Greece — Выкуп и доставка товаров из Греции в Грузию' }}</title>
    <meta name="description" content="{{ $description ?? 'Сервис выкупа товаров в магазинах Греции и подготовки посылок для международной доставки в Грузию через Camex.' }}">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- OpenGraph Metadata -->
    <meta property="og:title" content="{{ $title ?? 'Buyer Greece — Выкуп товаров в Греции' }}">
    <meta property="og:description" content="{{ $description ?? 'Помогаем покупать в греческих интернет-магазинах с доставкой в Грузию.' }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('images/og-cover.jpg') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex flex-col min-h-full text-slate-800 antialiased font-sans">
    
    <!-- HEADER -->
    <header x-data="{ mobileMenuOpen: false }" class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 text-xl font-bold text-slate-900 tracking-tight">
                        <span class="text-brand-600">Buyer</span>Greece
                    </a>
                </div>

                <!-- Desktop Nav -->
                <nav class="hidden md:flex items-center space-x-8 text-sm font-medium">
                    <a href="{{ route('how-it-works') }}" class="text-slate-600 hover:text-slate-900 transition-colors">Как это работает</a>
                    <a href="{{ route('buy-for-me') }}" class="text-slate-600 hover:text-slate-900 transition-colors">Выкупите за меня</a>
                    <a href="{{ route('forwarding') }}" class="text-slate-600 hover:text-slate-900 transition-colors">Форвардинг</a>
                    <a href="{{ route('help') }}" class="text-slate-600 hover:text-slate-900 transition-colors">Помощь / FAQ</a>
                </nav>

                <!-- Desktop CTAs -->
                <div class="hidden md:flex items-center space-x-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-sm font-medium text-slate-700 hover:text-slate-900">
                            Личный кабинет
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900 px-3 py-2">
                            Войти
                        </a>
                        <x-buttons.primary href="{{ route('buy-for-me') }}" size="small">
                            Отправить ссылку
                        </x-buttons.primary>
                    @endauth
                </div>

                <!-- Mobile Hamburger Button -->
                <div class="flex md:hidden items-center">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="text-slate-500 hover:text-slate-700 p-2 focus:outline-none" aria-label="Открыть меню">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path x-show="mobileMenuOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Slide-out Menu -->
        <div x-show="mobileMenuOpen" x-cloak class="md:hidden border-b border-slate-200 bg-white px-4 pt-2 pb-6 space-y-3">
            <a href="{{ route('how-it-works') }}" class="block px-3 py-2 text-base font-medium text-slate-700 hover:bg-slate-50 rounded-md">Как это работает</a>
            <a href="{{ route('buy-for-me') }}" class="block px-3 py-2 text-base font-medium text-slate-700 hover:bg-slate-50 rounded-md">Выкупите за меня</a>
            <a href="{{ route('forwarding') }}" class="block px-3 py-2 text-base font-medium text-slate-700 hover:bg-slate-50 rounded-md">Форвардинг</a>
            <a href="{{ route('help') }}" class="block px-3 py-2 text-base font-medium text-slate-700 hover:bg-slate-50 rounded-md">Помощь / FAQ</a>
            
            <div class="pt-4 border-t border-slate-100 flex flex-col gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="w-full text-center py-2 px-4 bg-slate-100 text-slate-800 font-medium rounded-lg">Личный кабинет</a>
                @else
                    <a href="{{ route('login') }}" class="w-full text-center py-2 px-4 text-slate-700 font-medium">Войти</a>
                    <x-buttons.primary href="{{ route('buy-for-me') }}" class="w-full justify-center">
                        Отправить ссылку
                    </x-buttons.primary>
                @endauth
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main class="flex-grow">
        {{ $slot }}
    </main>

    <!-- FOOTER -->
    <footer class="bg-slate-900 text-slate-400 py-12 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="space-y-4 md:col-span-1">
                <span class="text-xl font-bold text-white tracking-tight">Buyer<span class="text-brand-500">Greece</span></span>
                <p class="text-sm leading-relaxed">
                    Сервис выкупа и приема покупок в греческих магазинах. Международная доставка в Грузию выполняется перевозчиком Camex.
                </p>
            </div>

            <div>
                <h3 class="text-xs font-semibold text-slate-200 uppercase tracking-wider mb-4">Услуги</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('buy-for-me') }}" class="hover:text-white transition-colors">Выкуп по ссылке</a></li>
                    <li><a href="{{ route('forwarding') }}" class="hover:text-white transition-colors">Греческий адрес (Форвардинг)</a></li>
                    <li><a href="{{ route('how-it-works') }}" class="hover:text-white transition-colors">Схема работы</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-xs font-semibold text-slate-200 uppercase tracking-wider mb-4">Поддержка</h3>
                <div class="space-y-2">
                    <x-messenger type="telegram" size="small" />
                    <x-messenger type="whatsapp" size="small" />
                </div>
            </div>

            <div>
                <h3 class="text-xs font-semibold text-slate-200 uppercase tracking-wider mb-4">Информация</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="/terms" class="hover:text-white transition-colors">Условия обслуживания</a></li>
                    <li><a href="/privacy" class="hover:text-white transition-colors">Политика конфиденциальности</a></li>
                </ul>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 pt-6 border-t border-slate-800 text-xs text-slate-500 flex flex-col sm:flex-row justify-between items-center gap-4">
            <p>© {{ date('Y') }} BuyerGreece. Все права защищены.</p>
            <p>Международная транспортировка Греция → Грузия регулируется правилами Camex.</p>
        </div>
    </footer>

</body>
</html>