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

    <!-- Подключаем выпадающий Drawer прямо из внутри шапки -->
    <x-layouts.mobile-nav />
</header>