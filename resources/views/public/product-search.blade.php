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

        <!-- SEARCH BAR -->
        <div class="max-w-2xl mx-auto">
            <div class="bg-white p-3.5 sm:p-4 rounded-full shadow-[0_10px_30px_rgba(0,0,0,0.05)] border border-black/[0.08]">
                <form action="{{ route('product-search') }}" method="GET" class="flex items-center" x-data="{ loading: false }" @submit="loading = true">
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
                </form>
            </div>
        </div>

        <!-- RESULTS SECTION -->
        @if(!is_null($searchResult))
            <div class="space-y-6 animate-fade-in">
                
                <!-- RECOMMENDATION BANNER -->
                <div class="max-w-3xl flex items-center gap-3">
                    <div class="w-7 h-7 rounded-full bg-[#0071e3]/10 text-[#0071e3] flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-[#86868b] m-0">{{ $searchResult['recommendation'] }}</p>
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
                        <p class="app-text-sub">{{ __('По вашему запросу ничего не найдено. Попробуйте изменить формулировку.') }}</p>
                    </div>
                @endif

            </div>
        @endif

    </div>
</div>

</x-layouts.public>