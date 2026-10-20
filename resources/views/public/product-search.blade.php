<x-layouts.public 
    title="{{ __('Умный поиск товаров в Греции') }} | Buyer Greece"
    description="{{ __('Найдите нужный товар на естественном языке с помощью ИИ и отправьте его на выкуп.') }}"
>

<div class="min-h-[calc(100vh-4rem)] bg-[#fbfbfd] text-[#1d1d1f] antialiased selection:bg-[#0071e3] selection:text-white py-12 px-6 sm:px-8 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-10">
        
        <!-- APPLE HERO HEADER -->
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

        <!-- SEARCH BAR & FILTERS FORM -->
        <div class="max-w-3xl mx-auto space-y-4">
            <form action="{{ route('product-search') }}" method="GET" x-data="{ loading: false }" @submit="loading = true">
                
                <!-- MAIN SEARCH INPUT -->
                <div class="bg-white p-3.5 sm:p-4 rounded-full shadow-[0_10px_30px_rgba(0,0,0,0.05)] border border-black/[0.08] flex items-center">
                    <div class="relative flex-1 w-full flex items-center">
                        <span class="absolute left-3.5 pointer-events-none text-[#86868b] flex items-center">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input 
                            type="text" 
                            name="q" 
                            value="{{ $query ?? '' }}"
                            placeholder="{{ __('Например: оливковое масло 5л или кроссовки Adidas...') }}" 
                            required
                            maxlength="200"
                            class="w-full pl-12 pr-4 py-2 bg-transparent border-0 app-text-title text-[#1d1d1f] placeholder-[#86868b] focus:outline-none focus:ring-0"
                        >
                    </div>
                    <button 
                        type="submit" 
                        class="apple-button-primary shrink-0 ml-2 !py-2.5 !px-5"
                    >
                        <span x-show="!loading">{{ __('Искать') }}</span>
                        <span x-show="loading" x-cloak>{{ __('Поиск...') }}</span>
                    </button>
                </div>

                <!-- DYNAMIC FILTERS PANEL -->
                <div class="bg-white/80 backdrop-blur-md p-4 rounded-2xl border border-black/[0.06] shadow-sm flex flex-wrap items-center justify-between gap-4 mt-4">
                    
                    <!-- PRICE RANGE -->
                    <div class="flex items-center gap-2">
                        <span class="app-text-sub text-[#6e6e73] font-medium">{{ __('Цена (€):') }}</span>
                        <input 
                            type="number" 
                            name="min_price" 
                            value="{{ request('min_price', $filters['min_price'] ?? '') }}" 
                            placeholder="{{ __('От') }}" 
                            step="0.01" 
                            min="0"
                            class="w-20 px-3 py-1.5 text-sm rounded-xl border border-black/[0.1] bg-[#fbfbfd] focus:outline-none focus:ring-2 focus:ring-[#0071e3]/30"
                        >
                        <span class="text-[#86868b]">—</span>
                        <input 
                            type="number" 
                            name="max_price" 
                            value="{{ request('max_price', $filters['max_price'] ?? '') }}" 
                            placeholder="{{ __('До') }}" 
                            step="0.01" 
                            min="0"
                            class="w-20 px-3 py-1.5 text-sm rounded-xl border border-black/[0.1] bg-[#fbfbfd] focus:outline-none focus:ring-2 focus:ring-[#0071e3]/30"
                        >
                    </div>

                    <!-- SORTING -->
                    <div class="flex items-center gap-2">
                        <span class="app-text-sub text-[#6e6e73] font-medium">{{ __('Сортировка:') }}</span>
                        <select 
                            name="sort" 
                            onchange="this.form.submit()"
                            class="px-3 py-1.5 text-sm rounded-xl border border-black/[0.1] bg-[#fbfbfd] focus:outline-none focus:ring-2 focus:ring-[#0071e3]/30 text-[#1d1d1f] cursor-pointer"
                        >
                            <option value="relevance" {{ request('sort', $filters['sort'] ?? '') === 'relevance' ? 'selected' : '' }}>
                                {{ __('По релевантности') }}
                            </option>
                            <option value="price_asc" {{ request('sort', $filters['sort'] ?? '') === 'price_asc' ? 'selected' : '' }}>
                                {{ __('Сначала дешевле') }}
                            </option>
                            <option value="price_desc" {{ request('sort', $filters['sort'] ?? '') === 'price_desc' ? 'selected' : '' }}>
                                {{ __('Сначала дороже') }}
                            </option>
                        </select>
                    </div>

                    <!-- RESET FILTERS -->
                    @if(request()->hasAny(['min_price', 'max_price', 'sort']) && (request('min_price') || request('max_price') || request('sort') !== 'relevance'))
                        <div>
                            <a 
                                href="{{ route('product-search', ['q' => $query]) }}" 
                                class="text-xs font-medium text-[#0071e3] hover:underline"
                            >
                                {{ __('Сбросить фильтры') }}
                            </a>
                        </div>
                    @endif

                </div>

            </form>
        </div>

        <!-- RESULTS SECTION -->
        @if(!is_null($searchResult))
            <div class="space-y-6 animate-fade-in">
                
                <!-- RECOMMENDATION BANNER -->
                <div class="max-w-3xl flex items-center gap-3 bg-white/60 backdrop-blur-sm p-3.5 rounded-2xl border border-black/[0.04]">
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

                <!-- PRODUCTS GRID -->
                @if(!empty($searchResult['products']))
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @foreach($searchResult['products'] as $product)
                            <x-public-ui.product-card :product="$product" />
                        @endforeach
                    </div>
                @else
                    <div class="apple-card text-center max-w-md mx-auto space-y-2 py-10 bg-white">
                        <h4 class="app-text-title">{{ __('Ничего не найдено') }}</h4>
                        <p class="app-text-sub">{{ __('По вашему запросу ничего не найдено в каталоге BestPrice. Попробуйте изменить параметры поиска или ценовой диапазон.') }}</p>
                    </div>
                @endif

            </div>
        @endif

    </div>
</div>

</x-layouts.public>