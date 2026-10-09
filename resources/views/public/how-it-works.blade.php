<x-layouts.public 
    title="Как это работает | Buyer Greece"
    description="Пошаговое руководство по выкупу товаров и форвардингу из Греции в Грузию."
>
    <div class="py-12 bg-slate-50 min-h-[calc(100vh-4rem)]">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <div class="text-center">
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Как работает сервис</h1>
                <p class="text-slate-600 mt-2 text-sm">Простой и прозрачный процесс от заявки до получения</p>
            </div>

            <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm space-y-6">
                <div class="border-b border-slate-100 pb-4">
                    <h2 class="text-lg font-bold text-slate-900">1. Сценарий «Выкупите за меня»</h2>
                    <p class="text-xs text-slate-600 mt-1 leading-relaxed">Вы отправляете ссылку → Мы рассчитываем стоимость (Quote) → Вы оплачиваете переводом → Мы покупаем товар в магазине Греции.</p>
                </div>

                <div class="border-b border-slate-100 pb-4">
                    <h2 class="text-lg font-bold text-slate-900">2. Сценарий «Форвардинг»</h2>
                    <p class="text-xs text-slate-600 mt-1 leading-relaxed">Вы покупаете сами в греческом магазине на наш адрес склада → Добавляете трек-номер в кабинет → Мы принимаем и подготавливаем посылку.</p>
                </div>

                <div>
                    <h2 class="text-lg font-bold text-slate-900">3. Передача в Camex</h2>
                    <p class="text-xs text-slate-600 mt-1 leading-relaxed">Подготовленный груз передается международному перевозчику Camex для отправки в Грузию. Оплата транспортировки производится компании Camex при получении.</p>
                </div>
            </div>

            <!-- SUPPORT CTA -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm text-center space-y-3">
                <h3 class="text-base font-bold text-slate-900">Остались вопросы по процессом?</h3>
                <p class="text-xs text-slate-500 max-w-md mx-auto">
                    Напишите нам напрямую, если вам требуется помощь с оформлением или консультация по греческим магазинам.
                </p>
                <div class="flex justify-center gap-3 pt-2">
                    <x-messenger type="telegram" size="default" />
                    <x-messenger type="whatsapp" size="default" />
                </div>
            </div>
        </div>
    </div>
</x-layouts.public>