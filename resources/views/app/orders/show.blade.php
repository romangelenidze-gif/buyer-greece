<x-layouts.app title="Заказ {{ $order->public_order_number }} | Buyer Greece">
    <div class="max-w-4xl mx-auto space-y-6">

        <!-- HEADER & BACK BUTTON -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1 text-xs text-slate-500 hover:text-slate-800 transition-colors mb-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Назад к заказам
                </a>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">
                        Заказ № {{ $order->public_order_number }}
                    </h1>
                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800 border border-amber-200">
                        {{ is_object($order->status) ? $order->status->value : $order->status }}
                    </span>
                </div>
            </div>
            
            <div class="text-xs text-slate-500">
                Создан: {{ $order->created_at ? $order->created_at->format('d.m.Y H:i') : now()->format('d.m.Y') }}
            </div>
        </div>

        <!-- SUCCESS ALERT -->
        @if (session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-medium flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- SECTION 1: ORDER ITEMS -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
            <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Товары в заказе</h2>

            @forelse($order->items as $item)
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 py-3 border-b border-slate-100 last:border-0">
                    <div class="space-y-1">
                        <h3 class="font-bold text-slate-900 text-sm">{{ $item->product_name }}</h3>
                        @if($item->product_url)
                            <a href="{{ $item->product_url }}" target="_blank" class="text-xs text-brand-600 hover:underline inline-flex items-center gap-1">
                                <span>Открыть ссылку на товар</span>
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </a>
                        @endif
                        <div class="flex flex-wrap gap-2 text-xs text-slate-500 pt-1">
                            @if($item->size) <span>Размер: <strong class="text-slate-700">{{ $item->size }}</strong></span> @endif
                            @if($item->color) <span>Цвет: <strong class="text-slate-700">{{ $item->color }}</strong></span> @endif
                            @if($item->model) <span>Модель: <strong class="text-slate-700">{{ $item->model }}</strong></span> @endif
                        </div>
                    </div>

                    <div class="text-right flex sm:flex-col justify-between items-center sm:items-end text-xs">
                        <span class="text-slate-500">Количество: <strong>{{ $item->quantity }} шт.</strong></span>
                        @if($item->total_price)
                            <span class="text-sm font-bold text-slate-900">€{{ number_format($item->total_price, 2) }}</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-sm text-slate-500 py-4 text-center">
                    Информация о позициях обновляется менеджером.
                </div>
            @endforelse
        </div>

        <!-- SECTION 2: QUOTE / BREAKDOWN & ACCEPTANCE -->
        @if(isset($quote) && $quote)
            @php
                $quoteStatus = is_object($quote->status) ? $quote->status->value : $quote->status;
                $isAccepted = in_array($quoteStatus, ['accepted', 'approved', 'active']);
                $isRejected = in_array($quoteStatus, ['rejected', 'declined']);
            @endphp

            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h2 class="text-base font-bold text-slate-900">Расчет стоимости (Quote)</h2>
                    
                    @if($isAccepted)
                        <span class="text-xs text-emerald-700 font-semibold bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                            Расчет принят
                        </span>
                    @elseif($isRejected)
                        <span class="text-xs text-rose-700 font-semibold bg-rose-50 px-2.5 py-1 rounded-full border border-rose-200">
                            Расчет отклонен
                        </span>
                    @else
                        <span class="text-xs text-amber-700 font-semibold bg-amber-50 px-2.5 py-1 rounded-full border border-amber-200">
                            Ожидает вашего подтверждения
                        </span>
                    @endif
                </div>

                <!-- PRICE BREAKDOWN TABLE -->
                <div class="space-y-2 text-sm text-slate-600">
                    <div class="flex justify-between">
                        <span>Стоимость товаров:</span>
                        <span class="font-medium text-slate-900">€{{ number_format($quote->product_total, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Доставка по Греции:</span>
                        <span class="font-medium text-slate-900">€{{ number_format($quote->local_shipping, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Комиссия выкупа:</span>
                        <span class="font-medium text-slate-900">€{{ number_format($quote->buyer_fee, 2) }}</span>
                    </div>

                    @if($quote->services_total > 0)
                        <div class="flex justify-between">
                            <span>Доп. услуги:</span>
                            <span class="font-medium text-slate-900">€{{ number_format($quote->services_total, 2) }}</span>
                        </div>
                    @endif

                    @if($quote->other_costs > 0)
                        <div class="flex justify-between">
                            <span>Прочие расходы:</span>
                            <span class="font-medium text-slate-900">€{{ number_format($quote->other_costs, 2) }}</span>
                        </div>
                    @endif

                    @if($quote->discount > 0)
                        <div class="flex justify-between text-emerald-600">
                            <span>Скидка:</span>
                            <span class="font-medium">-€{{ number_format($quote->discount, 2) }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between pt-3 border-t border-slate-200 text-base font-extrabold text-slate-900">
                        <span>Итого к оплате:</span>
                        <span class="text-brand-600">€{{ number_format($quote->total, 2) }}</span>
                    </div>
                </div>

                @if($quote->notes)
                    <div class="p-3 bg-slate-50 border border-slate-100 rounded-xl text-xs text-slate-600">
                        <strong class="text-slate-700 block mb-1">Примечание менеджера:</strong>
                        {{ $quote->notes }}
                    </div>
                @endif

                <!-- ACCEPT / REJECT BUTTONS FOR PENDING QUOTES -->
                @if(!$isAccepted && !$isRejected)
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-3">
                        <p class="text-xs text-slate-600">
                            Пожалуйста, проверьте итоговую стоимость. После принятия расчета станет доступна форма загрузки чека оплаты.
                        </p>
                        
                        <div class="flex flex-col sm:flex-row gap-3 pt-1">
                            <form action="{{ route('orders.quotes.accept', [$order->id, $quote->id]) }}" method="POST" class="w-full sm:w-auto">
                                @csrf
                                <x-buttons.primary type="submit" class="w-full justify-center">
                                    Принять расчет и перейти к оплате
                                </x-buttons.primary>
                            </form>

                            <form action="{{ route('orders.quotes.reject', [$order->id, $quote->id]) }}" method="POST" class="w-full sm:w-auto">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-lg transition-colors text-center">
                                    Отклонить расчет
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

                <!-- BANK TRANSFER PAYMENT FORM WITH COPY FIELDS -->
                @if($isAccepted || $order->active_quote_id === $quote->id)
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 space-y-4">
                        <h3 class="text-sm font-bold text-slate-900">Оплата банковским переводом (Bank Transfer)</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Переведите итоговую сумму <strong>€{{ number_format($quote->total, 2) }}</strong> на банковские реквизиты сервиса и загрузите квитанцию об оплате ниже.
                        </p>

                        <!-- COPYABLE REQUISITES -->
                        <div class="space-y-3 pt-1">
                            <x-forms.copy-field 
                                label="IBAN / Расчетный счет" 
                                value="{{ config('services.bank.iban', 'GR1234567890123456789012345') }}" 
                            />
                            <x-forms.copy-field 
                                label="Сумма к оплате (€)" 
                                value="{{ number_format($quote->total, 2, '.', '') }}" 
                            />
                            <x-forms.copy-field 
                                label="Назначение платежа" 
                                value="Оплата заказа № {{ $order->public_order_number }}" 
                            />
                        </div>

                        <form action="{{ route('orders.payments.store', $order->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4 pt-3 border-t border-slate-200">
                            @csrf
                            <div>
                                <label for="receipt" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Квитанция / Чек оплаты (PDF, JPG, PNG до 10MB) <span class="text-rose-500">*</span>
                                </label>
                                <input 
                                    type="file" 
                                    name="receipt" 
                                    id="receipt" 
                                    accept="image/*,.pdf" 
                                    required 
                                    class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 transition-colors"
                                >
                                @error('receipt')
                                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <x-buttons.primary type="submit" size="default">
                                Отправить чек на проверку
                            </x-buttons.primary>
                        </form>
                    </div>
                @endif
            </div>
        @else
            <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6 text-center space-y-2">
                <h3 class="text-sm font-bold text-blue-900">Расчет готовится менеджером</h3>
                <p class="text-xs text-blue-700">
                    Мы проверяем наличие товара в греческом магазине. Когда расчет будет готов, здесь появится итоговая сумма и кнопка подтверждения.
                </p>
            </div>
        @endif

        <!-- CONTEXTUAL SUPPORT BOX -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Нужна консультация по заказу?</h3>
                <p class="text-xs text-slate-500 mt-0.5">Менеджер ответит на любой вопрос по покупке, доставке или оплате.</p>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <x-messenger type="telegram" :orderNumber="$order->public_order_number" size="small" />
                <x-messenger type="whatsapp" :orderNumber="$order->public_order_number" size="small" />
            </div>
        </div>

        <!-- SECTION 3: UPLOADED PAYMENTS HISTORY -->
        @if($order->payments && $order->payments->count() > 0)
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
                <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">История платежей</h2>
                <div class="space-y-3">
                    @foreach($order->payments as $payment)
                        <div class="flex items-center justify-between text-xs p-3 bg-slate-50 rounded-xl border border-slate-200">
                            <div>
                                <span class="font-bold text-slate-900">€{{ number_format($payment->amount, 2) }}</span>
                                <span class="text-slate-500 ml-2">({{ $payment->created_at ? $payment->created_at->format('d.m.Y H:i') : '' }})</span>
                            </div>
                            <span class="px-2 py-0.5 font-semibold rounded-md bg-amber-100 text-amber-800">
                                {{ $payment->status }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</x-layouts.app>