<x-layouts.public 
    title="{{ __('Умный поиск товаров в Греции') }} | Buyer Greece"
    description="{{ __('Найдите нужный товар на естественном языке с помощью ИИ и отправьте его на выкуп.') }}"
>

<div class="min-h-[calc(100vh-4rem)] bg-[#fbfbfd] text-[#1d1d1f] antialiased selection:bg-[#0071e3] selection:text-white py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto space-y-10">
        
        <!-- APPLE HERO HEADER -->
        <div class="text-center space-y-3 max-w-2xl mx-auto">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#0071e3]/10 text-[#0071e3] tracking-wide uppercase">
                {{ __('Greece to Georgia • AI Shopping') }}
            </span>
            <h1 class="text-3xl sm:text-4xl font-semibold tracking-tight text-[#1d1d1f]">
                {{ __('Умный поиск в Греции.') }}
            </h1>
            <p class="text-sm sm:text-base text-[#86868b] font-normal leading-relaxed">
                {{ __('Опишите любой товар на вашем языке. Наш ИИ подберет предложения в греческих магазинах с чистыми ценами и прямыми ссылками.') }}
            </p>
        </div>

        <!-- SEARCH BAR -->
        <div class="max-w-2xl mx-auto">
            <div class="bg-white/80 backdrop-blur-2xl p-3 sm:p-4 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.06)] border border-black/[0.06]">
                <form action="{{ route('product-search') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3" x-data="{ loading: false }" @submit="loading = true">
                    <div class="relative flex-1 w-full">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-[#86868b]">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input 
                            type="text" 
                            name="q" 
                            value="{{ $query ?? '' }}"
                            placeholder="{{ __('Например: оливковое масло 5л или кроссовки Adidas...') }}" 
                            required
                            maxlength="200"
                            class="w-full pl-11 pr-4 py-3 bg-[#f5f5f7] border-0 rounded-2xl text-[#1d1d1f] placeholder-[#86868b] text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0071e3]/30 transition-all"
                        >
                    </div>
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-[#0071e3] hover:bg-[#0077ed] active:scale-[0.98] text-white text-sm font-medium rounded-2xl shadow-sm transition-all duration-200 min-w-[130px] cursor-pointer">
                        <span x-show="!loading">{{ __('Найти') }}</span>
                        <span x-show="loading" x-cloak class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ __('Поиск...') }}
                        </span>
                    </button>
                </form>
            </div>
        </div>

        <!-- RESULTS SECTION -->
        @if(!is_null($searchResult))
            <div class="space-y-6 animate-fade-in">
                
                <!-- RECOMMENDATION BANNER -->
                <div class="max-w-3xl mx-auto bg-white/70 backdrop-blur-xl border border-black/[0.04] px-5 py-4 rounded-2xl shadow-xs flex items-center gap-3.5">
                    <div class="w-8 h-8 rounded-xl bg-[#0071e3]/10 text-[#0071e3] flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-xs sm:text-sm text-[#86868b] leading-relaxed m-0">{{ $searchResult['recommendation'] }}</p>
                </div>

                <!-- PRODUCTS GRID (COMPACT & ELEGANT) -->
                @if(!empty($searchResult['products']))
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        @foreach($searchResult['products'] as $product)
                            <div class="bg-white rounded-2xl p-5 border border-black/[0.05] shadow-[0_2px_12px_rgba(0,0,0,0.02)] hover:shadow-[0_8px_24px_rgba(0,0,0,0.06)] transition-all duration-300 flex flex-col justify-between group">
                                <div class="space-y-2.5">
                                    <!-- Header: Store domain & Price -->
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-semibold text-[#86868b] tracking-wider uppercase">
                                            {{ $product['merchant_name'] }}
                                        </span>
                                        <div>
                                            @if(!empty($product['price_amount']))
                                                <span class="text-lg font-bold text-[#1d1d1f] tracking-tight">€{{ number_format($product['price_amount'], 2) }}</span>
                                            @else
                                                <span class="text-xs font-semibold text-[#86868b] bg-[#f5f5f7] px-2 py-1 rounded-md">{{ $product['price_note'] ?: __('Уточняется') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Title -->
                                    <h3 class="text-sm sm:text-base font-semibold text-[#1d1d1f] line-clamp-2 tracking-tight group-hover:text-[#0071e3] transition-colors leading-snug">
                                        {{ $product['title'] }}
                                    </h3>
                                    
                                    <!-- Description -->
                                    <p class="text-xs text-[#86868b] line-clamp-2 leading-relaxed font-normal">
                                        {{ $product['short_description'] }}
                                    </p>
                                </div>

                                <!-- Actions -->
                                <div class="mt-5 pt-3.5 border-t border-black/[0.04] grid grid-cols-2 gap-2">
                                    <a href="{{ $product['product_url'] }}" target="_blank" rel="noopener noreferrer" 
                                       class="inline-flex justify-center items-center px-3 py-2.5 text-xs font-medium text-[#1d1d1f] bg-[#f5f5f7] hover:bg-[#e8e8ed] active:scale-[0.97] rounded-xl transition-all">
                                        {{ __('В магазин') }}
                                    </a>
                                    <a href="{{ route('buy-for-me') }}?url={{ urlencode($product['product_url']) }}" 
                                       class="inline-flex justify-center items-center px-3 py-2.5 text-xs font-medium text-white bg-[#0071e3] hover:bg-[#0077ed] active:scale-[0.97] rounded-xl shadow-sm transition-all">
                                        {{ __('В заказ') }}
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white rounded-2xl p-10 text-center border border-black/[0.05] max-w-md mx-auto space-y-2">
                        <h4 class="text-sm font-semibold text-[#1d1d1f]">{{ __('Ничего не найдено') }}</h4>
                        <p class="text-xs text-[#86868b]">{{ __('По вашему запросу ничего не найдено. Попробуйте изменить формулировку.') }}</p>
                    </div>
                @endif

            </div>
        @endif

    </div>
</div>

</x-layouts.public>