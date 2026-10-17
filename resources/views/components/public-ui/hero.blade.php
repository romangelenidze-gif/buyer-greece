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

                <!-- Форма отправки ссылки -->
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
                            {{ __('Сделать заказ') }}
                        </button>
                    </form>
                </div>

                <p class="apple-hero-subtitle mx-auto lg:mx-0">
                    {{ __('Мы помогаем выкупать товары или предоставляем греческий адрес для самостоятельных покупок. Доставку по Грузии выполняет транспортный партнер') }} <strong class="text-[#1d1d1f]">Camex</strong>.
                </p>

                <!-- Список преимуществ -->
                <ul class="space-y-2.5 text-base text-[#48484a] max-w-xl mx-auto lg:mx-0 text-left">
                    <li class="flex items-center gap-2.5">
                        <span class="w-2 h-2 rounded-sm bg-[#10b981] shrink-0"></span>
                        <span>{{ __('Берем на себя оформление заказа и общение с продавцом') }}</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <span class="w-2 h-2 rounded-sm bg-[#10b981] shrink-0"></span>
                        <span>{{ __('Применим все скидки магазина, если они есть') }}</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <span class="w-2 h-2 rounded-sm bg-[#10b981] shrink-0"></span>
                        <span>{{ __('Оформляем заказы в мессенджерах') }}</span>
                    </li>
                </ul>

                <!-- Кнопки мессенджеров -->
                <x-messenger-row />

            </div>

            <div class="hidden lg:block lg:col-span-5 pointer-events-none"></div>

        </div>
    </div>

    <div class="hero-mobile-bg relative mt-1"
         style="background-image: url('{{ asset('images/hero-bg-apple-vert.jpg') }}');">
        <div class="absolute top-0 inset-x-0 h-16 bg-gradient-to-b from-[#f2f2f2] via-[#f2f2f2]/60 to-transparent pointer-events-none"></div>
    </div>

</section>