<x-layouts.public 
    title="{{ __('Умный поиск товаров в Греции') }} | Exereto"
    description="{{ __('Поиск товаров в интернет-магазинах Греции с выкупом по ссылке и форвардингом.') }}"
>

    <div class="min-h-screen bg-[#f5f5f7] text-[#1d1d1f] antialiased selection:bg-[#0071e3] selection:text-white pb-20 space-y-1 relative"
         x-data="{ loading: false }"
         @pageshow.window="loading = false"
         @submit.window="loading = true"
         @search-loading.window="loading = true"
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
        
        <!-- Блок 1: Поисковый Spotlight -->
        <x-public-ui.search-spotlight />

        <!-- Блок 2: Категории каталога -->
        <x-public-ui.categories-grid :categories="$categories" />

        <!-- Блок 3: Бренды и сервисы (Buy For Me / Forwarding) -->
        <x-public-ui.brands-section :brands="$brands" />

        <!-- Блок 4: Слайдер популярных товаров -->
        <x-public-ui.showcase-slider />

        <!-- Блок 5: Мессенджеры -->
        <section class="max-w-3xl mx-auto px-6 py-10 text-center space-y-4">
            <div class="space-y-3 pt-2">
                <p class="apple-hero-note text-sm sm:text-base">
                    {{ __('Мы на связи и готовы помочь с любым вопросом по выкупу и доставке') }}
                </p>
                <x-messenger-row align="justify-center" />
            </div>
        </section>

    </div>

</x-layouts.public>