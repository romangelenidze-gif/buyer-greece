<x-layouts.public 
    title="{{ __('Умный поиск товаров в Греции') }} | Buyer Greece"
    description="{{ __('Найдите нужный товар на естественном языке с помощью ИИ и отправьте его на выкуп.') }}"
>

<div class="min-h-[calc(100vh-4rem)] bg-[#fbfbfd] text-[#1d1d1f] antialiased selection:bg-[#0071e3] selection:text-white py-12 px-6 sm:px-8 lg:px-8 relative" 
     x-data="{ 
        loading: false, 
        sortModalOpen: false,
        query: '{{ $query ?? '' }}',
        suggestions: [],
        loadingSuggestions: false,
        activeSort: '{{ request('sort', $filters['sort'] ?? 'relevance') }}',
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
        },
        sortProducts(sortType) {
            this.activeSort = sortType;
            this.sortModalOpen = false;
            
            const grid = document.getElementById('products-grid');
            if (!grid) return;
            
            const wrappers = Array.from(grid.children);
            wrappers.sort((a, b) => {
                const priceA = parseFloat(a.dataset.price || 0);
                const priceB = parseFloat(b.dataset.price || 0);
                const indexA = parseInt(a.dataset.index || 0);
                const indexB = parseInt(b.dataset.index || 0);
                
                if (sortType === 'price_asc') return priceA - priceB;
                if (sortType === 'price_desc') return priceB - priceA;
                return indexA - indexB;
            });
            
            wrappers.forEach(wrapper => grid.appendChild(wrapper));
        }
     }"
     x-init="loading = false"
     @pageshow.window="loading = false"
