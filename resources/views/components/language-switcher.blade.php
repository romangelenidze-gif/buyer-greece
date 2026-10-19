@props(['theme' => 'light', 'direction' => 'down'])

<div x-data="{ open: false }" class="relative inline-block text-left" @click.outside="open = false">
    <button @click="open = !open" 
            type="button" 
            class="apple-lang-btn theme-{{ $theme }}">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
        </svg>
        <span class="uppercase tracking-wide font-semibold">{{ app()->getLocale() }}</span>
    </button>

    <div x-show="open" 
         x-transition:enter="transition ease-out duration-150" 
         x-transition:enter-start="opacity-0 scale-95 {{ $direction === 'up' ? 'translate-y-1' : '-translate-y-1' }}" 
         x-transition:enter-end="opacity-100 scale-100 translate-y-0" 
         x-transition:leave="transition ease-in duration-100" 
         x-transition:leave-start="opacity-100 scale-100 translate-y-0" 
         x-transition:leave-end="opacity-0 scale-95 {{ $direction === 'up' ? 'translate-y-1' : '-translate-y-1' }}" 
         x-cloak 
         class="absolute {{ $direction === 'up' ? 'bottom-full mb-2' : 'top-full mt-2' }} right-0 w-28 rounded-2xl overflow-hidden shadow-xl py-1.5 z-50 text-xs font-medium {{ $theme === 'dark' ? 'bg-[#1d1d1f]/95 backdrop-blur-xl border border-white/15 text-white' : 'bg-white/95 backdrop-blur-xl border border-black/[0.08] text-[#1d1d1f]' }}">
        
        <a href="{{ route('lang.switch', 'ka') }}" class="flex items-center px-3.5 py-2 transition-colors {{ app()->getLocale() === 'ka' ? 'text-[#0071e3] font-semibold' : ($theme === 'dark' ? 'hover:bg-white/10' : 'hover:bg-black/[0.04]') }}">
            <span>ქართული</span>
        </a>
        <a href="{{ route('lang.switch', 'en') }}" class="flex items-center px-3.5 py-2 transition-colors {{ app()->getLocale() === 'en' ? 'text-[#0071e3] font-semibold' : ($theme === 'dark' ? 'hover:bg-white/10' : 'hover:bg-black/[0.04]') }}">
            <span>English</span>
        </a>
        <a href="{{ route('lang.switch', 'ru') }}" class="flex items-center px-3.5 py-2 transition-colors {{ app()->getLocale() === 'ru' ? 'text-[#0071e3] font-semibold' : ($theme === 'dark' ? 'hover:bg-white/10' : 'hover:bg-black/[0.04]') }}">
            <span>Русский</span>
        </a>
    </div>
</div>