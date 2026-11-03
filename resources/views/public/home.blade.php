<x-layouts.public 
    title="{{ __('Умный поиск товаров в Греции') }} | Exereto"
    description="{{ __('Поиск товаров в интернет-магазинах Греции с выкупом по ссылке и форвардингом.') }}"
>

    <div class="min-h-screen bg-[#fbfbfd] text-[#1d1d1f] antialiased selection:bg-[#0071e3] selection:text-white pb-20 space-y-1">
        
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