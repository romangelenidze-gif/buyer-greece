<x-layouts.public 
    title="{{ __('Как это работает | Buyer Greece') }}"
    description="{{ __('Пошаговое руководство по выкупу товаров и форвардингу из Греции в Грузию.') }}"
>
    <div class="py-6 bg-[#f2f2f2] min-h-[calc(100vh-4rem)]">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <div class="text-center space-y-2">
                <h1 class="apple-section-title text-left sm:text-center">{{ __('Как работает сервис') }}</h1>
                <p class="apple-section-subtitle text-left sm:text-center">{{ __('Простой и прозрачный процесс от заявки до получения') }}</p>
            </div>

            <div class="apple-card space-y-6">
                <div class="border-b border-black/[0.06] pb-5">
                    <h2 class="app-text-title">{{ __('1. Сценарий «Выкупите за меня»') }}</h2>
                    <p class="app-text-body mt-1.5">{{ __('Вы отправляете ссылку → Мы рассчитываем стоимость (Quote) → Вы оплачиваете переводом → Мы покупаем товар в магазине Греции.') }}</p>
                </div>

                <div class="border-b border-black/[0.06] pb-5">
                    <h2 class="app-text-title">{{ __('2. Сценарий «Форвардинг»') }}</h2>
                    <p class="app-text-body mt-1.5">{{ __('Вы покупаете сами в греческом магазине на наш адрес склада → Добавляете трек-номер в кабинет → Мы принимаем и подготавливаем посылку.') }}</p>
                </div>

                <div>
                    <h2 class="app-text-title">{{ __('3. Передача в Camex') }}</h2>
                    <p class="app-text-body mt-1.5">{{ __('Подготовленный груз передается международному перевозчику Camex для отправки в Грузию. Оплата транспортировки производится компании Camex при получении.') }}</p>
                </div>
            </div>
           
            <!-- STILL NEED HELP CTA -->
            <div class="apple-dark-cta">
                <h3 class="apple-dark-cta-title">{{ __('Остались вопросы по процессам?') }}</h3>
                <p class="apple-dark-cta-desc">{{ __('Напишите нам напрямую, если вам требуется помощь с оформлением или консультация по греческим магазинам.') }}</p>
                <x-messenger-row align="justify-center" />
            </div>

        </div>
    </div>
</x-layouts.public>