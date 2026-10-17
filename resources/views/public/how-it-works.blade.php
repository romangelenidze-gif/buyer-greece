<x-layouts.public 
    title="{{ __('Как это работает | Buyer Greece') }}"
    description="{{ __('Пошаговое руководство по выкупу товаров и форвардингу из Греции в Грузию.') }}"
>
    <div class="py-12 bg-[#f2f2f2] min-h-[calc(100vh-4rem)]">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <div class="text-center space-y-2">
                <h1 class="apple-section-title">{{ __('Как работает сервис') }}</h1>
                <p class="apple-section-subtitle">{{ __('Простой и прозрачный процесс от заявки до получения') }}</p>
            </div>

            <div class="apple-card space-y-6">
                <div class="border-b border-black/[0.06] pb-5">
                    <h2 class="text-base font-semibold text-[#1d1d1f]">{{ __('1. Сценарий «Выкупите за меня»') }}</h2>
                    <p class="text-xs text-[#86868b] mt-1.5 leading-relaxed">{{ __('Вы отправляете ссылку → Мы рассчитываем стоимость (Quote) → Вы оплачиваете переводом → Мы покупаем товар в магазине Греции.') }}</p>
                </div>

                <div class="border-b border-black/[0.06] pb-5">
                    <h2 class="text-base font-semibold text-[#1d1d1f]">{{ __('2. Сценарий «Форвардинг»') }}</h2>
                    <p class="text-xs text-[#86868b] mt-1.5 leading-relaxed">{{ __('Вы покупаете сами в греческом магазине на наш адрес склада → Добавляете трек-номер в кабинет → Мы принимаем и подготавливаем посылку.') }}</p>
                </div>

                <div>
                    <h2 class="text-base font-semibold text-[#1d1d1f]">{{ __('3. Передача в Camex') }}</h2>
                    <p class="text-xs text-[#86868b] mt-1.5 leading-relaxed">{{ __('Подготовленный груз передается международному перевозчику Camex для отправки в Грузию. Оплата транспортировки производится компании Camex при получении.') }}</p>
                </div>
            </div>
           
            <!-- STILL NEED HELP CTA -->
            <div class="bg-[#1d1d1f] text-white rounded-[2.5rem] p-8 sm:p-10 text-center space-y-4 shadow-xl">
                <h3 class="text-2xl font-semibold tracking-tight">{{ __('Остались вопросы по процессам?') }}</h3>
                <p class="text-sm text-[#86868b] max-w-md mx-auto leading-relaxed">
                    {{ __('Напишите нам напрямую, если вам требуется помощь с оформлением или консультация по греческим магазинам.') }}
                </p>
                
                <!-- Кнопки мессенджеров в ряд -->
                <x-messenger-row align="justify-center" />
            </div>


        </div>
    </div>
</x-layouts.public>
