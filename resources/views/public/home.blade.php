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

                <!-- Кнопки связи (теперь видны и на мобильной, и на десктопной версиях) -->
                <div class="flex pt-2 text-xs text-[#86868b] items-center justify-center lg:justify-start gap-4">
                    <span>{{ __('Нужна помощь с выбором?') }}</span>
                    <div class="flex items-center gap-2">
                        <x-messenger type="telegram" size="small" />
                        <x-messenger type="whatsapp" size="small" />
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

<!-- BLOCK 2: BENTO GRID SCENARIOS -->
<section class="py-20 bg-white">
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