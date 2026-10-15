<x-layouts.public 
    title="{{ __('Buyer Greece → Georgia | Выкуп и доставка товаров из Греции') }}"
    description="{{ __('Сервис выкупа и приема покупок в интернет-магазинах Греции с последующей доставкой в Грузию через Camex.') }}"
>

<!-- BLOCK 1: HERO SECTION -->
<section class="apple-hero-section relative bg-[#f2f2f2] overflow-hidden">
    
    <div class="hidden lg:block absolute inset-0 hero-desktop-bg"
         style="background-image: url('{{ asset('images/hero-bg-apple.jpg') }}');">
        <div class="absolute inset-0 bg-gradient-to-r from-[#f2f2f2] via-[#f2f2f2]/30 via-50% to-transparent pointer-events-none"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 pt-10 lg:py-24">
        <div class="grid lg:grid-cols-12 gap-8 items-center">
            
            <div class="lg:col-span-7 text-center lg:text-left space-y-6">
                
                <h1 class="apple-hero-title">
                    {{ __('Доставка и выкуп любых товаров из магазинов Греции') }}
                </h1>

                <p class="apple-hero-subtitle mx-auto lg:mx-0">
                    {{ __('Мы помогаем выкупать товары или предоставляем греческий адрес для самостоятельных покупок. Международную перевозку в Грузию выполняет транспортный партнер Camex.') }}
                </p>

                <!-- Форма поиска -->
                <div class="pt-2 max-w-xl mx-auto lg:mx-0">
                    <form action="{{ route('buy-for-me') }}" method="GET" class="apple-search-form">
                        <input 
                            type="url" 
                            name="url" 
                            placeholder="{{ __('Вставьте ссылку на товар...') }}" 
                            required
                            class="apple-search-input"
                        >
                        <button type="submit" class="apple-search-button">
                            {{ __('Рассчитать') }}
                        </button>
                    </form>
                </div>

                <!-- Кнопки связи -->
                <div class="flex pt-2 text-xs text-[#86868b] items-center justify-center lg:justify-start gap-4">
                    <span>{{ __('Нужна помощь с выбором?') }}</span>
                    <div class="flex items-center gap-2">
                        <x-messenger type="telegram" size="small" />
                        <x-messenger type="whatsapp" size="small" />
                        <!-- Добавлен Messenger, если настроен -->
                        @if(config('services.messenger.username') || config('services.facebook.messenger'))
                            <x-messenger type="messenger" size="small" />
                        @endif
                    </div>
                </div>

            </div>

            <div class="hidden lg:block lg:col-span-5 pointer-events-none"></div>

        </div>
    </div>

    <div class="hero-mobile-bg relative mt-1"
         style="background-image: url('{{ asset('images/hero-bg-apple-vert.jpg') }}');">
        <div class="absolute top-0 inset-x-0 h-16 bg-gradient-to-b from-[#f2f2f2] via-[#f2f2f2]/60 to-transparent pointer-events-none"></div>
    </div>

</section>

<!-- BLOCK 1.5: POPULAR PRODUCTS SHOWCASE (EDGE-TO-EDGE SCROLLER) -->
@php
    $popularProducts = \App\Models\ShowcaseProduct::where('is_published', true)->orderBy('sort_order')->get();
@endphp

