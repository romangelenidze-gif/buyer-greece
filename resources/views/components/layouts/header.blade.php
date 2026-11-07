@php
    $isHomePage = request()->routeIs('home');
@endphp

<!-- Статичный хедер -->
<header class="text-[#1d1d1f] relative w-full">
    <div class="max-w-[96rem] mx-auto px-4 sm:px-6 lg:px-8 w-full">
        
        <!-- МОБИЛЬНАЯ ШАПКА (< lg): на главной только гамбургер справа, на внутренних — логотип и гамбургер -->
        <div class="flex lg:hidden items-center h-16 {{ $isHomePage ? 'justify-end' : 'justify-between' }}">
            @unless($isHomePage)
                <a href="{{ route('home') }}" class="apple-section-title !text-xl flex items-center">
                    exereto
                </a>
            @endunless

            <button @click="mobileNavOpen = true" type="button" class="p-2 text-gray-700 rounded-full hover:bg-black/[0.04] transition-colors cursor-pointer focus:outline-none" aria-label="{{ __('Открыть меню') }}">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <!-- ДЕСКТОПНАЯ ШАПКА (>= lg) -->
        <div class="hidden lg:flex items-center justify-center gap-8 h-20">
            @unless($isHomePage)
                <a href="{{ route('home') }}" class="apple-section-title !text-xl flex items-center shrink-0">
                    exereto
                </a>
            @endunless

            <nav class="flex items-center space-x-1 shrink-0">
                <a href="{{ route('how-it-works') }}" class="apple-nav-link {{ request()->routeIs('how-it-works') ? 'active' : '' }}">
                    {{ __('Как это работает') }}
                </a>
                <a href="{{ route('buy-for-me') }}" class="apple-nav-link {{ request()->routeIs('buy-for-me') ? 'active' : '' }}">
                    {{ __('Выкупите за меня') }}
                </a>
                <a href="{{ route('forwarding') }}" class="apple-nav-link {{ request()->routeIs('forwarding') ? 'active' : '' }}">
                    {{ __('Форвардинг') }}
                </a>
                <a href="{{ route('help') }}" class="apple-nav-link {{ request()->routeIs('help') ? 'active' : '' }}">
                    {{ __('Помощь / FAQ') }}
                </a>
            </nav>

            <div class="flex items-center gap-4 shrink-0">
                @auth
                    <a href="{{ route('dashboard') }}" class="apple-nav-link">
                        {{ __('Личный кабинет') }}
                    </a>
                @else
                    <a href="{{ route('login') }}" class="apple-nav-link">
                        {{ __('Войти') }}
                    </a>
                @endauth

                <x-language-switcher theme="light" />
            </div>
        </div>

    </div>

    <x-layouts.mobile-nav />
    <x-layouts.search-sheet />
</header>