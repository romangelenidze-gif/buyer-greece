@php
    $isSearchPage = request()->routeIs('product-search');
@endphp

<header class="bg-white text-[#1d1d1f] sticky top-0 z-50 border-b border-black/[0.08] backdrop-blur-xl bg-white/90">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
        <div class="flex items-center justify-between h-20">
            
            <!-- Логотип -->
            <div class="flex items-center shrink-0">
                <a href="{{ route('home') }}" class="apple-section-title !text-xl flex items-center gap-1 group">
                    <span class="text-[#0071e3]">Buyer</span>Greece
                </a>
            </div>

            <!-- Десктопное меню -->
            <nav class="hidden lg:flex items-center space-x-2">
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

            <!-- Правая панель -->
            <div class="flex items-center gap-2 sm:gap-1 shrink-0">
                
                <!-- КНОПКА ПОИСКА (скрывается на странице результатов поиска) -->
                @unless($isSearchPage)
                    <button @click="searchOpen = true" type="button" class="apple-nav-link cursor-pointer focus:outline-none" aria-label="{{ __('Поиск') }}">
                        <svg class="w-5 h-5 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                       <!--  <span class="hidden md:inline">{{ __('Поиск') }}</span>-->
                    </button>
                @endunless

                @auth
                    <a href="{{ route('dashboard') }}" class="hidden sm:inline-flex apple-nav-link">
                        {{ __('Личный кабинет') }}
                    </a>
                @else
                    <a href="{{ route('login') }}" class="hidden sm:inline-flex apple-nav-link">
                        {{ __('Войти') }}
                    </a>
                @endauth

                <!-- Переключатель языка -->
                <div class="hidden sm:block">
                    <x-language-switcher theme="light" />
                </div>

                <!-- Кнопка мобильного гамбургера -->
                <button @click="mobileNavOpen = true" type="button" class="lg:hidden p-2 text-gray-700 rounded-full hover:bg-gray-100 transition-colors cursor-pointer focus:outline-none" aria-label="{{ __('Открыть меню') }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

        </div>
    </div>

    <!-- Мобильная шторка меню -->
    <x-layouts.mobile-nav />

    <!-- Шторка умного поиска -->
    <x-layouts.search-sheet />
</header>