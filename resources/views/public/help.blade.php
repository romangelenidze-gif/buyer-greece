<x-layouts.public 
    title="{{ __('Центр помощи и FAQ | Buyer Greece') }}"
    description="{{ __('Ответы на часто задаваемые вопросы о выкупе, оплате, форвардинге и доставке из Греции в Грузию.') }}"
>
    <div class="py-6 bg-[#f2f2f2] min-h-[calc(100vh-4rem)]" x-data="{ openFaq: null }">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
            
            <!-- HEADER -->
            <div class="text-center max-w-3xl mx-auto space-y-4">
                <h1 class="apple-section-title text-left sm:text-center">
                    {{ __('Центр помощи Buyer Greece') }}
                </h1>
                <p class="apple-hero-subtitle text-left sm:text-center mx-auto">
                    {{ __('Здесь собраны ответы на вопросы о покупках в греческих магазинах, оформлении заказов, оплате, доставке в Грузию и получении посылок.') }}
                </p>
            </div>

            <!-- KNOWLEDGE BASE ACCORDION SECTION -->
            <div class="apple-card space-y-4">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-base font-semibold text-[#1d1d1f] tracking-tight">{{ __('База знаний и статьи') }}</h2>
                    <span class="text-xs font-normal text-[#86868b]">8 найдено</span>
                </div>

                <!-- Item 1 -->
                <div class="border-b border-black/[0.06] pb-4">
                    <button 
                        type="button"
                        @click="openFaq = (openFaq === 1 ? null : 1)" 
                        class="w-full text-left font-semibold text-[#1d1d1f] text-sm sm:text-base flex justify-between items-center py-2 hover:text-[#0071e3] transition-colors cursor-pointer"
                    >
                        <span>{{ __('Что такое Buyer Greece и как работает сервис?') }}</span>
                        <span class="w-7 h-7 rounded-full bg-black/[0.06] flex items-center justify-center text-sm font-bold text-[#1d1d1f] transition-transform duration-300 flex-shrink-0 ml-4" :class="openFaq === 1 ? 'bg-[#0071e3] text-black' : ''" x-text="openFaq === 1 ? '-' : '+'"></span>
                    </button>
                    <div x-show="openFaq === 1" x-cloak class="text-xs sm:text-sm text-[#86868b] pt-3 leading-relaxed transition-all duration-300">
                        {{ __('Buyer Greece помогает клиентам в Грузии приобретать товары в греческих магазинах и организовывать их дальнейшую отправку в Грузию через Camex. В зависимости от ситуации вы можете попросить нашу команду выкупить товар за вас (Buy For Me), оформить заявку или самостоятельно приобрести товар в греческом магазине, воспользовавшись нашим складом в Греции.') }}
                    </div>
                </div>

                <!-- Item 2 -->
                <div class="border-b border-black/[0.06] pb-4">
                    <button 
                        type="button"
                        @click="openFaq = (openFaq === 2 ? null : 2)" 
                        class="w-full text-left font-semibold text-[#1d1d1f] text-sm sm:text-base flex justify-between items-center py-2 hover:text-[#0071e3] transition-colors cursor-pointer"
                    >
                        <span>{{ __('Как оформить заявку на выкуп товара (Buy For Me)?') }}</span>
                        <span class="w-7 h-7 rounded-full bg-black/[0.06] flex items-center justify-center text-sm font-bold text-[#1d1d1f] transition-transform duration-300 flex-shrink-0 ml-4" :class="openFaq === 2 ? 'bg-[#0071e3] text-black' : ''" x-text="openFaq === 2 ? '-' : '+'"></span>
                    </button>
                    <div x-show="openFaq === 2" x-cloak class="text-xs sm:text-sm text-[#86868b] pt-3 leading-relaxed transition-all duration-300">
                        {!! __('1. Найдите товар в греческом интернет-магазине или воспользуйтесь умным поиском Buyer Greece.<br>2. Скопируйте ссылку на страницу нужного продукта.<br>3. Перейдите к форме Buy For Me, вставьте ссылку и укажите размер, цвет, модель или количество.<br>4. Отправьте заявку и дождитесь проверки информации менеджером.') !!}
                    </div>
                </div>

                <!-- Item 3 -->
                <div class="border-b border-black/[0.06] pb-4">
                    <button 
                        type="button"
                        @click="openFaq = (openFaq === 3 ? null : 3)" 
                        class="w-full text-left font-semibold text-[#1d1d1f] text-sm sm:text-base flex justify-between items-center py-2 hover:text-[#0071e3] transition-colors cursor-pointer"
                    >
                        <span>{{ __('Как оплатить заказ и подтвердить перевод?') }}</span>
                        <span class="w-7 h-7 rounded-full bg-black/[0.06] flex items-center justify-center text-sm font-bold text-[#1d1d1f] transition-transform duration-300 flex-shrink-0 ml-4" :class="openFaq === 3 ? 'bg-[#0071e3] text-black' : ''" x-text="openFaq === 3 ? '-' : '+'"></span>
                    </button>
                    <div x-show="openFaq === 3" x-cloak class="text-xs sm:text-sm text-[#86868b] pt-3 leading-relaxed transition-all duration-300">
                        {{ __('В текущей модели Buyer Greece основным способом оплаты является банковский перевод. Перевод следует выполнять после получения подтверждённой суммы и реквизитов. После отправки перевода загрузите квитанцию в систему для подтверждения платежа.') }}
                    </div>
                </div>

                <!-- Item 4 -->
                <div class="border-b border-black/[0.06] pb-4">
                    <button 
                        type="button"
                        @click="openFaq = (openFaq === 4 ? null : 4)" 
                        class="w-full text-left font-semibold text-[#1d1d1f] text-sm sm:text-base flex justify-between items-center py-2 hover:text-[#0071e3] transition-colors cursor-pointer"
                    >
                        <span>{{ __('Как правильно использовать адрес в Греции при самостоятельной покупке?') }}</span>
                        <span class="w-7 h-7 rounded-full bg-black/[0.06] flex items-center justify-center text-sm font-bold text-[#1d1d1f] transition-transform duration-300 flex-shrink-0 ml-4" :class="openFaq === 4 ? 'bg-[#0071e3] text-black' : ''" x-text="openFaq === 4 ? '-' : '+'"></span>
                    </button>
                    <div x-show="openFaq === 4" x-cloak class="text-xs sm:text-sm text-[#86868b] pt-3 leading-relaxed transition-all duration-300">
                        {{ __('Перед оплатой товара в магазине убедитесь, что получили актуальные инструкции Buyer Greece. При оформлении внимательно проверьте адрес, имя получателя, индекс и ваш персональный идентификатор Camex для точной идентификации посылки на складе.') }}
                    </div>
                </div>

                <!-- Item 5 -->
                <div class="border-b border-black/[0.06] pb-4">
                    <button 
                        type="button"
                        @click="openFaq = (openFaq === 5 ? null : 5)" 
                        class="w-full text-left font-semibold text-[#1d1d1f] text-sm sm:text-base flex justify-between items-center py-2 hover:text-[#0071e3] transition-colors cursor-pointer"
                    >
                        <span>{{ __('Как работает доставка через Camex и когда появляется трек-номер?') }}</span>
                        <span class="w-7 h-7 rounded-full bg-black/[0.06] flex items-center justify-center text-sm font-bold text-[#1d1d1f] transition-transform duration-300 flex-shrink-0 ml-4" :class="openFaq === 5 ? 'bg-[#0071e3] text-black' : ''" x-text="openFaq === 5 ? '-' : '+'"></span>
                    </button>
                    <div x-show="openFaq === 5" x-cloak class="text-xs sm:text-sm text-[#86868b] pt-3 leading-relaxed transition-all duration-300">
                        {{ __('После поступления и обработки товара на нашем складе в Греции он передается службе Camex для международной доставки в Грузию. После оформления передачи в системе появляется международный трек-номер, по которому можно отслеживать посылку на официальном сайте перевозчика.') }}
                    </div>
                </div>

                <!-- Item 6 -->
                <div class="border-b border-black/[0.06] pb-4">
                    <button 
                        type="button"
                        @click="openFaq = (openFaq === 6 ? null : 6)" 
                        class="w-full text-left font-semibold text-[#1d1d1f] text-sm sm:text-base flex justify-between items-center py-2 hover:text-[#0071e3] transition-colors cursor-pointer"
                    >
                        <span>{{ __('Как работает умный поиск товаров и перевод запросов?') }}</span>
                        <span class="w-7 h-7 rounded-full bg-black/[0.06] flex items-center justify-center text-sm font-bold text-[#1d1d1f] transition-transform duration-300 flex-shrink-0 ml-4" :class="openFaq === 6 ? 'bg-[#0071e3] text-black' : ''" x-text="openFaq === 6 ? '-' : '+'"></span>
                    </button>
                    <div x-show="openFaq === 6" x-cloak class="text-xs sm:text-sm text-[#86868b] pt-3 leading-relaxed transition-all duration-300">
                        {{ __('Наш умный поиск на базе ИИ принимает запросы на естественных языках (включая русский и грузинский), переводит их и адаптирует для каталога BestPrice.gr, после чего возвращает актуальные предложения с переводом названий обратно.') }}
                    </div>
                </div>

                <!-- Item 7 -->
                <div class="border-b border-black/[0.06] pb-4">
                    <button 
                        type="button"
                        @click="openFaq = (openFaq === 7 ? null : 7)" 
                        class="w-full text-left font-semibold text-[#1d1d1f] text-sm sm:text-base flex justify-between items-center py-2 hover:text-[#0071e3] transition-colors cursor-pointer"
                    >
                        <span>{{ __('Можно ли отменить заказ или вернуть товар?') }}</span>
                        <span class="w-7 h-7 rounded-full bg-black/[0.06] flex items-center justify-center text-sm font-bold text-[#1d1d1f] transition-transform duration-300 flex-shrink-0 ml-4" :class="openFaq === 7 ? 'bg-[#0071e3] text-black' : ''" x-text="openFaq === 7 ? '-' : '+'"></span>
                    </button>
                    <div x-show="openFaq === 7" x-cloak class="text-xs sm:text-sm text-[#86868b] pt-3 leading-relaxed transition-all duration-300">
                        {{ __('Если заявка еще не оплачена и покупка не началась, обратитесь в поддержку для отмены. Если товар уже куплен или доставлен, возврат регулируется правилами продавца и может потребовать покрытия дополнительных расходов на логистику.') }}
                    </div>
                </div>

                <!-- Item 8 -->
                <div class="pb-2">
                    <button 
                        type="button"
                        @click="openFaq = (openFaq === 8 ? null : 8)" 
                        class="w-full text-left font-semibold text-[#1d1d1f] text-sm sm:text-base flex justify-between items-center py-2 hover:text-[#0071e3] transition-colors cursor-pointer"
                    >
                        <span>{{ __('Где посмотреть статусы заказа, посылки и отправки?') }}</span>
                        <span class="w-7 h-7 rounded-full bg-black/[0.06] flex items-center justify-center text-sm font-bold text-[#1d1d1f] transition-transform duration-300 flex-shrink-0 ml-4" :class="openFaq === 8 ? 'bg-[#0071e3] text-white' : ''" x-text="openFaq === 8 ? '-' : '+'"></span>
                    </button>
                    <div x-show="openFaq === 8" x-cloak class="text-xs sm:text-sm text-[#86868b] pt-3 leading-relaxed transition-all duration-300">
                        {{ __('В вашем личном кабинете вы можете отдельно отслеживать процесс выкупа товара (заказ), физическое поступление на склад (входящая посылка) и международную транспортировку (отправка Camex).') }}
                    </div>
                </div>
            </div>

            <!-- STILL NEED HELP CTA -->
            <div class="bg-[#1d1d1f] text-white rounded-4xl p-8 sm:p-10 text-center space-y-4 shadow-xl">
                <h3 class="text-xl sm:text-2xl font-semibold tracking-tight">{{ __('Остались вопросы или нужна помощь?') }}</h3>
                <p class="text-xs sm:text-sm text-[#86868b] max-w-md mx-auto leading-relaxed">
                    {{ __('Свяжитесь с нашей командой в удобном мессенджере. Мы на связи и готовы помочь с любым вопросом по выкупу и доставке.') }}
                </p>
                <x-messenger-row align="justify-center" />
            </div>

        </div>
    </div>
</x-layouts.public>