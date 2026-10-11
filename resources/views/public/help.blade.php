<x-layouts.public 
    title="{{ __('Центр помощи и FAQ | Buyer Greece') }}"
    description="{{ __('Ответы на часто задаваемые вопросы о выкупе, оплате, форвардинге и доставке из Греции в Грузию.') }}"
>
    <div class="py-16 bg-[#f2f2f2] min-h-[calc(100vh-4rem)]" x-data="{ activeCategory: 'all', openFaq: null }">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
            
            <!-- HEADER -->
            <div class="text-center max-w-2xl mx-auto space-y-3">
                <h1 class="apple-section-title">{{ __('Центр помощи') }}</h1>
                <p class="apple-section-subtitle">
                    {{ __('Найдите ответы на популярные вопросы или обратитесь в нашу службу поддержки.') }}
                </p>
            </div>

            <!-- CATEGORY FILTER BUTTONS (Apple Segmented Control) -->
            <div class="flex flex-wrap gap-1.5 justify-center bg-black/[0.04] p-1.5 rounded-full max-w-2xl mx-auto border border-black/[0.04]">
                <button 
                    type="button"
                    @click="activeCategory = 'all'" 
                    :class="activeCategory === 'all' ? 'bg-white text-[#1d1d1f] shadow-[0_2px_8px_rgba(0,0,0,0.08)] font-medium' : 'text-[#86868b] hover:text-[#1d1d1f]'"
                    class="px-4 py-2 rounded-full text-xs transition-all duration-200 cursor-pointer"
                >
                    {{ __('Все вопросы') }}
                </button>
                <button 
                    type="button"
                    @click="activeCategory = 'buying'" 
                    :class="activeCategory === 'buying' ? 'bg-white text-[#1d1d1f] shadow-[0_2px_8px_rgba(0,0,0,0.08)] font-medium' : 'text-[#86868b] hover:text-[#1d1d1f]'"
                    class="px-4 py-2 rounded-full text-xs transition-all duration-200 cursor-pointer"
                >
                    {{ __('Выкуп') }}
                </button>
                <button 
                    type="button"
                    @click="activeCategory = 'payment'" 
                    :class="activeCategory === 'payment' ? 'bg-white text-[#1d1d1f] shadow-[0_2px_8px_rgba(0,0,0,0.08)] font-medium' : 'text-[#86868b] hover:text-[#1d1d1f]'"
                    class="px-4 py-2 rounded-full text-xs transition-all duration-200 cursor-pointer"
                >
                    {{ __('Оплата') }}
                </button>
                <button 
                    type="button"
                    @click="activeCategory = 'forwarding'" 
                    :class="activeCategory === 'forwarding' ? 'bg-white text-[#1d1d1f] shadow-[0_2px_8px_rgba(0,0,0,0.08)] font-medium' : 'text-[#86868b] hover:text-[#1d1d1f]'"
                    class="px-4 py-2 rounded-full text-xs transition-all duration-200 cursor-pointer"
                >
                    {{ __('Форвардинг') }}
                </button>
                <button 
                    type="button"
                    @click="activeCategory = 'camex'" 
                    :class="activeCategory === 'camex' ? 'bg-white text-[#1d1d1f] shadow-[0_2px_8px_rgba(0,0,0,0.08)] font-medium' : 'text-[#86868b] hover:text-[#1d1d1f]'"
                    class="px-4 py-2 rounded-full text-xs transition-all duration-200 cursor-pointer"
                >
                    {{ __('Camex и Доставка') }}
                </button>
            </div>

            <!-- FAQ ACCORDION SECTION (Apple Style with Smooth Animations) -->
            <div class="apple-card space-y-6">
                <h2 class="text-xl font-semibold text-[#1d1d1f] tracking-tight mb-2">{{ __('Часто задаваемые вопросы') }}</h2>

                <!-- Item 1: Buying -->
                <div x-show="activeCategory === 'all' || activeCategory === 'buying'" class="border-b border-black/[0.06] pb-4">
                    <button 
                        type="button"
                        @click="openFaq = (openFaq === 1 ? null : 1)" 
                        class="w-full text-left font-semibold text-[#1d1d1f] text-sm sm:text-base flex justify-between items-center py-2 hover:text-[#0071e3] transition-colors cursor-pointer"
                    >
                        <span>{{ __('Как происходит выкуп товара по моей ссылке?') }}</span>
                        <span class="w-7 h-7 rounded-full bg-black/[0.04] flex items-center justify-center text-sm font-bold text-[#1d1d1f] transition-transform duration-300 flex-shrink-0 ml-4" :class="openFaq === 1 ? 'rotate-45 bg-[#0071e3] text-white' : ''">+</span>
                    </button>
                    <div x-show="openFaq === 1" x-cloak class="text-sm text-[#86868b] pt-3 leading-relaxed transition-all duration-300">
                        {{ __('Вы отправляете ссылку через форму «Выкупите за меня». Менеджер проверяет наличие товара в магазине, рассчитывает стоимость доставки по Греции и нашу комиссию, после чего формирует Quote (расчет). Вы оплачиваете банковским переводом, и мы совершаем покупку.') }}
                    </div>
                </div>

                <!-- Item 2: Payment -->
                <div x-show="activeCategory === 'all' || activeCategory === 'payment'" class="border-b border-black/[0.06] pb-4">
                    <button 
                        type="button"
                        @click="openFaq = (openFaq === 2 ? null : 2)" 
                        class="w-full text-left font-semibold text-[#1d1d1f] text-sm sm:text-base flex justify-between items-center py-2 hover:text-[#0071e3] transition-colors cursor-pointer"
                    >
                        <span>{{ __('Как оплатить расчет (Quote)?') }}</span>
                        <span class="w-7 h-7 rounded-full bg-black/[0.04] flex items-center justify-center text-sm font-bold text-[#1d1d1f] transition-transform duration-300 flex-shrink-0 ml-4" :class="openFaq === 2 ? 'rotate-45 bg-[#0071e3] text-white' : ''">+</span>
                    </button>
                    <div x-show="openFaq === 2" x-cloak class="text-sm text-[#86868b] pt-3 leading-relaxed transition-all duration-300">
                        {{ __('Оплата производится банковским переводом по реквизитам, указанным на странице оплаты. После перевода вы загружаете фото или PDF квитанции (чека). Менеджер подтверждает платеж, и заказ переходит в статус выкупа.') }}
                    </div>
                </div>

                <!-- Item 3: Camex -->
                <div x-show="activeCategory === 'all' || activeCategory === 'camex'" class="border-b border-black/[0.06] pb-4">
                    <button 
                        type="button"
                        @click="openFaq = (openFaq === 3 ? null : 3)" 
                        class="w-full text-left font-semibold text-[#1d1d1f] text-sm sm:text-base flex justify-between items-center py-2 hover:text-[#0071e3] transition-colors cursor-pointer"
                    >
                        <span>{{ __('Входит ли международная доставка в Грузию в ваш расчет?') }}</span>
                        <span class="w-7 h-7 rounded-full bg-black/[0.04] flex items-center justify-center text-sm font-bold text-[#1d1d1f] transition-transform duration-300 flex-shrink-0 ml-4" :class="openFaq === 3 ? 'rotate-45 bg-[#0071e3] text-white' : ''">+</span>
                    </button>
                    <div x-show="openFaq === 3" x-cloak class="text-sm text-[#86868b] pt-3 leading-relaxed transition-all duration-300">
                        {{ __('Нет. Международная транспортировка из Греции в Грузию осуществляется службой Camex. Доставка оплачивается вами отдельно компании Camex при получении посылки согласно их тарифам.') }}
                    </div>
                </div>

                <!-- Item 4: Forwarding -->
                <div x-show="activeCategory === 'all' || activeCategory === 'forwarding'" class="pb-2">
                    <button 
                        type="button"
                        @click="openFaq = (openFaq === 4 ? null : 4)" 
                        class="w-full text-left font-semibold text-[#1d1d1f] text-sm sm:text-base flex justify-between items-center py-2 hover:text-[#0071e3] transition-colors cursor-pointer"
                    >
                        <span>{{ __('Зачем нужен Camex personal number в профиле?') }}</span>
                        <span class="w-7 h-7 rounded-full bg-black/[0.04] flex items-center justify-center text-sm font-bold text-[#1d1d1f] transition-transform duration-300 flex-shrink-0 ml-4" :class="openFaq === 4 ? 'rotate-45 bg-[#0071e3] text-white' : ''">+</span>
                    </button>
                    <div x-show="openFaq === 4" x-cloak class="text-sm text-[#86868b] pt-3 leading-relaxed transition-all duration-300">
                        {{ __('Персональный номер Camex необходим для идентификации вашего груза на складе и правильного оформления документов перед передачей перевозчику. Без него адрес приема в Греции не активируется.') }}
                    </div>
                </div>
            </div>

            <!-- KNOWLEDGE BASE CARDS -->
            <div class="space-y-4">
                <h2 class="text-xl font-semibold text-[#1d1d1f] tracking-tight">{{ __('База знаний') }}</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <a href="#" class="apple-bento-card block">
                        <div class="text-xs font-semibold text-[#0071e3] mb-2 uppercase tracking-wide">{{ __('Инструкция') }}</div>
                        <h3 class="text-base font-semibold text-[#1d1d1f] tracking-tight">{{ __('Как правильно заказать в греческом магазине') }}</h3>
                        <p class="text-xs text-[#86868b] mt-2 leading-relaxed">{{ __('Особенности заполнения адреса и выбора способа оплаты.') }}</p>
                    </a>

                    <a href="#" class="apple-bento-card block">
                        <div class="text-xs font-semibold text-[#0071e3] mb-2 uppercase tracking-wide">{{ __('Оплата') }}</div>
                        <h3 class="text-base font-semibold text-[#1d1d1f] tracking-tight">{{ __('Инструкция по загрузке чека об оплате') }}</h3>
                        <p class="text-xs text-[#86868b] mt-2 leading-relaxed">{{ __('Требования к форматам и размерам файлов реквизитов.') }}</p>
                    </a>
                </div>
            </div>

            <!-- STILL NEED HELP CTA -->
            <div class="bg-[#1d1d1f] text-white rounded-[2.5rem] p-8 sm:p-10 text-center space-y-4 shadow-xl">
                <h3 class="text-2xl font-semibold tracking-tight">{{ __('Остались вопросы?') }}</h3>
                <p class="text-sm text-[#86868b] max-w-md mx-auto leading-relaxed">
                    {{ __('Свяжитесь с нашим менеджером в мессенджерах для оперативной консультации.') }}
                </p>
                <div class="flex justify-center gap-3 pt-3">
                    <x-messenger type="telegram" size="small" />
                    <x-messenger type="whatsapp" size="small" />
                </div>
            </div>

        </div>
    </div>
</x-layouts.public>