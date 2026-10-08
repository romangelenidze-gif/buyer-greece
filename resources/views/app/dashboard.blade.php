<x-layouts.app title="Дашборд | Buyer Greece">

    <div class="space-y-8">

        <!-- TOP GREETING & PRIMARY ACTIONS -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                    Здравствуйте, {{ Auth::user()->first_name ?? Auth::user()->name ?? 'Клиент' }}!
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Управляйте вашими заказами, покупками и греческим адресом в едином окне.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                <x-buttons.primary href="{{ route('buy-for-me') }}" size="default" class="w-full sm:w-auto">
                    Выкупите за меня
                </x-buttons.primary>
                <a href="{{ route('forwarding') }}" class="w-full sm:w-auto text-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition-colors">
                    Инструкция форвардинга
                </a>
            </div>
        </div>

        <!-- GRID: GREEK ADDRESS WIDGET & SUMMARY STATS -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- WIDGET: MY GREEK ADDRESS (FORWARDING) -->
            <div class="lg:col-span-2 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm flex flex-col justify-between">
                <div class="space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <h2 class="font-bold text-slate-900 text-base">Мой греческий адрес для покупок</h2>
                        </div>
                        <span class="text-xs text-slate-400 font-mono">Греция (GR)</span>
                    </div>

                    <!-- Rule Check: camex_personal_number -->
                    @if(Auth::user()->camex_personal_number)
                        <p class="text-xs text-slate-600">
                            Указывайте этот адрес в интернет-магазинах Греции при самостоятельной покупке:
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                            <x-forms.copy-field 
                                label="Имя получателя" 
                                value="{{ Auth::user()->name }}" 
                            />
                            
                            <x-forms.copy-field 
                                label="Персональный код Camex" 
                                value="{{ Auth::user()->camex_personal_number }}" 
                            />

                            <x-forms.copy-field 
                                label="Улица и дом (Address Line 1)" 
                                value="Agiou Georgiou 12, (ID: {{ Auth::user()->customer_number ?? 'BG-'.Auth::id() }})" 
                            />

                            <x-forms.copy-field 
                                label="Город и Индекс" 
                                value="Thessaloniki, 56429" 
                            />
                        </div>

                        <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-500 flex items-start gap-2">
                            <svg class="w-4 h-4 text-brand-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Обязательно указывайте ваш внутренний ID и код Camex в строке адреса магазина.</span>
                        </div>
                    @else
                        <!-- Warning when camex_personal_number is empty -->
                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 space-y-3">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <div class="text-xs text-amber-900 space-y-1">
                                    <p class="font-bold">Греческий адрес скрыт</p>
                                    <p>Внесите ваш персональный номер Camex в профиле перед использованием адреса склада в Греции.</p>
                                </div>
                            </div>
                            <div class="pt-1">
                                <a href="{{ route('profile.show') }}" class="inline-flex items-center px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white font-semibold text-xs rounded-md transition-colors">
                                    Заполнить номер Camex в профиле →
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- STATS / COUNTERS SUMMARY -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm flex flex-col justify-between space-y-4">
                <h2 class="font-bold text-slate-900 text-base border-b border-slate-100 pb-3">Сводка посылкам</h2>

                <div class="space-y-3">
                    <div class="flex justify-between items-center p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <span class="text-xs font-semibold text-slate-600">Ожидаются складом:</span>
                        <span class="font-bold text-sm text-slate-900">{{ $stats['expected_packages'] ?? 0 }}</span>
                    </div>

                    <div class="flex justify-between items-center p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <span class="text-xs font-semibold text-slate-600">Получено в Греции:</span>
                        <span class="font-bold text-sm text-slate-900">{{ $stats['received_packages'] ?? 0 }}</span>
                    </div>

                    <div class="flex justify-between items-center p-3 bg-brand-50 rounded-xl border border-brand-100">
                        <span class="text-xs font-semibold text-brand-900">Готовы к отправке Camex:</span>
                        <span class="font-bold text-sm text-brand-700">{{ $stats['ready_shipments'] ?? 0 }}</span>
                    </div>
                </div>

                <div class="pt-2">
                    <a href="{{ route('packages.index') }}" class="block text-center text-xs font-semibold text-brand-600 hover:text-brand-700">
                        Все посылки →
                    </a>
                </div>
            </div>

        </div>

        <!-- ACTIVE ORDERS SECTION -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-6">
            <div class="flex justify-between items-center border-b border-slate-100 pb-4">
                <h2 class="font-bold text-slate-900 text-base">Активные заказы</h2>
                <a href="{{ route('orders.index') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700">
                    История всех заказов →
                </a>
            </div>

            @if(isset($activeOrders) && count($activeOrders) > 0)
                <div class="space-y-4">
                    @foreach($activeOrders as $order)
                        <div class="border border-slate-200 rounded-xl p-4 hover:border-slate-300 transition-colors flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-xs font-bold text-slate-900">#{{ $order->order_number }}</span>
                                    <x-status-badge :status="$order->status" :label="$order->status_label ?? $order->status" />
                                </div>
                                <h3 class="text-sm font-semibold text-slate-800">{{ $order->product_name ?? 'Товар из Греции' }}</h3>
                                <p class="text-xs text-slate-500">Дата создания: {{ $order->created_at ? $order->created_at->format('d.m.Y') : 'Сегодня' }}</p>
                            </div>

                            <div class="flex items-center gap-3 w-full md:w-auto">
                                @if($order->status === 'quote_ready')
                                    <a href="{{ route('orders.show', $order->id) }}" class="w-full md:w-auto px-4 py-2 bg-amber-600 hover:bg-amber-500 text-white font-semibold text-xs rounded-lg text-center transition-colors">
                                        Рассмотреть расчет (Quote)
                                    </a>
                                @elseif($order->status === 'awaiting_payment')
                                    <a href="{{ route('orders.show', $order->id) }}" class="w-full md:w-auto px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs rounded-lg text-center transition-colors">
                                        Оплатить переводом
                                    </a>
                                @else
                                    <a href="{{ route('orders.show', $order->id) }}" class="w-full md:w-auto px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-lg text-center transition-colors">
                                        Детали заказа
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- EMPTY STATE -->
                <div class="text-center py-8 space-y-3">
                    <div class="w-12 h-12 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 11h14l1 12H4L5 11z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800">У вас пока нет активных заказов</h3>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto">
                        Отправьте ссылку на нужный товар из магазина Греции, или оформите покупку самостоятельно на наш греческий адрес.
                    </p>
                    <div class="pt-2">
                        <x-buttons.primary href="{{ route('buy-for-me') }}" size="small">
                            Отправить ссылку на товар
                        </x-buttons.primary>
                    </div>
                </div>
            @endif
        </div>

    </div>

</x-layouts.app>