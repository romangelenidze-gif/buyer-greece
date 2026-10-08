<x-layouts.public 
    title="Форвардинг | Греческий адрес для покупок | Buyer Greece"
    description="Покупайте в интернет-магазинах Греции самостоятельно и используйте наш адрес приема для последующей доставки в Грузию через Camex."
>
    <div class="py-12 bg-slate-50 min-h-[calc(100vh-4rem)]">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            
            <!-- HERO -->
            <div class="text-center max-w-3xl mx-auto space-y-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-brand-50 text-brand-700 border border-brand-100">
                    Самостоятельные покупки
                </span>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Покупайте сами в магазинах Греции — мы примем и подготовим посылку
                </h1>
                <p class="text-base text-slate-600 leading-relaxed">
                    Используйте наш виртуальный адрес в Греции при оформлении заказов. После приема на складе мы подготавливаем посылку и передаем её компании Camex для международной доставки.
                </p>
            </div>

            <!-- 7 STEPS FLOW -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-sm">
                <h2 class="text-xl font-bold text-slate-900 mb-6">Как работает форвардинг (7 шагов)</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-full bg-slate-900 text-white font-bold text-sm flex items-center justify-center flex-shrink-0">1</div>
                        <div>
                            <h3 class="font-bold text-slate-800 text-sm">Зарегистрируйтесь на сайте</h3>
                            <p class="text-xs text-slate-600 mt-1">Создайте личный кабинет для доступа к настройкам и отслеживанию посылок.</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-full bg-slate-900 text-white font-bold text-sm flex items-center justify-center flex-shrink-0">2</div>
                        <div>
                            <h3 class="font-bold text-slate-800 text-sm">Укажите ваш персональный номер Camex</h3>
                            <p class="text-xs text-slate-600 mt-1">Внесите код Camex в профиле. Без этого адреса приемки не отображаются.</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-full bg-slate-900 text-white font-bold text-sm flex items-center justify-center flex-shrink-0">3</div>
                        <div>
                            <h3 class="font-bold text-slate-800 text-sm">Получите реквизиты адреса в Греции</h3>
                            <p class="text-xs text-slate-600 mt-1">Скопируйте адрес склада и ваш идентификатор клиента в кабинете.</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-full bg-slate-900 text-white font-bold text-sm flex items-center justify-center flex-shrink-0">4</div>
                        <div>
                            <h3 class="font-bold text-slate-800 text-sm">Оформите заказ в интернет-магазине</h3>
                            <p class="text-xs text-slate-600 mt-1">Укажите наш адрес доставки и обязательно добавьте ваш идентификатор.</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-full bg-slate-900 text-white font-bold text-sm flex items-center justify-center flex-shrink-0">5</div>
                        <div>
                            <h3 class="font-bold text-slate-800 text-sm">Добавьте ожидаемую посылку (трек-номер)</h3>
                            <p class="text-xs text-slate-600 mt-1">Внесите трек-номер магазина в кабинет, чтобы мы быстрее идентифицировали груз.</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-full bg-slate-900 text-white font-bold text-sm flex items-center justify-center flex-shrink-0">6</div>
                        <div>
                            <h3 class="font-bold text-slate-800 text-sm">Прием и обработка в Греции</h3>
                            <p class="text-xs text-slate-600 mt-1">Мы принимаем заказ, взвешиваем и подготавливаем документы.</p>
                        </div>
                    </div>

                    <div class="flex gap-4 md:col-span-2 bg-brand-50 p-4 rounded-xl border border-brand-100">
                        <div class="w-8 h-8 rounded-full bg-brand-600 text-white font-bold text-sm flex items-center justify-center flex-shrink-0">7</div>
                        <div>
                            <h3 class="font-bold text-brand-900 text-sm">Передача перевозчику Camex</h3>
                            <p class="text-xs text-brand-800 mt-1">После передачи посылки Camex международная транспортировка в Грузию и выдача отслеживаются на стороне Camex.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CAMEX NOTICE & CALL TO ACTION -->
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 sm:p-8 flex flex-col sm:flex-row items-center justify-between gap-6">
                <div class="space-y-2">
                    <h3 class="text-base font-bold text-amber-900">Нужен адрес приема в Греции?</h3>
                    <p class="text-xs sm:text-sm text-amber-800 leading-relaxed">
                        Адрес предоставляется бесплатно в Личном кабинете после добавления вашего персонального номера Camex.
                    </p>
                </div>
                @auth
                    <a href="{{ route('dashboard') }}" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-semibold text-sm rounded-lg flex-shrink-0 transition-colors">
                        Открыть мой адрес
                    </a>
                @else
                    <a href="{{ route('register') }}" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-semibold text-sm rounded-lg flex-shrink-0 transition-colors">
                        Зарегистрироваться
                    </a>
                @endauth
            </div>

            <!-- IMPORTANT RULES -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 space-y-4">
                <h3 class="text-base font-bold text-slate-900">Правила приема самостоятельных заказов</h3>
                <ul class="space-y-2 text-xs sm:text-sm text-slate-600 list-disc list-inside leading-relaxed">
                    <li>Всегда указывайте ваш уникальный идентификатор в адресе доставки магазина.</li>
                    <li>Мы не принимаем посылки с оплатой при получении (COD / Наложенный платеж). Заказ должен быть полностью оплачен вами в магазине.</li>
                    <li>Товары, запрещенные к международной авиаперевозке, не принимаются и будут возвращены продавцу за ваш счет.</li>
                </ul>
            </div>

        </div>
    </div>
</x-layouts.public>