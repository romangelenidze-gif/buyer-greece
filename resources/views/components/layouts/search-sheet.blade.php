@php
    // Берем реальные популярные запросы из таблицы кэша автокомплита learned_autocomplete по количеству хитов
    $quickQueries = \Illuminate\Support\Facades\DB::table('learned_autocomplete')
        ->orderByDesc('hits')
        ->limit(6)
        ->get();
@endphp

<div x-show="searchOpen"
    x-effect="searchOpen ? document.body.classList.add('overflow-hidden') : document.body.classList.remove('overflow-hidden')"
    @keydown.escape.window="searchOpen = false"
    @resize.window="if (window.innerWidth >= 1024) searchOpen = false"
    class="relative z-[100]"
    x-data="{
        query: '{{ request('q') }}',
        suggestions: [],
        loadingSuggestions: false,
        loading: false,
        fetchSuggestions() {
            if (this.query.length < 2) {
                this.suggestions = [];
                return;
            }
            this.loadingSuggestions = true;
            fetch(`{{ route('api.autocomplete') }}?q=${encodeURIComponent(this.query)}`)
                .then(res => res.json())
                .then(data => {
                    this.suggestions = data.suggestions || [];
                    this.loadingSuggestions = false;
                })
                .catch(() => {
                    this.suggestions = [];
                    this.loadingSuggestions = false;
                });
        },
        selectSuggestion(val) {
            this.query = val;
            this.suggestions = [];
            this.loading = true;
            window.location.href = `{{ route('product-search') }}?q=${encodeURIComponent(val)}`;
        }
    }"
    x-init="$watch('searchOpen', value => { if(value) { setTimeout(() => $refs.searchInput.focus(), 100); } })"
    @pageshow.window="loading = false"
>
    <!-- ТЕЛЕПОРТ ОВЕРЛЕЯ ЗАГРУЗКИ В BODY -->
    <template x-teleport="body">
        <div x-show="loading" x-cloak class="fixed inset-0 z-[9999] bg-white/80 backdrop-blur-md flex flex-col items-center justify-center space-y-4">
            <svg class="animate-spin h-10 w-10 text-[#0071e3]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-base font-semibold text-[#1d1d1f] animate-pulse">{{ __('Ищем предложения в магазинах Греции...') }}</span>
        </div>
    </template>

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
                        {{ __('Умный поиск') }}
                    </h2>
                    <p class="app-text-sub">
                        {{ __('Опишите любой товар на вашем языке. ИИ подберет предложения в греческих магазинах.') }}
                    </p>
                </div>

                <!-- Поисковая строка и автокомплит -->
                <div class="relative">
                    <div class="bg-white p-2 sm:p-4 rounded-full shadow-[0_10px_30px_rgba(0,0,0,0.05)] border border-black/[0.08]">
                        <form action="{{ route('product-search') }}" method="GET" class="flex items-center gap-2" @submit="loading = true">
                            <div class="relative flex-1 w-full flex items-center">
                                <span class="absolute left-3.5 pointer-events-none text-[#86868b] flex items-center">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </span>
                                <input 
                                    type="text" 
                                    name="q" 
                                    x-model="query"
                                    @input.debounce.300ms="fetchSuggestions()"
                                    placeholder="{{ __('Поиск...') }}" 
                                    required
                                    maxlength="200"
                                    x-ref="searchInput"
                                    class="w-full pl-12 pr-12 py-2 bg-transparent border-0 app-text-title text-xl text-[#1d1d1f] placeholder-[#86868b] focus:outline-none focus:ring-0"
                                >
                                <button type="submit" aria-label="{{ __('Найти') }}" class="absolute right-3.5 p-2 text-[#0071e3] hover:opacity-85 transition-opacity cursor-pointer">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Выпадающий список автокомплита -->
                    <div x-show="suggestions.length > 0" x-cloak class="absolute left-0 right-0 mt-2 bg-white rounded-2xl shadow-xl border border-black/[0.06] overflow-hidden z-50">
                        <template x-for="item in suggestions" :key="item.value">
                            <div @click="selectSuggestion(item.value)" class="px-5 py-3 hover:bg-[#f5f5f7] flex items-center justify-between cursor-pointer transition-colors border-b border-black/[0.03] last:border-b-0">
                                <span class="app-text-body font-medium text-[#1d1d1f]" x-text="item.label"></span>
                                <span class="text-xs px-2.5 py-1 bg-[#0071e3]/10 text-[#0071e3] rounded-full font-semibold" x-text="item.badge"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Динамические популярные запросы из learned_autocomplete -->
                @if($quickQueries->isNotEmpty())
                    <div class="pt-4 space-y-3 items-center">
                        <h3 class="app-badge-text text-[#86868b] text-center">{{ __('Популярные запросы') }}</h3>
                        <div class="flex flex-wrap gap-2 justify-center">
                            @foreach($quickQueries as $item)
                                <a href="{{ route('product-search', ['q' => $item->query]) }}" 
                                @click="searchOpen = false; loading = true;" 
                                class="px-4 py-2 rounded-full bg-black/[0.04] hover:bg-black/[0.08] app-text-sub font-medium text-[#1d1d1f] transition-colors">
                                    {{ $item->query }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>