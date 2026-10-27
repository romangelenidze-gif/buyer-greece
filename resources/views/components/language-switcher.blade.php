@props(['theme' => 'light', 'direction' => 'down'])

<x-public-ui.dropdown :theme="$theme" :direction="$direction" width="w-28">
    
    <!-- Слот кнопки-триггера языка -->
    <x-slot:trigger>
        <button type="button" class="apple-lang-btn theme-{{ $theme }}">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
            </svg>
            <span class="uppercase tracking-wide font-semibold">{{ app()->getLocale() }}</span>
        </button>
    </x-slot:trigger>

    <!-- Список языков (попадает в $slot) -->
    <a href="{{ route('lang.switch', 'ka') }}" class="flex items-center px-3.5 py-2 transition-colors {{ app()->getLocale() === 'ka' ? 'text-[#0071e3] font-semibold' : ($theme === 'dark' ? 'hover:bg-white/10' : 'hover:bg-black/[0.04]') }}">
        <span>ქართული</span>
    </a>
    <a href="{{ route('lang.switch', 'en') }}" class="flex items-center px-3.5 py-2 transition-colors {{ app()->getLocale() === 'en' ? 'text-[#0071e3] font-semibold' : ($theme === 'dark' ? 'hover:bg-white/10' : 'hover:bg-black/[0.04]') }}">
        <span>English</span>
    </a>
    <a href="{{ route('lang.switch', 'ru') }}" class="flex items-center px-3.5 py-2 transition-colors {{ app()->getLocale() === 'ru' ? 'text-[#0071e3] font-semibold' : ($theme === 'dark' ? 'hover:bg-white/10' : 'hover:bg-black/[0.04]') }}">
        <span>Русский</span>
    </a>

</x-public-ui.dropdown>