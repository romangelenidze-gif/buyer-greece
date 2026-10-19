<x-layouts.public 
    title="{{ __('Форвардинг | Греческий адрес для покупок | Buyer Greece') }}"
    description="{{ __('Покупайте в интернет-магазинах Греции самостоятельно и используйте наш адрес приема для последующей доставки в Грузию через Camex.') }}"
>
    <div class="py-6 bg-[#f2f2f2] min-h-[calc(100vh-4rem)]">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <!-- HERO -->
            <div class="text-center max-w-3xl mx-auto space-y-4">
                <h1 class="apple-section-title text-left sm:text-center">
                    {{ __('Покупайте сами в магазинах Греции — мы примем и подготовим посылку') }}
                </h1>
                <p class="apple-hero-subtitle text-left sm:text-center mx-auto">
                    {{ __('Используйте наш виртуальный адрес в Греции при оформлении заказов. После приема на складе мы подготавливаем посылку и передаем её компании Camex для международной доставки.') }}
                </p>
            </div>

            <!-- 7 STEPS FLOW -->
            <div class="apple-card">
                <h2 class="app-text-title mb-6">{{ __('Как работает форвардинг (7 шагов)') }}</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-full bg-[#1d1d1f] text-white font-regular flex items-center justify-center shrink-0">1</div>
                        <div>
                            <h3 class="app-text-title !text-base">{{ __('Зарегистрируйтесь на сайте') }}</h3>
                            <p class="app-text-sub mt-1">{{ __('Создайте личный кабинет для доступа к настройкам и отслеживанию посылок.') }}</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-full bg-[#1d1d1f] text-white font-regular flex items-center justify-center shrink-0">2</div>
                        <div>
                            <h3 class="app-text-title !text-base">{{ __('Укажите ваш персональный номер Camex') }}</h3>
                            <p class="app-text-sub mt-1">{{ __('Внесите код Camex в профиле. Без этого адреса приемки не отображаются.') }}</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-full bg-[#1d1d1f] text-white font-regular flex items-center justify-center shrink-0">3</div>
                        <div>
                            <h3 class="app-text-title !text-base">{{ __('Получите реквизиты адреса в Греции') }}</h3>
                            <p class="app-text-sub mt-1">{{ __('Скопируйте адрес склада и ваш идентификатор клиента в кабинете.') }}</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-full bg-[#1d1d1f] text-white font-regular flex items-center justify-center shrink-0">4</div>
                        <div>
                            <h3 class="app-text-title !text-base">{{ __('Оформите заказ в интернет-магазине') }}</h3>
                            <p class="app-text-sub mt-1">{{ __('Укажите наш адрес доставки и обязательно добавьте ваш идентификатор.') }}</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-full bg-[#1d1d1f] text-white font-regular  flex items-center justify-center shrink-0">5</div>
                        <div>
                            <h3 class="app-text-title !text-base">{{ __('Добавьте ожидаемую посылку (трек-номер)') }}</h3>
                            <p class="app-text-sub mt-1">{{ __('Внесите трек-номер магазина в кабинет, чтобы мы быстрее идентифицировали груз.') }}</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-full bg-[#1d1d1f] text-white font-regular flex items-center justify-center shrink-0">6</div>
                        <div>
                            <h3 class="app-text-title !text-base">{{ __('Прием и обработка в Греции') }}</h3>
                            <p class="app-text-sub mt-1">{{ __('Мы принимаем заказ, взвешиваем и подготавливаем документы.') }}</p>
                        </div>
                    </div>

                    <div class="flex gap-4 md:col-span-2 bg-blue-50/70 p-4 rounded-2xl border border-blue-200/60">
                        <div class="w-8 h-8 rounded-full bg-[#0071e3] text-white font-regular flex items-center justify-center shrink-0">7</div>
                        <div>
                            <h3 class="app-text-title !text-base text-blue-900">{{ __('Передача перевозчику Camex') }}</h3>
                            <p class="app-text-sub text-blue-800 mt-1">{{ __('После передачи посылки Camex международная транспортировка в Грузию и выдача отслеживаются на стороне Camex.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CAMEX NOTICE & CALL TO ACTION -->
            <div class="apple-card bg-amber-50/70 border-amber-200/60 flex flex-col sm:flex-row items-center justify-between gap-6">
                <div class="space-y-2">
                    <h3 class="app-text-title text-amber-900">{{ __('Нужен адрес приема в Греции?') }}</h3>
                    <p class="app-text-body text-amber-800">
                        {{ __('Адрес предоставляется бесплатно в Личном кабинете после добавления вашего персонального номера Camex.') }}
                    </p>
                </div>
                @auth
                    <a href="{{ route('dashboard') }}" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-full shrink-0 transition-colors">
                        {{ __('Открыть мой адрес') }}
                    </a>
                @else
                    <a href="{{ route('register') }}" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-full shrink-0 transition-colors">
                        {{ __('Зарегистрироваться') }}
                    </a>
                @endauth
            </div>

            <!-- IMPORTANT RULES -->
            <div class="apple-card space-y-4">
                <h3 class="app-text-title">{{ __('Правила приема самостоятельных заказов') }}</h3>
                <ul class="space-y-2 app-text-sub list-disc list-inside leading-relaxed">
                    <li>{{ __('Всегда указывайте ваш уникальный идентификатор в адресе доставки магазина.') }}</li>
                    <li>{{ __('Мы не принимаем посылки с оплатой при получении (COD / Наложенный платеж). Заказ должен быть полностью оплачен вами в магазине.') }}</li>
                    <li>{{ __('Товары, запрещенные к международной авиаперевозке, не принимаются и будут возвращены продавцу за ваш счет.') }}</li>
                </ul>
            </div>

            <!-- STILL NEED HELP CTA -->
            <div class="apple-dark-cta">
                <h3 class="apple-dark-cta-title">{{ __('Возникли сложности при заполнении адреса в магазине?') }}</h3>
                <p class="apple-dark-cta-desc">{{ __('Напишите менеджеру — мы поможем правильно ввести адрес на греческом сайте.') }}</p>
                <x-messenger-row align="justify-center" />
            </div>

        </div>
    </div>
</x-layouts.public>