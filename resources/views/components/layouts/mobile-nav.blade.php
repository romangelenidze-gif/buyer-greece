@php
    $primaryLinks = [
        ['route' => 'home',         'label' => __('Главная')],
        ['route' => 'how-it-works', 'label' => __('Как это работает')],
        ['route' => 'buy-for-me',   'label' => __('Выкупите за меня')],
        ['route' => 'forwarding',   'label' => __('Форвардинг')],
    ];
    $offset = count($primaryLinks);
@endphp

<div x-show="mobileNavOpen"
    x-effect="mobileNavOpen ? document.body.classList.add('overflow-hidden') : document.body.classList.remove('overflow-hidden')"
    @keydown.escape.window="mobileNavOpen = false"
    @resize.window="if (window.innerWidth >= 1024) mobileNavOpen = false"
    class="lg:hidden"
>
    <div
        x-show="mobileNavOpen"
        x-cloak
        x-transition:enter="apple-sheet-enter"
        x-transition:enter-start="apple-sheet-enter-start"
        x-transition:enter-end="apple-sheet-enter-end"
        x-transition:leave="apple-sheet-leave"
        x-transition:leave-start="apple-sheet-leave-start"
        x-transition:leave-end="apple-sheet-leave-end"
        class="apple-sheet fixed inset-0 z-[100] flex flex-col overflow-y-auto !bg-[#fbfbfd]"
        role="dialog"
        aria-modal="true"
        aria-label="{{ __('Меню') }}"
    >
        <!-- Шапка шторки точно повторяет сетку хедера, чтобы крестик встал пиксель в пиксель на место гамбургера -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full shrink-0">
            <div class="flex items-center justify-end h-20">
                <button type="button" @click="mobileNavOpen = false" aria-label="{{ __('Закрыть') }}"
                        class="p-2 text-gray-700 rounded-full hover:bg-gray-100 transition-colors cursor-pointer focus:outline-none">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Контейнер пунктов меню: подняты высоко вверх под шапку (как на эталоне Apple) -->
        <div class="max-w-7xl mx-auto px-10 sm:px-12 lg:px-8 w-full flex-1 flex flex-col pb-8 pt-0 -mt-3 sm:-mt-4">
            <nav class="flex flex-1 flex-col">
                <!-- Основные пункты меню с надежным синим выделением активного роута -->
                <ul class="space-y-1">
                    @foreach ($primaryLinks as $link)
                        @php 
                            $active = request()->routeIs($link['route']); 
                            $linkStyle = $active ? 'color: #0071e3; font-weight: 700;' : 'color: #1d1d1f;';
                        @endphp
                        <li class="apple-item" style="--i: {{ $loop->index }}">
                            <a href="{{ route($link['route']) }}" @click="mobileNavOpen = false"
                               @if ($active) aria-current="page" @endif
                               class="apple-section-title block py-1.5 !text-2xl sm:!text-3xl transition-colors hover:opacity-60"
                               style="{{ $linkStyle }}">
                                {{ $link['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>

                <!-- Вторичные ссылки -->
                <ul class="apple-item mt-6 space-y-1 border-t border-black/[0.08] pt-4" style="--i: {{ $offset }}">
                    <li>
                        @php 
                            $helpActive = request()->routeIs('help'); 
                            $helpStyle = $helpActive ? 'color: #0071e3; font-weight: 700;' : 'color: #1d1d1f;';
                        @endphp
                        <a href="{{ route('help') }}" @click="mobileNavOpen = false"
                           @if ($helpActive) aria-current="page" @endif
                           class="app-card-title block py-1.5 transition-colors hover:opacity-60"
                           style="{{ $helpStyle }}">
                            {{ __('Помощь / FAQ') }}
                        </a>
                    </li>
                    <li>
                        @auth
                            @php 
                                $dashActive = request()->routeIs('dashboard'); 
                                $dashStyle = $dashActive ? 'color: #0071e3; font-weight: 700;' : 'color: #1d1d1f;';
                            @endphp
                            <a href="{{ route('dashboard') }}" @click="mobileNavOpen = false"
                               @if ($dashActive) aria-current="page" @endif
                               class="app-card-title block py-1.5 transition-colors hover:opacity-60"
                               style="{{ $dashStyle }}">
                                {{ __('Личный кабинет') }}
                            </a>
                        @else
                            <a href="{{ route('login') }}" @click="mobileNavOpen = false"
                               class="app-card-title block py-1.5 transition-colors hover:opacity-60"
                               style="color: #0071e3; font-weight: 700;">
                                {{ __('Войти в аккаунт') }}
                            </a>
                        @endauth
                    </li>
                </ul>

                <!-- Нижняя панель переключения языка -->
                <div class="apple-item mt-auto pt-4 flex items-center justify-between border-t border-black/[0.08]"
                     style="--i: {{ $offset + 1 }}; padding-bottom: max(1rem, env(safe-area-inset-bottom))">
                    <span class="app-badge-text text-[#86868b]">{{ __('Язык интерфейса') }}</span>
                    <x-language-switcher theme="light" direction="up" />
                </div>
            </nav>
        </div>
    </div>
</div>