>
    
    <!-- МГНОВЕННЫЙ ПОЛНОЭКРАННЫЙ ОВЕРЛЕЙ ЗАГРУЗКИ ПРИ ПОИСКЕ -->
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
        
        <!-- HEADER -->
        @if(is_null($searchResult))
            <div class="text-center space-y-3 max-w-2xl mx-auto">
                <h1 class="apple-section-title text-left sm:text-center">
                    {{ __('Умный поиск в Греции.') }}
                </h1>
                <p class="apple-hero-subtitle text-left sm:text-center mx-auto">
                    {{ __('Опишите любой товар на вашем языке. Наш ИИ подберет предложения в греческих магазинах с чистыми ценами и прямыми ссылками.') }}
                </p>
            </div>
        @endif

        <!-- SEARCH FORM & FILTERS (ДОБАВЛЕН ОТСТУП СНИЗУ MB-10) -->
        <div class="max-w-3xl mx-auto mb-1 space-y-4">
            <form action="{{ route('product-search') }}" method="GET" x-ref="searchForm" @submit="loading = true">
                <input type="hidden" name="sort" :value="activeSort">

                <!-- SEARCH INPUT WITH AUTOCOMPLETE & SUBMIT BUTTON -->
                <div class="relative">
                    <div class="apple-search-bar flex items-center justify-between">
                        <input 
                            type="text" 
                            name="q" 
                            x-model="query"
                            @input.debounce.300ms="fetchSuggestions()"
                            placeholder="{{ __('Назовите товар и нажмите Enter...') }}" 
                            required
                            maxlength="200"
                            autofocus
                            class="apple-search-input flex-1"
                        >
                        <button type="submit" aria-label="{{ __('Найти') }}" class="p-2 text-[#0071e3] hover:opacity-85 transition-opacity cursor-pointer shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </div>

                    <!-- ВЫПАДАЮЩИЙ СПИСОК АВТОКОМПЛИТА НА СТРАНИЦЕ ПОИСКА -->
                    <div x-show="suggestions.length > 0" x-cloak class="absolute left-0 right-0 mt-2 bg-white rounded-2xl shadow-xl border border-black/[0.06] overflow-hidden z-50">
                        <template x-for="item in suggestions" :key="item.value">
                            <div @click="selectSuggestion(item.value)" class="px-5 py-3 hover:bg-[#f5f5f7] flex items-center justify-between cursor-pointer transition-colors border-b border-black/[0.03] last:border-b-0">
                                <span class="app-text-body font-medium text-[#1d1d1f]" x-text="item.label"></span>
                                <span class="text-xs px-2.5 py-1 bg-[#0071e3]/10 text-[#0071e3] rounded-full font-semibold" x-text="item.badge"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- FILTERS BAR -->
                <div class="flex items-center gap-2 pt-3 pb-2 text-xs overflow-x-auto no-scrollbar">
                    
                    <!-- MIN PRICE -->
                    <div class="apple-filter-pill shrink-0">
                        <input 
                            type="number" 
                            name="min_price" 
                            value="{{ request('min_price', $filters['min_price'] ?? '') }}" 
                            placeholder="{{ __('от') }}" 
                            step="0.01" 
                            min="0"
                            @change="loading = true; $refs.searchForm.submit()"
                            class="apple-filter-input"
                        >
                        <span class="text-[#86868b] text-xs ml-1.5 font-normal">€</span>
                    </div>

                    <!-- MAX PRICE -->
                    <div class="apple-filter-pill shrink-0">
                        <input 
                            type="number" 
                            name="max_price" 
                            value="{{ request('max_price', $filters['max_price'] ?? '') }}" 
                            placeholder="{{ __('до') }}" 
                            step="0.01" 
                            min="0"
                            @change="loading = true; $refs.searchForm.submit()"
                            class="apple-filter-input"
                        >
                        <span class="text-[#86868b] text-xs ml-1.5 font-normal">€</span>
                    </div>

                    <!-- ДЕСКТОП: СЕЛЕКТ (СКРЫТ НА МОБИЛЬНЫХ) -->
                    <div class="hidden sm:flex ml-auto items-center">
                        <div class="apple-filter-pill shrink-0 px-4 py-2">
                            <select 
                                x-model="activeSort"
                                @change="sortProducts(activeSort)"
                                class="bg-transparent border-0 p-0 text-xs font-medium text-[#1d1d1f] focus:outline-none focus:ring-0 cursor-pointer pr-1"
                            >
                                <option value="relevance">{{ __('По релевантности') }}</option>
                                <option value="price_asc">{{ __('Сначала дешевле') }}</option>
                                <option value="price_desc">{{ __('Сначала дороже') }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- МОБИЛКА: КНОПКА СО СТРЕЛОЧКАМИ (ОТКРЫВАЕТ ШТОРКУ) -->
                    <button 
                        type="button" 
                        @click="sortModalOpen = true"
                        class="flex sm:hidden w-8 h-8 rounded-full bg-[#e5e5ea] active:bg-[#d1d1d6] items-center justify-center text-[#1d1d1f] transition-all shrink-0 ml-auto"
                        title="{{ __('Сортировка') }}"
                    >
                        <svg class="w-4 h-4 text-[#1d1d1f]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                        </svg>
                    </button>

                    <!-- RESET FILTERS BUTTON -->
                    @if(request()->hasAny(['min_price', 'max_price']) || (request('sort') && request('sort') !== 'relevance'))
                        <a 
                            href="{{ route('product-search', ['q' => $query]) }}" 
                            @click="loading = true"
                            class="w-8 h-8 rounded-full bg-[#f2f2f7] hover:bg-[#e5e5ea] text-[#86868b] hover:text-[#1d1d1f] flex items-center justify-center transition-all shrink-0 ml-1"
                            title="{{ __('Сбросить фильтры') }}"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    @endif

                </div>

            </form>
        </div>

        <!-- MOBILE SORT MODAL COMPONENT -->
        <x-public-ui.sort-modal />

        <!-- RESULTS SECTION -->
        <div>
            @if(!is_null($searchResult))
                <div class="space-y-6">
                    
                    <!-- RECOMMENDATION BANNER -->
                    <div class="apple-recommendation-box">
                        <div class="w-8 h-8 rounded-full bg-[#0071e3]/10 text-[#0071e3] flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="text-sm">
                            <p class="text-[#1d1d1f] font-medium m-0">{{ $searchResult['recommendation'] }}</p>
                            @if(!empty($searchResult['intent_analysis']['brand']))
                                <p class="text-[#86868b] text-xs m-0 mt-0.5">
                                    {{ __('Уточнен бренд:') }} <span class="font-semibold text-[#1d1d1f]">{{ $searchResult['intent_analysis']['brand'] }}</span>
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- PRODUCTS GRID WITH ROBUST SORTING WRAPPERS -->
                    @if(!empty($searchResult['products']))
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6" id="products-grid">
                            @foreach($searchResult['products'] as $product)
                                <div data-price="{{ $product['price_amount'] ?? $product['price'] ?? 0 }}" data-index="{{ $loop->index }}">
                                    <x-public-ui.product-card :product="$product" />
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="apple-card text-center max-w-md mx-auto space-y-2 py-10 bg-white rounded-3xl border border-black/[0.06]">
                            <h4 class="app-text-title font-semibold">{{ __('Ничего не найдено') }}</h4>
                            <p class="app-text-sub text-sm text-[#86868b]">{{ __('По вашему запросу ничего не найдено в каталоге BestPrice. Попробуйте изменить параметры поиска или ценовой диапазон.') }}</p>
                        </div>
                    @endif

                </div>
            @endif
        </div>

    </div>
</div>

</x-layouts.public>