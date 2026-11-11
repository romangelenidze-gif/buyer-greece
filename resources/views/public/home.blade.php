<x-layouts.public 
    title="{{ __('Официальный каталог Adidas в Греции') }} | Exereto"
    description="{{ __('Поиск и подбор товаров из официального магазина Adidas через партнерскую сеть Awin.') }}"
>

    <div class="min-h-screen bg-[#f5f5f7] text-[#1d1d1f] antialiased pb-20 space-y-12 relative"
         x-data="{ loading: false }"
         @pageshow.window="loading = false"
         @submit.window="loading = true"
         @search-loading.window="loading = true"
    >
        
        <!-- Оверлей загрузки -->
        <template x-teleport="body">
            <div x-show="loading" x-cloak class="fixed inset-0 z-[9999] bg-white/80 backdrop-blur-md flex flex-col items-center justify-center space-y-4">
                <svg class="animate-spin h-10 w-10 text-[#0071e3]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-base font-semibold text-[#1d1d1f] animate-pulse">{{ __('Загрузка каталога Adidas...') }}</span>
            </div>
        </template>
        
        <!-- Поисковый Spotlight -->
        <x-public-ui.search-spotlight />

        <!-- Баннер Adidas (с изоляцией стилей) -->
        <section class="max-w-7xl mx-auto px-6 pt-4">
            <div class="adidas-hero-banner">
                @if(!empty($promoBanner['image_url']))
                    <img src="{{ $promoBanner['image_url'] }}" alt="{{ $promoBanner['title'] }}">
                    <div class="banner-overlay"></div>
                @endif
                <div class="banner-content space-y-4">
                    <span class="inline-block text-xs font-bold uppercase tracking-widest bg-white/20 backdrop-blur-md px-3.5 py-1.5 rounded-full text-white border border-white/20">
                        Official Partner
                    </span>
                    <h1 class="text-3xl sm:text-5xl font-black tracking-tight leading-tight text-white">
                        {{ $promoBanner['title'] }}
                    </h1>
                    <p class="text-zinc-200 text-sm sm:text-base leading-relaxed">
                        {{ $promoBanner['subtitle'] }}
                    </p>
                    <div class="pt-2">
                        <a href="{{ $promoBanner['link'] }}" class="adidas-btn-black">
                            {{ __('Смотреть весь каталог') }}
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Популярные модели Adidas -->
        @if(isset($popularProducts) && $popularProducts->isNotEmpty())
        <section class="max-w-7xl mx-auto px-6 space-y-6">
            <div class="flex justify-between items-end">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-zinc-900">{{ __('Популярные модели Adidas') }}</h2>
                    <p class="text-sm text-zinc-500 mt-1">{{ __('Хиты продаж из официального фида Awin') }}</p>
                </div>
                <a href="{{ route('product-search', ['q' => 'adidas']) }}" class="text-sm font-semibold text-zinc-900 hover:underline">
                    {{ __('Все товары') }} &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($popularProducts as $index => $product)
                    <x-public-ui.product-card :product="$product" :is-first="$index === 0" />
                @endforeach
            </div>
        </section>
        @endif

        <!-- Специальные скидки и акции -->
        @if(isset($discountProducts) && $discountProducts->isNotEmpty())
        <section class="max-w-7xl mx-auto px-6 space-y-6">
            <div class="flex justify-between items-end">
                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-red-600">Special Sales</span>
                    <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-zinc-900 mt-1">{{ __('Скидки и специальные предложения') }}</h2>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($discountProducts as $product)
                    <x-public-ui.product-card :product="$product" />
                @endforeach
            </div>
        </section>
        @endif

        <!-- Мессенджеры -->
        <section class="max-w-3xl mx-auto px-6 py-10 text-center space-y-4">
            <div class="space-y-3 pt-2">
                <p class="text-sm sm:text-base text-zinc-600">
                    {{ __('Мы на связи и готовы помочь с любым вопросом по выкупу и доставке') }}
                </p>
                <x-messenger-row align="justify-center" />
            </div>
        </section>

    </div>

</x-layouts.public>