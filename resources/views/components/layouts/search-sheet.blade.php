@php
    $quickQueries = [
        ['label' => 'оливковое масло', 'query' => 'оливковое масло 5л'],
        ['label' => 'Nike кроссовки', 'query' => 'кроссовки nike'],
        ['label' => 'греческий мед', 'query' => 'греческий мед'],
        ['label' => 'Apivita', 'query' => 'косметика apivita'],
    ];
@endphp

<div x-show="searchOpen"
    x-effect="searchOpen ? document.body.classList.add('overflow-hidden') : document.body.classList.remove('overflow-hidden')"
    @keydown.escape.window="searchOpen = false"
    @resize.window="if (window.innerWidth >= 1024) searchOpen = false"
    class="relative z-[100]"
>
    <div
        x-show="searchOpen"
        x-cloak
        x-transition:enter="apple-sheet-enter"
        x-transition:enter-start="apple-sheet-enter-start"
        x-transition:enter-end="apple-sheet-enter-end"
        x-transition:leave="apple-sheet-leave"
        x-transition:leave-start="apple-sheet-leave-start"
        x-transition:leave-end="apple-sheet-leave-end"
        class="apple-sheet fixed inset-0 z-[100] flex flex-col overflow-y-auto bg-[#fbfbfd]"
        role="dialog"
        aria-modal="true"
        aria-label="{{ __('Поиск') }}"
    >
        <!-- Шапка шторки -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full shrink-0">
            <div class="flex items-center justify-end h-20">
                <button type="button" @click="searchOpen = false" aria-label="{{ __('Закрыть поиск') }}"
                        class="p-2 text-gray-700 rounded-full hover:bg-black/[0.04] transition-colors cursor-pointer focus:outline-none">
                    <svg class="w-6 h-6 text-[#1d1d1f]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Контент умного поиска -->
        <div class="max-w-3xl mx-auto px-6 sm:px-8 lg:px-8 w-full flex-1 flex flex-col pb-12 pt-2">
            <div class="space-y-6">
                
                <!-- Заголовок -->
                <div class="text-center space-y-2">
                    <h2 class="apple-section-title">
                        {{ __('Умный поиск в Греции') }}
                    </h2>
                    <p class="app-text-sub">
                        {{ __('Опишите любой товар на вашем языке. ИИ подберет предложения в греческих магазинах.') }}
                    </p>
                </div>

                <!-- Поисковая строка -->
                <div class="bg-white p-3.5 sm:p-4 rounded-full shadow-[0_10px_30px_rgba(0,0,0,0.05)] border border-black/[0.08]">
                    <form action="{{ route('product-search') }}" method="GET" class="flex items-center gap-2" x-data="{ loading: false }" @submit="loading = true">
                        <div class="relative flex-1 w-full flex items-center">
                            <span class="absolute left-3.5 pointer-events-none text-[#86868b] flex items-center">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input 
                                type="text" 
                                name="q" 
                                value="{{ request('q') }}"
                                placeholder="{{ __('Поиск...') }}" 
                                required
                                maxlength="200"
                                x-ref="searchInput"
                                x-effect="if (searchOpen) setTimeout(() => $refs.searchInput.focus(), 100)"
                                class="w-full pl-12 pr-4 py-2 bg-transparent border-0 app-text-title text-xl text-[#1d1d1f] placeholder-[#86868b] focus:outline-none focus:ring-0"
                            >
                        </div>
                    </form>
                </div>

                <!-- Быстрые подсказки -->
                <div class="pt-4 space-y-3">
                    <h3 class="app-badge-text text-[#86868b]">{{ __('Быстрый поиск и категории') }}</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($quickQueries as $item)
                            <a href="{{ route('product-search', ['q' => $item['query']]) }}" @click="searchOpen = false" class="px-4 py-2 rounded-full bg-black/[0.04] hover:bg-black/[0.08] app-text-sub font-medium text-[#1d1d1f] transition-colors">
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>