<section class="apple-hero-section">
    
    <!-- Десктопный фон -->
    <div class="hidden lg:block absolute inset-0 hero-desktop-bg"
         style="background-image: url('{{ asset('images/hero-bg-apple.jpg') }}');">
        <div class="absolute inset-0 bg-gradient-to-r from-[#f2f2f2] via-[#f2f2f2]/30 via-50% to-transparent pointer-events-none"></div>
    </div>

    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-8 relative z-10 w-full">
        <div class="grid lg:grid-cols-12 gap-8 items-center">
            
            <div class="lg:col-span-7 text-center lg:text-left space-y-6">
                
                <!-- Главный заголовок -->
                <h1 class="apple-hero-title">
                    {{ __('Покупка и доставка любых товаров') }} 
                    <span class="text-[#0071e3]">{{ __('из магазинов Греции') }}</span>
                </h1>

                <!-- Подзаголовок (Normal уровень, 16px - 20px) -->
                <p class="apple-hero-subtitle max-w-2xl">
                    {{ __('Находите нужные товары, мы поможем купить их в греческих магазинах и доставим в Грузию через надёжного партнёра Camex.') }}
                </p>

               <!-- Форма отправки ссылки на заказ -->
                <div class="max-w-xl mx-auto lg:mx-0">
                    <form action="{{ route('buy-for-me') }}" method="GET" class="bg-white py-4 px-3.5 sm:p-2 rounded-full shadow-[0_10px_25px_rgba(0,0,0,0.05)] border border-black/[0.08] flex items-center gap-2 relative">
                        <div class="relative flex-1 flex items-center pl-1 sm:pl-4">
                            <span class="text-[#86868b] mr-2.5 shrink-0">
                                <svg class="w-5 h-5 text-[#0071e3]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                </svg>
                            </span>
                            <input 
                                type="url" 
                                name="url" 
                                placeholder="{{ __('Вставьте ссылку на товар...') }}" 
                                required
                                class="w-full bg-transparent border-0 text-sm sm:text-base font-normal text-[#1d1d1f] placeholder-[#86868b] focus:outline-none focus:ring-0 p-0"
                            >
                        </div>
                        <button type="submit" class="hidden sm:inline-block px-6 py-2.5 bg-[#0071e3] hover:bg-[#0077ed] active:scale-[0.98] text-white text-xs sm:text-sm font-medium rounded-full shadow-xs transition-all cursor-pointer whitespace-nowrap shrink-0">
                            {{ __('Сделать заказ') }}
                        </button>
                    </form>
                </div>

                <!-- ПЕРЕЧЕНЬ УСЛУГ И ПРЕИМУЩЕСТВ -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 max-w-xl mx-auto lg:mx-0 pt-2 text-left">
                    
                    <!-- Услуга 1 -->
                    <div class="apple-feature-item">
                        <div class="apple-feature-icon">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <span class="apple-feature-text">
                            {{ __('Проверенные магазины Греции') }}
                        </span>
                    </div>

                    <!-- Услуга 2 -->
                    <div class="apple-feature-item">
                        <div class="apple-feature-icon">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <span class="apple-feature-text">
                            {{ __('Выгодные цены и условия') }}
                        </span>
                    </div>

                    <!-- Услуга 3 -->
                    <div class="apple-feature-item">
                        <div class="apple-feature-icon">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <span class="apple-feature-text">
                            {{ __('Надёжная доставка через Camex') }}
                        </span>
                    </div>

                    <!-- Услуга 4 -->
                    <div class="apple-feature-item">
                        <div class="apple-feature-icon">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <span class="apple-feature-text">
                            {{ __('Поддержка на каждом этапе') }}
                        </span>
                    </div>

                </div>

                <!-- Блок связи с мессенджерами (Minimum уровень, 14px - 16px) -->
                <div class="space-y-3 pt-2">
                    <p class="apple-hero-note text-sm sm:text-base">
                        {{ __('Мы на связи и готовы помочь с любым вопросом по выкупу и доставке') }}
                    </p>
                    <x-messenger-row />
                </div>

            </div>

            <div class="hidden lg:block lg:col-span-5 pointer-events-none"></div>

        </div>
    </div>

</section>