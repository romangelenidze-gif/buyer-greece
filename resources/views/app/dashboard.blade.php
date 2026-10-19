<x-layouts.app title="{{ __('Личный кабинет') }} | Buyer Greece">
    <div class="space-y-8">

        <!-- WELCOME HEADER -->
        <div class="apple-card flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="app-text-title">
                    {{ __('Добро пожаловать,') }} {{ Auth::user()->name }}!
                </h1>
                <p class="app-text-sub mt-1">
                    {{ __('Управляйте вашими выкупами в Греции и отслеживайте статус посылок.') }}
                </p>
            </div>
            
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <a href="{{ route('buy-for-me') }}" class="apple-btn-primary w-full sm:w-auto text-center">
                    + {{ __('Создать новый заказ') }}
                </a>
            </div>
        </div>

        <!-- STATS OVERVIEW (Bento Grid) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div class="apple-bento-card">
                <div class="app-badge-text text-[#86868b]">{{ __('Ожидаемые посылки') }}</div>
                <div class="app-text-title text-3xl mt-3">{{ $stats['expected_packages'] ?? 0 }}</div>
                <div class="app-text-sub mt-1">{{ __('На складе в Афинах') }}</div>
            </div>

            <div class="apple-bento-card">
                <div class="app-badge-text text-[#86868b]">{{ __('Прибыло на склад') }}</div>
                <div class="app-text-title text-3xl mt-3">{{ $stats['received_packages'] ?? 0 }}</div>
                <div class="app-text-sub mt-1">{{ __('Готовы к консолидации') }}</div>
            </div>

            <div class="apple-bento-card">
                <div class="app-badge-text text-[#86868b]">{{ __('Отправки в Грузию') }}</div>
                <div class="app-text-title text-3xl mt-3">{{ $stats['ready_shipments'] ?? 0 }}</div>
                <div class="app-text-sub mt-1">{{ __('В пути или переданы Camex') }}</div>
            </div>
        </div>

        <!-- GREEK ADDRESS WIDGET -->
        @php
            $customer = Auth::user()->customer;
            $hasCamex = $customer && !empty($customer->camex_personal_number);
        @endphp

        <div class="apple-card space-y-4">
            <div class="flex items-center justify-between border-b border-black/[0.06] pb-4">
                <div>
                    <h2 class="app-text-title">{{ __('Ваш адрес приема в Греции (Форвардинг)') }}</h2>
                    <p class="app-text-sub mt-0.5">{{ __('Используйте эти реквизиты при самостоятельных покупках в магазинах Греции') }}</p>
                </div>
                @if($hasCamex)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full app-badge-text border bg-emerald-50 text-emerald-700 border-emerald-200/80">
                        {{ __('Адрес активен') }}
                    </span>
                @endif
            </div>

            @if($hasCamex)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                    <x-forms.copy-field label="{{ __('Имя получателя (Full Name)') }}" value="{{ Auth::user()->name }} / {{ $customer->camex_personal_number }}" />
                    <x-forms.copy-field label="{{ __('Улица и номер дома (Street Address)') }}" value="Agiou Georgiou 14, Buyer Greece Warehouse" />
                    <x-forms.copy-field label="{{ __('Город и Индекс (City / Zip Code)') }}" value="Athens, 10433" />
                    <x-forms.copy-field label="{{ __('Код Camex (Personal ID)') }}" value="{{ $customer->camex_personal_number }}" />
                </div>
            @else
                <div class="p-4 bg-amber-50/70 border border-amber-200/60 rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="app-text-sub text-amber-900">
                        <strong class="font-semibold block mb-0.5">{{ __('Адрес приемки пока недоступен') }}</strong>
                        {{ __('Для получения персонального адреса в Греции укажите ваш') }} <strong>Camex personal number</strong> {{ __('в профиле.') }}
                    </div>
                    <a href="{{ route('profile.show') }}" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-full transition-colors shrink-0">
                        {{ __('Заполнить в профиле') }} →
                    </a>
                </div>
            @endif
        </div>

        <!-- ACTIVE ORDERS -->
        <div class="apple-card space-y-4">
            <div class="flex items-center justify-between border-b border-black/[0.06] pb-4">
                <div>
                    <h2 class="app-text-title">{{ __('Ваши заказы («Выкупите за меня»)') }}</h2>
                    <p class="app-text-sub mt-0.5">{{ __('Все текущие и завершенные заявки на выкуп товаров') }}</p>
                </div>
            </div>

            @if(isset($activeOrders) && $activeOrders->count() > 0)
                <div class="divide-y divide-black/[0.06]">
                    @foreach($activeOrders as $order)
                        @php $firstItem = $order->items->first(); @endphp
                        <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-black/[0.02] rounded-2xl px-3 transition-colors">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono app-text-sub font-semibold text-[#1d1d1f] bg-black/[0.05] px-2.5 py-0.5 rounded-full">
                                        {{ $order->public_order_number }}
                                    </span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full app-badge-text border bg-amber-50 text-amber-800 border-amber-200/80 font-semibold">
                                        {{ is_object($order->status) ? $order->status->value : $order->status }}
                                    </span>
                                </div>

                                <h3 class="app-text-title">
                                    {{ $firstItem ? $firstItem->product_name : __('Заказ товара') }}
                                </h3>

                                <div class="app-text-sub">
                                    {{ __('Создан:') }} {{ $order->created_at ? $order->created_at->format('d.m.Y H:i') : '' }}
                                </div>
                            </div>

                            <div class="flex items-center justify-between sm:justify-end gap-3">
                                @if($order->activeQuote)
                                    <div class="text-right mr-2">
                                        <div class="app-text-sub">{{ __('Сумма к оплате:') }}</div>
                                        <div class="app-text-title text-[#0071e3]">€{{ number_format($order->activeQuote->total, 2) }}</div>
                                    </div>
                                @endif

                                <a href="{{ route('orders.show', $order->id) }}" class="px-4 py-2 app-text-sub font-semibold text-[#0071e3] bg-blue-50/70 hover:bg-blue-100/70 rounded-full transition-colors inline-flex items-center gap-1">
                                    <span>{{ __('Детали') }}</span>
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
                    <div class="w-12 h-12 bg-black/[0.04] text-[#86868b] rounded-full flex items-center justify-center mx-auto">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 11h14l1 12H4L5 11z" />
                        </svg>
                    </div>
                    <div class="app-text-title">{{ __('У вас пока нет активных заказов') }}</div>
                    <p class="app-text-sub max-w-sm mx-auto">
                        {{ __('Отправьте ссылку на нужный товар из интернет-магазина Греции, и мы рассчитаем стоимость выкупа.') }}
                    </p>
                    <div class="pt-2">
                        <a href="{{ route('buy-for-me') }}" class="apple-btn-primary">
                            {{ __('Создать первый заказ') }}
                        </a>
                    </div>
                </div>
            @endif
        </div>

    </div>
</x-layouts.app>