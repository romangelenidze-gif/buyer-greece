@php
    $primaryLinks = [
        ['route' => 'home',         'label' => __('Главная')],
        ['route' => 'how-it-works', 'label' => __('Как это работает')],
        ['route' => 'buy-for-me',   'label' => __('Выкупите за меня')],
        ['route' => 'forwarding',   'label' => __('Форвардинг')],
    ];

    $secondaryLinks = [
        ['route' => 'help', 'label' => __('Помощь / FAQ')],
        auth()->check()
            ? ['route' => 'dashboard', 'label' => __('Личный кабинет')]
            : ['route' => 'login', 'label' => __('Войти в аккаунт')],
    ];

    // Индексы для каскадной анимации: основные пункты, затем вторичный блок и нижняя панель
    $offset = count($primaryLinks);
@endphp

<div x-show="mobileNavOpen"
     x-cloak
     x-effect="document.body.classList.toggle('overflow-hidden', mobileNavOpen)"
     @keydown.escape.window="mobileNavOpen = false"
     @resize.window="if (window.innerWidth >= 1024) mobileNavOpen = false"
     x-transition:enter="apple-sheet-enter"
     x-transition:enter-start="apple-sheet-enter-start"
     x-transition:enter-end="apple-sheet-enter-end"
     x-transition:leave="apple-sheet-leave"
     x-transition:leave-start="apple-sheet-leave-start"
     x-transition:leave-end="apple-sheet-leave-end"
     class="apple-sheet fixed inset-0 z-[100] flex flex-col overflow-y-auto lg:hidden"
     role="dialog"
     aria-modal="true"
     aria-label="{{ __('Меню') }}"
>
    {{-- Шапка шторки повторяет сетку хедера, чтобы крестик встал точно на место гамбургера --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 w-full shrink-0">
        <div class="flex items-center justify-end h-20">
            <button type="button" @click="mobileNavOpen = false" aria-label="{{ __('Закрыть') }}"
                    class="apple-icon-btn">
                <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Пункты подняты вверх под шапку (как на эталоне Apple) --}}
    <div class="max-w-7xl mx-auto px-10 sm:px-12 w-full flex-1 flex flex-col pb-8 -mt-3 sm:-mt-4">
        <nav class="flex flex-1 flex-col">
            <ul class="space-y-1">
                @foreach ($primaryLinks as $link)
                    <li class="apple-item" style="--i: {{ $loop->index }}">
                        <a href="{{ route($link['route']) }}" @click="mobileNavOpen = false"
                           @if (request()->routeIs($link['route'])) aria-current="page" @endif
                           class="apple-mobile-link">
                            {{ $link['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>

            <ul class="apple-item apple-mobile-nav-divider mt-6 space-y-1" style="--i: {{ $offset }}">
                @foreach ($secondaryLinks as $link)
                    <li>
                        <a href="{{ route($link['route']) }}" @click="mobileNavOpen = false"
                           @if (request()->routeIs($link['route'])) aria-current="page" @endif
                           @class(['apple-mobile-link apple-mobile-link--secondary', 'apple-mobile-link--accent' => ! empty($link['accent'])])>
                            {{ $link['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>

            {{-- Нижняя панель: переключатель языка --}}
            <div class="apple-item apple-mobile-nav-divider apple-mobile-nav-footer" style="--i: {{ $offset + 1 }}">
                <span class="app-badge-text apple-mobile-nav-label">{{ __('Язык интерфейса') }}</span>
                <x-language-switcher theme="light" direction="up" />
            </div>
        </nav>
    </div>
</div>
