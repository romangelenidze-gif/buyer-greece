<x-layouts.public 
    title="Buyer Greece → Georgia | Выкуп и доставка товаров из Греции"
    description="Сервис выкупа и приема покупок в интернет-магазинах Греции с последующей доставкой в Грузию через Camex."
>

    <!-- BLOCK 1: HERO -->
    <section class="py-16 md:py-24 bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center md:text-left">
            <div class="grid md:grid-cols-12 gap-8 items-center">
                <div class="md:col-span-7 space-y-6">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-brand-50 text-brand-700 border border-brand-100">
                        Греция → Грузия
                    </span>
                    <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight">
                        Доставка и выкуп любых товаров из магазинов Греции
                    </h1>
                    <p class="text-base sm:text-lg text-slate-600 leading-relaxed">
                        Мы помогаем выкупать товары или предоставляем греческий адрес для самостоятельных покупок. Международную перевозку в Грузию выполняет транспортный партнер <strong class="text-slate-800">Camex</strong>.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-3 pt-2 justify-center md:justify-start">
                        <x-buttons.primary href="{{ route('buy-for-me') }}" size="large">
                            Выкупите за меня
                        </x-buttons.primary>
                        <a href="{{ route('forwarding') }}" class="inline-flex items-center justify-center px-6 py-3 text-base font-semibold rounded-lg border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 transition-all">
                            Я покупаю сам (Форвардинг)
                        </a>
                    </div>

                    <div class="pt-2 text-xs text-slate-500 flex items-center justify-center md:justify-start gap-2">
                        <span>Нужна помощь с выбором?</span>
                        <a href="https://t.me/your_support_bot" target="_blank" class="text-brand-600 font-medium hover:underline">Написать в Telegram</a>
                        <span>•</span>
                        <a href="https://wa.me/your_number" target="_blank" class="text-brand-600 font-medium hover:underline">WhatsApp</a>
                    </div>
                </div>

                <div class="md:col-span-5">
                    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
                        <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Быстрый старт</div>
                        <h2 class="text-lg font-bold text-slate-900">Есть ссылка на товар из Греции?</h2>
                        <p class="text-sm text-slate-600">Вставьте URL в форму, и мы рассчитаем итоговую стоимость перед оплатой.</p>
                        <a href="{{ route('buy-for-me') }}" class="block w-full text-center py-2.5 px-4 bg-slate-900 hover:bg-slate-800 text-white font-medium text-sm rounded-lg transition-colors">
                            Отправить ссылку на товар →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- BLOCK 2: THREE SCENARIOS -->
    <section class="py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <h2 class="text-2xl sm:text-3xl font-bold text-slate-900">3 сценария работы</h2>
                <p class="text-sm sm:text-base text-slate-600 mt-2">Выберите формат, который удобен именно вам</p>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                <!-- Scenario 1 -->
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="w-10 h-10 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center font-bold mb-4">1</div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">Выкупите за меня</h3>
                        <p class="text-sm text-slate-600 leading-relaxed mb-6">
                            У вас есть ссылка на товар. Мы проверяем его наличие, составляем точный расчет (Quote) и выкупаем для вас.
                        </p>
                    </div>
                    <a href="{{ route('buy-for-me') }}" class="inline-flex items-center text-sm font-semibold text-brand-600 hover:text-brand-700">
                        Отправить ссылку →
                    </a>
                </div>

                <!-- Scenario 2 -->
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="w-10 h-10 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center font-bold mb-4">2</div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">Помогите купить</h3>
                        <p class="text-sm text-slate-600 leading-relaxed mb-6">
                            Не знаете, где найти нужный товар или необходим подбор размера? Менеджер поможет оформить запрос в чате.
                        </p>
                    </div>
                    <div class="flex gap-4">
                        <a href="https://t.me/your_support_bot" target="_blank" class="text-sm font-semibold text-brand-600 hover:text-brand-700">Telegram</a>
                        <span class="text-slate-300">|</span>
                        <a href="https://wa.me/your_number" target="_blank" class="text-sm font-semibold text-brand-600 hover:text-brand-700">WhatsApp</a>
                    </div>
                </div>

                <!-- Scenario 3 -->
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="w-10 h-10 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center font-bold mb-4">3</div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">Форвардинг</h3>
                        <p class="text-sm text-slate-600 leading-relaxed mb-6">
                            Вы покупаете сами в греческом магазине и отправляете посылку на наш адрес приема в Греции.
                        </p>
                    </div>
                    <a href="{{ route('forwarding') }}" class="inline-flex items-center text-sm font-semibold text-brand-600 hover:text-brand-700">
                        Получить адрес →
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- BLOCK 3: HOW IT WORKS PREVIEW -->
    <section class="py-16 bg-white border-y border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-10">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-slate-900">Простой процесс покупки</h2>
                    <p class="text-slate-600 text-sm mt-1">От заявки до передачи в международную доставку</p>
                </div>
                <a href="{{ route('how-it-works') }}" class="mt-4 md:mt-0 text-sm font-semibold text-brand-600 hover:text-brand-700">
                    Подробное описание процесса →
                </a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-6 gap-4 text-center">
                <div class="p-4 bg-slate-50 rounded-lg border border-slate-100">
                    <div class="text-xs font-bold text-slate-400 mb-1">Шаг 1</div>
                    <div class="text-xs font-semibold text-slate-800">Ссылка</div>
                </div>
                <div class="p-4 bg-slate-50 rounded-lg border border-slate-100">
                    <div class="text-xs font-bold text-slate-400 mb-1">Шаг 2</div>
                    <div class="text-xs font-semibold text-slate-800">Расчет (Quote)</div>
                </div>
                <div class="p-4 bg-slate-50 rounded-lg border border-slate-100">
                    <div class="text-xs font-bold text-slate-400 mb-1">Шаг 3</div>
                    <div class="text-xs font-semibold text-slate-800">Оплата</div>
                </div>
                <div class="p-4 bg-slate-50 rounded-lg border border-slate-100">
                    <div class="text-xs font-bold text-slate-400 mb-1">Шаг 4</div>
                    <div class="text-xs font-semibold text-slate-800">Выкуп в Греции</div>
                </div>
                <div class="p-4 bg-slate-50 rounded-lg border border-slate-100">
                    <div class="text-xs font-bold text-slate-400 mb-1">Шаг 5</div>
                    <div class="text-xs font-semibold text-slate-800">Прием и подготовка</div>
                </div>
                <div class="p-4 bg-brand-50 rounded-lg border border-brand-100">
                    <div class="text-xs font-bold text-brand-600 mb-1">Шаг 6</div>
                    <div class="text-xs font-semibold text-brand-900">Передача Camex</div>
                </div>
            </div>
        </div>
    </section>

    <!-- BLOCK 5: PRICE CLARITY EXAMPLE -->
    <section class="py-16 bg-slate-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-sm">
                <h2 class="text-xl font-bold text-slate-900 mb-2">Прозрачность расходов</h2>
                <p class="text-sm text-slate-600 mb-6">Пример расчета стоимости выкупа товара из магазина:</p>

                <div class="space-y-3 font-mono text-sm border-b border-slate-200 pb-4">
                    <div class="flex justify-between">
                        <span class="text-slate-600">Стоимость товара в магазине:</span>
                        <span class="font-bold text-slate-800">€100.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Доставка по Греции (магазин → склад):</span>
                        <span class="font-bold text-slate-800">€5.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Наша комиссия за выкуп:</span>
                        <span class="font-bold text-slate-800">€10.00</span>
                    </div>
                </div>

                <div class="pt-4 flex justify-between items-center">
                    <span class="font-bold text-slate-900 text-base">Итого к оплате нам:</span>
                    <span class="font-extrabold text-xl text-brand-600">€115.00</span>
                </div>

                <div class="mt-6 p-4 bg-amber-50 rounded-lg border border-amber-200 text-xs text-amber-800 leading-relaxed">
                    <strong>Обратите внимание:</strong> Международная доставка Греция → Грузия рассчитывается по тарифам Camex и оплачивается клиентом напрямую компании Camex при получении.
                </div>
            </div>
        </div>
    </section>

    <!-- BLOCK 6 & 7: FINAL CTA -->
    <section class="py-16 bg-slate-900 text-white text-center">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <h2 class="text-2xl sm:text-3xl font-extrabold">Готовы сделать заказ?</h2>
            <p class="text-slate-400 text-sm sm:text-base">
                Отправьте нам ссылку на товар из любого магазина Греции, и мы оперативно подготовим расчет.
            </p>
            <div class="pt-2">
                <x-buttons.primary href="{{ route('buy-for-me') }}" size="large">
                    Отправить ссылку на товар
                </x-buttons.primary>
            </div>
        </div>
    </section>

</x-layouts.public>