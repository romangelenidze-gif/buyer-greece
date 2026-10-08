<x-layouts.public 
    title="Центр помощи и FAQ | Buyer Greece"
    description="Ответы на часто задаваемые вопросы о выкупе, оплате, форвардинге и доставке из Греции в Грузию."
>
    <div class="py-12 bg-slate-50 min-h-[calc(100vh-4rem)]" x-data="{ activeCategory: 'all', openFaq: null }">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
            
            <!-- HEADER -->
            <div class="text-center max-w-2xl mx-auto space-y-3">
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Центр помощи</h1>
                <p class="text-sm text-slate-600">
                    Найдите ответы на популярные вопросы или обратитесь в нашу службу поддержки.
                </p>
            </div>

            <!-- CATEGORY FILTER BUTTONS -->
            <div class="flex flex-wrap gap-2 justify-center border-b border-slate-200 pb-4">
                <button 
                    @click="activeCategory = 'all'" 
                    :class="activeCategory === 'all' ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                    class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition-colors"
                >
                    Все вопросы
                </button>
                <button 
                    @click="activeCategory = 'buying'" 
                    :class="activeCategory === 'buying' ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                    class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition-colors"
                >
                    Выкуп
                </button>
                <button 
                    @click="activeCategory = 'payment'" 
                    :class="activeCategory === 'payment' ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                    class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition-colors"
                >
                    Оплата
                </button>
                <button 
                    @click="activeCategory = 'forwarding'" 
                    :class="activeCategory === 'forwarding' ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                    class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition-colors"
                >
                    Форвардинг
                </button>
                <button 
                    @click="activeCategory = 'camex'" 
                    :class="activeCategory === 'camex' ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                    class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition-colors"
                >
                    Camex и Доставка
                </button>
            </div>

            <!-- FAQ ACCORDION SECTION -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-sm space-y-4">
                <h2 class="text-xl font-bold text-slate-900 mb-4">Часто задаваемые вопросы</h2>

                <!-- Item 1: Buying -->
                <div x-show="activeCategory === 'all' || activeCategory === 'buying'" class="border-b border-slate-100 pb-3">
                    <button @click="openFaq = (openFaq === 1 ? null : 1)" class="w-full text-left font-semibold text-slate-800 text-sm flex justify-between items-center py-2 hover:text-brand-600">
                        <span>Как происходит выкуп товара по моей ссылке?</span>
                        <span class="text-slate-400" x-text="openFaq === 1 ? '−' : '+'"></span>
                    </button>
                    <div x-show="openFaq === 1" x-cloak class="text-xs text-slate-600 pt-2 leading-relaxed">
                        Вы отправляете ссылку через форму «Выкупите за меня». Менеджер проверяет наличие товара в магазине, рассчитывает стоимость доставки по Греции и нашу комиссию, после чего формирует Quote (расчет). Вы оплачиваете банковским переводом, и мы совершаем покупку.
                    </div>
                </div>

                <!-- Item 2: Payment -->
                <div x-show="activeCategory === 'all' || activeCategory === 'payment'" class="border-b border-slate-100 pb-3">
                    <button @click="openFaq = (openFaq === 2 ? null : 2)" class="w-full text-left font-semibold text-slate-800 text-sm flex justify-between items-center py-2 hover:text-brand-600">
                        <span>Как оплатить расчет (Quote)?</span>
                        <span class="text-slate-400" x-text="openFaq === 2 ? '−' : '+'"></span>
                    </button>
                    <div x-show="openFaq === 2" x-cloak class="text-xs text-slate-600 pt-2 leading-relaxed">
                        Оплата производится банковским переводом по реквизитам, указанным на странице оплаты. После перевода вы загружаете фото или PDF квитанции (чека). Менеджер подтверждает платеж, и заказ переходит в статус выкупа.
                    </div>
                </div>

                <!-- Item 3: Camex -->
                <div x-show="activeCategory === 'all' || activeCategory === 'camex'" class="border-b border-slate-100 pb-3">
                    <button @click="openFaq = (openFaq === 3 ? null : 3)" class="w-full text-left font-semibold text-slate-800 text-sm flex justify-between items-center py-2 hover:text-brand-600">
                        <span>Входит ли международная доставка в Грузию в ваш расчет?</span>
                        <span class="text-slate-400" x-text="openFaq === 3 ? '−' : '+'"></span>
                    </button>
                    <div x-show="openFaq === 3" x-cloak class="text-xs text-slate-600 pt-2 leading-relaxed">
                        Нет. Международная транспортировка из Греции в Грузию осуществляется службой Camex. Доставка оплачивается вами отдельно компании Camex при получении посылки согласно их тарифам.
                    </div>
                </div>

                <!-- Item 4: Forwarding -->
                <div x-show="activeCategory === 'all' || activeCategory === 'forwarding'" class="border-b border-slate-100 pb-3">
                    <button @click="openFaq = (openFaq === 4 ? null : 4)" class="w-full text-left font-semibold text-slate-800 text-sm flex justify-between items-center py-2 hover:text-brand-600">
                        <span>Зачем нужен Camex personal number в профиле?</span>
                        <span class="text-slate-400" x-text="openFaq === 4 ? '−' : '+'"></span>
                    </button>
                    <div x-show="openFaq === 4" x-cloak class="text-xs text-slate-600 pt-2 leading-relaxed">
                        Персональный номер Camex необходим для идентификации вашего груза на складе и правильного оформления документов перед передачей перевозчику. Без него адрес приема в Греции не активируется.
                    </div>
                </div>
            </div>

            <!-- KNOWLEDGE BASE CARDS -->
            <div class="space-y-4">
                <h2 class="text-xl font-bold text-slate-900">База знаний</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <a href="#" class="p-4 bg-white border border-slate-200 rounded-xl hover:border-brand-500 transition-colors block">
                        <div class="text-xs font-semibold text-brand-600 mb-1">Инструкция</div>
                        <h3 class="text-sm font-bold text-slate-800">Как правильно заказать в греческом магазине</h3>
                        <p class="text-xs text-slate-500 mt-1">Особенности заполнения адреса и выбора способа оплаты.</p>
                    </a>

                    <a href="#" class="p-4 bg-white border border-slate-200 rounded-xl hover:border-brand-500 transition-colors block">
                        <div class="text-xs font-semibold text-brand-600 mb-1">Оплата</div>
                        <h3 class="text-sm font-bold text-slate-800">Инструкция по загрузке чека об оплате</h3>
                        <p class="text-xs text-slate-500 mt-1">Требования к форматам и размерам файлов реквизитов.</p>
                    </a>
                </div>
            </div>

            <!-- STILL NEED HELP CTA -->
            <div class="bg-slate-900 text-white rounded-2xl p-6 text-center space-y-4">
                <h3 class="text-lg font-bold">Остались вопросы?</h3>
                <p class="text-xs text-slate-400 max-w-md mx-auto">
                    Свяжитесь с нашим менеджером в мессенджерах для оперативной консультации.
                </p>
                <div class="flex justify-center gap-4 pt-2">
                    <a href="https://t.me/your_support_bot" target="_blank" class="px-4 py-2 bg-brand-600 hover:bg-brand-500 text-white font-semibold text-xs rounded-lg transition-colors">
                        Telegram Support
                    </a>
                    <a href="https://wa.me/your_number" target="_blank" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs rounded-lg transition-colors">
                        WhatsApp Support
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-layouts.public>