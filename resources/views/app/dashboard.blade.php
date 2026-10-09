<x-layouts.app title="Личный кабинет | Buyer Greece">
    <div class="space-y-8">

        <!-- WELCOME HEADER -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">
                    Добро пожаловать, {{ Auth::user()->name }}!
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Управляйте вашими выкупами в Греции и отслеживайте статус посылок.
                </p>
            </div>
            
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <x-buttons.primary href="{{ route('buy-for-me') }}" class="w-full sm:w-auto text-center justify-center">
                    + Создать новый заказ
                </x-buttons.primary>
            </div>
        </div>

        <!-- ALERT MESSAGES -->
        @if (session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-medium flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- STATS OVERVIEW -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Ожидаемые посылки</div>
                <div class="text-2xl font-black text-slate-900 mt-2">{{ $stats['expected_packages'] ?? 0 }}</div>
                <div class="text-[11px] text-slate-400 mt-1">На складе в Афинах</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Прибыло на склад</div>
                <div class="text-2xl font-black text-slate-900 mt-2">{{ $stats['received_packages'] ?? 0 }}</div>
                <div class="text-[11px] text-slate-400 mt-1">Готовы к консолидации</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Отправки в Грузию</div>
                <div class="text-2xl font-black text-slate-900 mt-2">{{ $stats['ready_shipments'] ?? 0 }}</div>
                <div class="text-[11px] text-slate-400 mt-1">В пути или переданы Camex</div>
            </div>
        </div>

        <!-- GREEK ADDRESS WIDGET (FORWARDING) -->
        @php
            $customer = Auth::user()->customer;
            $hasCamex = $customer && !empty($customer->camex_personal_number);
        @endphp

        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Ваш адрес приема в Греции (Форвардинг)</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Используйте эти реквизиты при самостоятельных покупках в магазинах Греции</p>
                </div>
                @if($hasCamex)
                    <span class="px-2.5 py-1 text-xs font-semibold text-emerald-700 bg-emerald-50 rounded-full border border-emerald-200">
                        Адрес активен
                    </span>
                @endif
            </div>

            @if($hasCamex)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                    <x-forms.copy-field label="Имя получателя (Full Name)" value="{{ Auth::user()->name }} / {{ $customer->camex_personal_number }}" />
                    <x-forms.copy-field label="Улица и номер дома (Street Address)" value="Agiou Georgiou 14, Buyer Greece Warehouse" />
                    <x-forms.copy-field label="Город и Индекс (City / Zip Code)" value="Athens, 10433" />
                    <x-forms.copy-field label="Код Camex (Personal ID)" value="{{ $customer->camex_personal_number }}" />
                </div>
            @else
                <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-xs text-amber-900">
                        <strong class="font-bold block mb-0.5">Адрес приемки пока недоступен</strong>
                        Для получения персонального адреса в Греции укажите ваш <strong>Camex personal number</strong> в профиле.
                    </div>
                    <a href="{{ route('profile.show') }}" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-semibold text-xs rounded-lg transition-colors flex-shrink-0">
                        Заполнить в профиле →
                    </a>
                </div>
            @endif
        </div>

        <!-- ACTIVE ORDERS TABLE / LIST -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Ваши заказы («Выкупите за меня»)</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Все текущие и завершенные заявки на выкуп товаров</p>
                </div>
            </div>

            @if(isset($activeOrders) && $activeOrders->count() > 0)
                <div class="divide-y divide-slate-100">
                    @foreach($activeOrders as $order)
                        @php
                            $firstItem = $order->items->first();
                        @endphp
                        <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50/50 rounded-xl px-2 transition-colors">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-xs font-bold text-slate-900 bg-slate-100 px-2 py-0.5 rounded">
                                        {{ $order->public_order_number }}
                                    </span>
                                    
                                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-amber-100 text-amber-800 border border-amber-200">
                                        {{ is_object($order->status) ? $order->status->value : $order->status }}
                                    </span>
                                </div>

                                <h3 class="text-sm font-bold text-slate-900">
                                    {{ $firstItem ? $firstItem->product_name : 'Заказ товара' }}
                                    @if($order->items->count() > 1)
                                        <span class="text-xs font-normal text-slate-500">(и еще {{ $order->items->count() - 1 }} поз.)</span>
                                    @endif
                                </h3>

                                <div class="text-xs text-slate-400">
                                    Создан: {{ $order->created_at ? $order->created_at->format('d.m.Y H:i') : '' }}
                                </div>
                            </div>

                            <div class="flex items-center justify-between sm:justify-end gap-3">
                                @if($order->activeQuote)
                                    <div class="text-right mr-2">
                                        <div class="text-xs text-slate-500">Сумма к оплате:</div>
                                        <div class="text-sm font-extrabold text-brand-600">€{{ number_format($order->activeQuote->total, 2) }}</div>
                                    </div>
                                @endif

                                <x-messenger type="telegram" :orderNumber="$order->public_order_number" size="small">
                                    Чат
                                </x-messenger>

                                <a href="{{ route('orders.show', $order->id) }}" class="px-3 py-1.5 text-xs font-semibold text-brand-600 hover:text-brand-700 bg-brand-50 hover:bg-brand-100 rounded-lg transition-colors inline-flex items-center gap-1">
                                    <span>Детали</span>
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 space-y-3">
                    <div class="w-12 h-12 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 11h14l1 12H4L5 11z" />
                        </svg>
                    </div>
                    <div class="text-sm font-semibold text-slate-700">У вас пока нет активных заказов</div>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto">
                        Отправьте ссылку на нужный товар из интернет-магазина Греции, и мы рассчитаем стоимость выкупа.
                    </p>
                    <div class="pt-2">
                        <x-buttons.primary href="{{ route('buy-for-me') }}" size="small">
                            Создать первый заказ
                        </x-buttons.primary>
                    </div>
                </div>
            @endif
        </div>

        <!-- MESSENGER SUPPORT BANNER -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="space-y-1 text-center sm:text-left">
                <h3 class="text-base font-bold text-slate-900">Нужна оперативная помощь менеджеру?</h3>
                <p class="text-xs text-slate-500">Напишите нам в мессенджер — ответим на любой вопрос по поиску, покупкам и доставке.</p>
            </div>
            <div class="flex gap-2">
                <x-messenger type="telegram" size="default" />
                <x-messenger type="whatsapp" size="default" />
            </div>
        </div>

    </div>
</x-layouts.app>