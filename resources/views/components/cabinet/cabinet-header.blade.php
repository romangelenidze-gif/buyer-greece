<header x-data="{ sidebarOpen: false }" class="bg-[#1d1d1f] text-white sticky top-0 z-45 border-b border-white/10">
    <div class="px-4 sm:px-6 lg:px-8 w-full">
        <div class="flex items-center justify-between h-16">
            
            <!-- Logo (Extreme Left) -->
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}" class="font-bold text-base tracking-tight text-white flex items-center gap-1.5">
                    <span class="text-[#0071e3]">Buyer</span>Greece
                    <span class="hidden sm:inline-block app-badge-text text-gray-400 bg-white/10 px-2.5 py-0.5 rounded-full ml-1">{{ __('Личный кабинет') }}</span>
                </a>
            </div>

            <!-- Right Actions: Profile, Language Switcher & Hamburger -->
            <div class="flex items-center gap-3">
                <a href="{{ route('profile.show') }}" class="hidden sm:flex items-center gap-2 text-xs font-medium text-gray-300 hover:text-white bg-white/5 hover:bg-white/10 px-3.5 py-1.5 rounded-full border border-white/10 transition-colors">
                    <span class="w-5 h-5 rounded-full bg-[#0071e3] text-white font-bold flex items-center justify-center text-[10px]">
                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                    </span>
                    <span class="max-w-[120px] truncate">{{ Auth::user()->name ?? __('Клиент') }}</span>
                </a>

                <!-- Language Switcher (между профилем и гамбургером) -->
                <x-language-switcher theme="dark" direction="down" />

                <!-- Hamburger Button -->
                <button @click="sidebarOpen = true" type="button" class="p-2 text-gray-300 hover:text-white rounded-xl hover:bg-white/5 transition-colors focus:outline-none cursor-pointer" aria-label="Открыть меню">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

        </div>
    </div>

    <!-- Выпадающее боковое меню -->
    <x-cabinet.cabinet-nav />
</header>