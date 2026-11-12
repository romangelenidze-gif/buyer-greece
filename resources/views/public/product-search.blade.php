<x-layouts.public 
    title="{{ __('Умный поиск товаров в Греции') }} | Buyer Greece"
    description="{{ __('Найдите нужный товар на естественном языке с помощью ИИ и отправьте его на выкуп.') }}"
>

@php
    $pagination = $searchResult['pagination'] ?? ['total' => 0, 'per_page' => 24, 'current_page' => 1, 'last_page' => 1];
    $initialSort = $filters['sort'] ?? request('sort', 'relevance');
    $baseAction = request()->routeIs('feed-category.show') ? url()->current() : route('product-search');
@endphp

<div class="min-h-[calc(100vh-4rem)] bg-[#f5f5f7] text-[#1d1d1f] antialiased selection:bg-[#0071e3] selection:text-white py-12 px-6 sm:px-8 lg:px-8 relative" 
     x-data="{ 
        loading: false, 
        loadingMore: false,
        sortModalOpen: false,
        initialQuery: @js($query ?? ''),
        query: @js($query ?? ''),
        suggestions: [],
        loadingSuggestions: false,
        abortCtrl: null,
        activeSort: @js($initialSort),
        currentPage: {{ (int) ($pagination['current_page'] ?? 1) }},
        lastPage: {{ (int) ($pagination['last_page'] ?? 1) }},
        totalItems: {{ (int) ($pagination['total'] ?? 0) }},
        shownCount: {{ count($searchResult['products'] ?? []) }},
        submitSearch() {
            this.loading = true;
            if (this.query.trim() !== this.initialQuery.trim()) {
                this.$refs.searchForm.action = '{{ route('product-search') }}';
            }
            this.$nextTick(() => this.$refs.searchForm.submit());
        },
        fetchSuggestions() {
            if (this.query.trim().length < 2) {
                this.suggestions = [];
                return;
            }
            if (this.abortCtrl) this.abortCtrl.abort();
            this.abortCtrl = new AbortController();
            this.loadingSuggestions = true;

            fetch(`{{ route('api.autocomplete') }}?q=${encodeURIComponent(this.query.trim())}`, {
                signal: this.abortCtrl.signal,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept-Language': '{{ app()->getLocale() }}'
                },
                credentials: 'same-origin'
            })
                .then(res => res.json())
                .then(data => {
                    this.suggestions = data.suggestions || [];
                    this.loadingSuggestions = false;
                })
                .catch(err => {
                    if (err.name !== 'AbortError') {
                        this.suggestions = [];
                        this.loadingSuggestions = false;
                    }
                });
        },
        selectSuggestion(item) {
            this.query = item.label || item.value;
            this.suggestions = [];
            this.loading = true;
            if (item.url) {
                window.location.href = item.url;
                return;
            }
            window.location.href = `{{ route('product-search') }}?q=${encodeURIComponent(item.value)}`;
        },
        sortProducts(sortType) {
            if (this.activeSort === sortType) return;
            this.activeSort = sortType;
            this.sortModalOpen = false;
            this.loading = true;
            this.$nextTick(() => this.$refs.searchForm.submit());
        },
        loadMore() {
            if (this.loadingMore || this.currentPage >= this.lastPage) return;
            this.loadingMore = true;

            const params = new URLSearchParams(window.location.search);
            if (!window.location.pathname.startsWith('/category/')) {
                params.set('q', this.query);
            }
            params.set('sort', this.activeSort);
            params.set('page', String(this.currentPage + 1));

            fetch(`${window.location.pathname}?${params.toString()}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            })
                .then(res => res.json())
                .then(data => {
                    const grid = document.getElementById('products-grid');
                    if (grid && data.html) {
                        grid.insertAdjacentHTML('beforeend', data.html);
                    }
                    if (data.pagination) {
                        this.currentPage = data.pagination.current_page;
                        this.lastPage = data.pagination.last_page;
                        this.totalItems = data.pagination.total;
                    }
                    this.shownCount = grid ? grid.children.length : this.shownCount;
                    this.loadingMore = false;
                })
                .catch(() => {
                    this.loadingMore = false;
                });
        }
     }"
     @pageshow.window="loading = false"
>
    
    <template x-teleport="body">
        <div x-show="loading" x-cloak class="fixed inset-0 z-[9999] bg-white/80 backdrop-blur-md flex flex-col items-center justify-center space-y-4">
            <svg class="animate-spin h-10 w-10 text-[#0071e3]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-base font-semibold text-[#1d1d1f] animate-pulse">{{ __('Ищем предложения в магазинах Греции...') }}</span>
        </div>
    </template>

    <div class="max-w-7xl mx-auto space-y-8">
        
        @if(is_null($searchResult))
            <div class="text-center space-y-3 max-w-2xl mx-auto">
                <h1 class="apple-section-title text-left sm:text-center">
                    {{ __('Умный поиск в Греции.') }}
                </h1>
                <p class="apple-hero-subtitle text-left sm:text-center mx-auto">
                    {{ __('Быстрый поиск по официальным каталогам брендов в Греции с прямыми партнёрскими ссылками.') }}
                </p>
            </div>
        @endif

        <div class="max-w-3xl mx-auto mb-1 space-y-4">
            <form action="{{ $baseAction }}" method="GET" x-ref="searchForm" @submit.prevent="submitSearch()">
                
                <div class="relative" @click.outside="suggestions = []">
                    <div class="apple-search-bar flex items-center justify-between">
                        <input 
                            type="text" 
                            name="q" 
                            x-model="query"
                            @input.debounce.180ms="fetchSuggestions()"
                            placeholder="{{ __('Назовите товар и нажмите Enter...') }}" 
                            required
                            maxlength="200"
                            autocomplete="off"
                            class="apple-search-input flex-1"
                        >
                        <button type="submit" aria-label="{{ __('Найти') }}" class="p-2 text-[#0071e3] hover:opacity-85 transition-opacity cursor-pointer shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </div>

                    <div x-show="suggestions.length > 0" x-cloak class="absolute left-0 right-0 mt-2 bg-white rounded-2xl shadow-xl border border-black/[0.06] overflow-hidden z-50">
                        <template x-for="item in suggestions" :key="item.url || item.value">
                            <div @click="selectSuggestion(item)" class="px-5 py-3 hover:bg-[#f5f5f7] flex items-center justify-between gap-3 cursor-pointer transition-colors border-b border-black/[0.03] last:border-b-0">
                                <div class="flex items-center gap-3 min-w-0">
                                    <img x-show="item.image" :src="item.image" alt="" class="w-9 h-9 rounded-lg object-cover bg-[#f5f5f7] shrink-0">
                                    <span class="app-text-body font-medium text-[#1d1d1f] truncate" x-text="item.label"></span>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span x-show="item.price" class="text-xs font-semibold text-[#1d1d1f]" x-text="item.price"></span>
                                    <span class="text-xs px-2.5 py-1 bg-[#0071e3]/10 text-[#0071e3] rounded-full font-semibold" x-text="item.badge"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <x-public-ui.filter-bar :filters="$filters ?? []" :query="$query ?? ''" />

            </form>
        </div>

        <x-public-ui.sort-modal />

        <div>
            @if(!is_null($searchResult))
                <div class="space-y-6">
                    @php
                        $products = $searchResult['products'] ?? [];
                    @endphp

                    @if(!empty($products))
                        <div class="flex items-center justify-between text-xs text-[#86868b] px-1">
                            <span>
                                {{ __('Показано') }} <strong class="text-[#1d1d1f]" x-text="shownCount"></strong>
                                {{ __('из') }} <strong class="text-[#1d1d1f]" x-text="totalItems"></strong>
                            </span>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6" id="products-grid">
                        @if(!empty($products))
                            @foreach($products as $product)
                                <x-public-ui.product-card :product="$product" :is-first="$loop->first" />
                            @endforeach
                        @else
                            <div class="col-span-full apple-card text-center max-w-md mx-auto space-y-2 py-10 bg-white rounded-3xl border border-black/[0.06]">
                                <h4 class="app-text-title font-semibold">{{ __('Ничего не найдено') }}</h4>
                                <p class="app-text-sub text-sm text-[#86868b]">{{ __('По вашему запросу ничего не найдено. Попробуйте изменить параметры поиска или ценовой диапазон.') }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- КНОПКА «ПОКАЗАТЬ ЕЩЁ» -->
                    <div x-show="currentPage < lastPage" x-cloak class="flex flex-col items-center justify-center pt-4 space-y-2">
                        <button
                            type="button"
                            @click="loadMore()"
                            :disabled="loadingMore"
                            class="px-7 py-3 rounded-full bg-white hover:bg-[#e8e8ed] text-[#1d1d1f] text-sm font-semibold shadow-sm border border-black/[0.08] transition-all cursor-pointer disabled:opacity-60 flex items-center gap-2"
                        >
                            <svg x-show="loadingMore" class="animate-spin h-4 w-4 text-[#0071e3]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="loadingMore ? '{{ __('Загружаем...') }}' : '{{ __('Показать ещё') }}'"></span>
                        </button>
                    </div>

                </div>
            @endif
        </div>

    </div>
</div>

</x-layouts.public>