@if($popularProducts->isNotEmpty())
<section class="py-16 bg-gradient-to-b from-gray-50 to-white border-b border-gray-100 overflow-hidden">
    
    <!-- Заголовок и кнопки прокрутки оставляем внутри сетки max-w-7xl -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-blue-600 text-xs font-bold uppercase tracking-wider mb-3">
                    <span class="w-2 h-2 rounded-full bg-blue-600 animate-ping"></span>
                    {{ __('Тренды и Выгода') }}
                </div>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">
                    {{ __('Популярные товары') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1 font-medium">
                    {{ __('Лучшие находки из греческих интернет-магазинов с доставкой') }}
                </p>
            </div>
            
            @if($popularProducts->count() > 1)
            <div class="flex gap-2">
                <button onclick="document.getElementById('showcase-scroll-container').scrollBy({left: -320, behavior: 'smooth'})" 
                        class="p-3 rounded-2xl bg-white text-gray-800 shadow-md border border-gray-100 hover:bg-blue-600 hover:text-white transition-all cursor-pointer" 
                        aria-label="Назад">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button onclick="document.getElementById('showcase-scroll-container').scrollBy({left: 320, behavior: 'smooth'})" 
                        class="p-3 rounded-2xl bg-white text-gray-800 shadow-md border border-gray-100 hover:bg-blue-600 hover:text-white transition-all cursor-pointer" 
                        aria-label="Вперед">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
            @endif
        </div>
    </div>

    <!-- Скроллер на всю ширину экрана с центрированием карточек -->
    <div class="w-full">
        <div id="showcase-scroll-container" 
             class="flex overflow-x-auto space-x-6 py-8 -my-4 px-4 sm:px-8 lg:px-[max(2rem,calc((100vw-80rem)/2))] no-scrollbar snap-x snap-center scroll-smooth">
            @foreach($popularProducts as $product)
                <div class="snap-center flex-shrink-0 w-[85%] sm:w-[45%] lg:w-[28%] min-w-[280px]">
                    @include('components.showcase-card', ['product' => $product])
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- BLOCK 2: BENTO GRID SCENARIOS -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-14">
            <h2 class="apple-section-title">{{ __('3 формата работы') }}</h2>
            <p class="apple-section-subtitle">{{ __('Выберите подходящий вариант сервиса') }}</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <!-- Scenario 1 -->
            <div class="apple-bento-card">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-white text-[#1d1d1f] shadow-sm flex items-center justify-center font-semibold text-lg mb-6 border border-black/[0.04]">01</div>
                    <h3 class="text-xl font-semibold text-[#1d1d1f] mb-3 tracking-tight">{{ __('Выкупите за меня') }}</h3>
                    <p class="text-sm text-[#86868b] leading-relaxed mb-8">
                        {{ __('Отправьте ссылку на товар. Мы согласуем точную калькуляцию (Quote) и организуем выкуп.') }}
                    </p>
                </div>
                <a href="{{ route('buy-for-me') }}" class="inline-flex items-center text-sm font-semibold text-[#0071e3] hover:text-[#0077ed] group">
                    <span>{{ __('Отправить ссылку') }}</span>
                    <span class="ml-1 transition-transform duration-200 group-hover:translate-x-1">→</span>
                </a>
            </div>

            <!-- Scenario 2 -->
            <div class="apple-bento-card">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-white text-[#1d1d1f] shadow-sm flex items-center justify-center font-semibold text-lg mb-6 border border-black/[0.04]">02</div>
                    <h3 class="text-xl font-semibold text-[#1d1d1f] mb-3 tracking-tight">{{ __('Помогите купить') }}</h3>
                    <p class="text-sm text-[#86868b] leading-relaxed mb-8">
                        {{ __('Нужна консультация по выбору продавца или подбору размера? Менеджеры помогут в чате.') }}
                    </p>
                </div>
                <div class="flex gap-3">
                    <x-messenger type="telegram" size="small" />
                    <x-messenger type="whatsapp" size="small" />
                </div>
            </div>

            <!-- Scenario 3 -->
            <div class="apple-bento-card">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-white text-[#1d1d1f] shadow-sm flex items-center justify-center font-semibold text-lg mb-6 border border-black/[0.04]">03</div>
                    <h3 class="text-xl font-semibold text-[#1d1d1f] mb-3 tracking-tight">{{ __('Форвардинг') }}</h3>
                    <p class="text-sm text-[#86868b] leading-relaxed mb-8">
                        {{ __('Оплачивайте покупки самостоятельно и отправляйте их на наш адрес склада в Греции.') }}
                    </p>
                </div>
                <a href="{{ route('forwarding') }}" class="inline-flex items-center text-sm font-semibold text-[#0071e3] hover:text-[#0077ed] group">
                    <span>{{ __('Получить адрес') }}</span>
                    <span class="ml-1 transition-transform duration-200 group-hover:translate-x-1">→</span>
                </a>
            </div>
        </div>
    </div>
</section>

</x-layouts.public>