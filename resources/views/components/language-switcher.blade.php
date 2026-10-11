@props(['theme' => 'light', 'direction' => 'down'])

<div x-data="{ open: false }" class="relative inline-block text-left" @click.outside="open = false">
    <button @click="open = !open" type="button" 
        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium transition-colors cursor-pointer {{ $theme === 'dark' ? 'bg-white/10 text-white hover:bg-white/15 border border-white/10' : 'bg-black/[0.04] text-[#1d1d1f] hover:bg-black/[0.08] border border-black/[0.06]' }}">
        <svg class="w-3.5 h-3.5 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
        </svg>
        <span class="uppercase font-semibold">{{ app()->getLocale() }}</span>
        <svg class="w-3 h-3 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div x-show="open" 
         x-transition:enter="transition ease-out duration-150" 
         x-transition:enter-start="opacity-0 scale-95 {{ $direction === 'up' ? 'translate-y-1' : '-translate-y-1' }}" 
         x-transition:enter-end="opacity-100 scale-100 translate-y-0" 
         x-transition:leave="transition ease-in duration-100" 
         x-transition:leave-start="opacity-100 scale-100 translate-y-0" 
         x-transition:leave-end="opacity-0 scale-95 {{ $direction === 'up' ? 'translate-y-1' : '-translate-y-1' }}" 
         x-cloak 
         class="absolute {{ $direction === 'up' ? 'bottom-full mb-2' : 'top-full mt-2' }} right-0 w-36 rounded-2xl shadow-xl py-1.5 z-50 text-xs font-medium {{ $theme === 'dark' ? 'bg-[#1d1d1f]/95 backdrop-blur-xl border border-white/15 text-white' : 'bg-white/95 backdrop-blur-xl border border-black/[0.08] text-[#1d1d1f]' }}">
        
        <a href="{{ route('lang.switch', 'ka') }}" class="flex items-center justify-between px-3.5 py-2 transition-colors {{ app()->getLocale() === 'ka' ? 'bg-[#0071e3] text-white' : ($theme === 'dark' ? 'hover:bg-white/10' : 'hover:bg-black/[0.04]') }}">
            <span>ქართული</span>
            @if(app()->getLocale() === 'ka')<span class="text-[10px]">✓</span>@endif
        </a>
        <a href="{{ route('lang.switch', 'en') }}" class="flex items-center justify-between px-3.5 py-2 transition-colors {{ app()->getLocale() === 'en' ? 'bg-[#0071e3] text-white' : ($theme === 'dark' ? 'hover:bg-white/10' : 'hover:bg-black/[0.04]') }}">
            <span>English</span>
            @if(app()->getLocale() === 'en')<span class="text-[10px]">✓</span>@endif
        </a>
        <a href="{{ route('lang.switch', 'ru') }}" class="flex items-center justify-between px-3.5 py-2 transition-colors {{ app()->getLocale() === 'ru' ? 'bg-[#0071e3] text-white' : ($theme === 'dark' ? 'hover:bg-white/10' : 'hover:bg-black/[0.04]') }}">
            <span>Русский</span>
            @if(app()->getLocale() === 'ru')<span class="text-[10px]">✓</span>@endif
        </a>
    </div>
</div>