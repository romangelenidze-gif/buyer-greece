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
            <a href="{{ route('how-it-works') }}" @click="mobileNavOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all {{ request()->routeIs('how-it-works') ? 'bg-[#0071e3] text-white font-semibold shadow-xs' : 'text-[#1d1d1f] hover:bg-black/[0.04]' }}">
                {{ __('Как это работает') }}
            </a>
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