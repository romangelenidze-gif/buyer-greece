@props(['theme' => 'light', 'direction' => 'down'])

@php
    $languages = [
        'ka' => 'ქართული',
        'en' => 'English',
        'ru' => 'Русский',
    ];
    $current = app()->getLocale();
@endphp

<x-public-ui.dropdown :theme="$theme" :direction="$direction" width="w-34">

    {{-- Кнопка-триггер --}}
    <x-slot:trigger>
        <button type="button" class="apple-lang-btn theme-{{ $theme }}" aria-label="{{ __('Язык интерфейса') }}">
            <svg fill="none" viewBox="0 0 22 22" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
            </svg>
            <span class="apple-lang-code">{{ $current }}</span>
        </button>
    </x-slot:trigger>

    {{-- Список языков (попадает в $slot) --}}
    @foreach ($languages as $code => $name)
        <a href="{{ route('lang.switch', $code) }}"
           @if ($current === $code) aria-current="true" @endif
           class="apple-lang-option theme-{{ $theme }}">
            <span lang="{{ $code }}">{{ $name }}</span>
        </a>
    @endforeach

</x-public-ui.dropdown>
