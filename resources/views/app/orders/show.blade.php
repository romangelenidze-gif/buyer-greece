<x-layouts.app title="Заказ #{{ $order->order_number ?? 'BG-1001' }} | Buyer Greece">

    <div class="max-w-4xl mx-auto space-y-6">

        <!-- BREADCRUMBS & TOP BAR -->
        <div class="flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
                ← Назад в Дашборд
            </a>
            <x-status-badge 
                :status="$order->status ?? 'quote_ready'" 
                :label="$order->status_label ?? 'Расчет готов'" 
            />
        </div>

        <!-- ORDER HEADER -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 border-b border-slate-100 pb-4">
                <div>
                    <span class="text-xs font-mono text-slate-400">Номер заказа</span>
                    <h1 class="text-xl font-extrabold text-slate-900">#{{ $order->order_number ?? 'BG-1001' }}</h1>
                </div>
                <div class="text-xs text-slate-500">
                    Создан: <strong class="text-slate-700">{{ isset($order->created_at) ? $order->created_at->format('d.m.Y H:i') : '08.10.2026 14:30' }}</strong>
                </div>
            </div>

            <!-- PRODUCT SUMMARY -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                <div class="md:col-span-2 space-y-1">
                    <span class="text-slate-500">Наименование товара:</span>
                    <p class="font-bold text-slate-800 text-sm">{{ $order->product_name ?? 'Zara Wool Coat (Winter Collection)' }}</p>
                    <a href="{{ $order->product_url ?? '#' }}" target="_blank" class="inline-block text-brand-600 hover:underline truncate max-w-full">
                        {{ $order->product_url ?? 'https://www.zara.com/gr/en/wool-coat-p01234567.html' }} ↗
                    </a>
                </div>
                <div class="space-y-1 bg-slate-50 p-3 rounded-xl border border-slate-100">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Количество:</span>
                        <span class="font-bold text-slate-800">{{ $order->quantity ?? 1 }} шт.</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Размер:</span>
                        <span class="font-bold text-slate-800">{{ $order->size ?? 'M' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Цвет:</span>
                        <span class="font-bold text-slate-800">{{ $order->color ?? 'Black' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- STATUS TIMELINE -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
            <h2 class="text-sm font-bold text-slate-900 mb-4">Статус выполнения</h2>
            
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 text-center text-xs">
                <!-- Step 1 -->
                <div class="p-2.5 rounded-lg border bg-emerald-50 border-emerald-200 text-emerald-800 font-medium">
                    <div class="font-bold">1. Заявка</div>
                    <div class="text-[10px] opacity-80">Принята</div>
                </div>
                <!-- Step 2 -->
                <div class="p-2.5 rounded-lg border bg-amber-50 border-amber-300 text-amber-900 font-bold shadow-sm">
                    <div class="font-bold">2. Расчет (Quote)</div>
                    <div class="text-[10px] opacity-80">Ожидает оплаты</div>
                </div>
                <!-- Step 3 -->
                <div class="p-2.5 rounded-lg border bg-slate-50 border-slate-200 text-slate-400">
                    <div class="font-bold">3. Оплата</div>
                    <div class="text-[10px]">Проверка чека</div>
                </div>
                <!-- Step 4 -->
                <div class="p-2.5 rounded-lg border bg-slate-50 border-slate-200 text-slate-400">
                    <div class="font-bold">4. Выкуп</div>
                    <div class="text-[10px]">Магазин GR</div>
                </div>
                <!-- Step 5 -->
                <div class="p-2.5 rounded-lg border bg-slate-50 border-slate-200 text-slate-400">
                    <div class="font-bold">5. Camex</div>
                    <div class="text-[10px]">Передано</div>
                </div>
            </div>
        </div>

        <!-- QUOTE & PAYMENT BLOCK -->
        <div class="bg-white border-2 border-brand-500 rounded-2xl p-6 shadow-md space-y-6">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <h2 class="text-base font-bold text-slate-900">Расчет стоимости (Quote)</h2>
                <span class="text-xs bg-brand-50 text-brand-700 px-2.5 py-1 rounded-full font-semibold border border-brand-100">
                    Готов к оплате
                </span>
            </div>

            <!-- PRICE BREAKDOWN TABLE -->
            <div class="space-y-2 text-xs font-mono">
                <div class="flex justify-between py-1 border-b border-slate-100">
                    <span class="text-slate-600">Товар в магазине Греции:</span>
                    <span class="font-bold text-slate-800">€{{ number_format($order->item_price ?? 89.90, 2) }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-100">
                    <span class="text-slate-600">Доставка магазина до склада в Греции:</span>
                    <span class="font-bold text-slate-800">€{{ number_format($order->local_shipping ?? 4.50, 2) }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-100">
                    <span class="text-slate-600">Сервисная комиссия за выкуп:</span>
                    <span class="font-bold text-slate-800">€{{ number_format($order->service_fee ?? 10.00, 2) }}</span>
                </div>
                <div class="flex justify-between py-2 text-sm font-sans font-extrabold text-slate-900 pt-3">
                    <span>ИТОГО К ОПЛАТЕ:</span>
                    <span class="text-brand-600 font-mono text-base">€{{ number_format($order->total_amount ?? 104.40, 2) }}</span>
                </div>
            </div>

            <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-xs text-amber-900 leading-relaxed">
                <strong>Обратите внимание:</strong> Доставка из Греции в Грузию оплачивается отдельно при получении посылки в компании Camex.
            </div>

            @if(($order->status ?? '') === 'expired')
                <!-- EXPIRED QUOTE WARNING -->
                <div class="bg-rose-50 border border-rose-200 rounded-2xl p-5 space-y-3">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-rose-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="text-xs text-rose-900 space-y-1">
                            <p class="font-bold text-sm">Срок действия расчета (Quote) истек</p>
                            <p>Цены на товар или стоимость локальной доставки изменились. Оплата по данному расчету временно недоступна.</p>
                        </div>
                    </div>

                    <div class="pt-2 flex items-center gap-3">
                        <x-buttons.messenger type="telegram" :order-number="$order->order_number" size="small">
                            Запросить обновленный расчет
                        </x-buttons.messenger>
                    </div>
                </div>
            @endif

            <!-- BANK TRANSFER PAYMENT FORM -->
            <div x-data="{ showPaymentForm: false }" class="pt-2">
                <div x-show="!showPaymentForm">
                    <button 
                        @click="showPaymentForm = true" 
                        type="button" 
                        class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm rounded-xl transition-colors text-center shadow-sm"
                    >
                        Оплатить €{{ number_format($order->total_amount ?? 104.40, 2) }} банковским переводом
                    </button>
                </div>

                <!-- CONTEXTUAL SUPPORT BLOCK -->
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 sm:p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="space-y-0.5">
                        <h3 class="text-xs font-bold text-slate-800">Возникли вопросы по этому заказу?</h3>
                        <p class="text-[11px] text-slate-500">Напишите менеджеру — номер заказа #{{ $order->order_number ?? 'BG-1001' }} подставится автоматически.</p>
                    </div>

                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <x-buttons.messenger type="telegram" :order-number="$order->order_number ?? 'BG-1001'" size="small" class="w-1/2 sm:w-auto">
                            Telegram
                        </x-buttons.messenger>
                        <x-buttons.messenger type="whatsapp" :order-number="$order->order_number ?? 'BG-1001'" size="small" class="w-1/2 sm:w-auto">
                            WhatsApp
                        </x-buttons.messenger>
                    </div>
                </div>

                <!-- PAYMENT DETAILS & RECEIPT UPLOAD -->
                <div x-show="showPaymentForm" x-cloak class="bg-slate-50 border border-slate-200 rounded-xl p-5 space-y-4">
                    <h3 class="text-sm font-bold text-slate-900">Реквизиты для банковского перевода</h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <x-forms.copy-field label="Получатель (Beneficiary)" value="Buyer Greece LTD" />
                        <x-forms.copy-field label="IBAN Банка" value="GR1234567890123456789012345" />
                        <x-forms.copy-field label="SWIFT / BIC" value="BANKGR22XXX" />
                        <x-forms.copy-field label="Назначение платежа (Обязательно)" value="Payment for Order #{{ $order->order_number ?? 'BG-1001' }}" />
                    </div>

                    <!-- UPLOAD FORM -->
                    <form action="{{ route('orders.payments.store', $order->id ?? 1) }}" method="POST" enctype="multipart/form-data" class="pt-4 border-t border-slate-200 space-y-4">
                        @csrf
                        
                        <div>
                            <label for="receipt_file" class="block text-xs font-semibold text-slate-800 mb-1">
                                Загрузите квитанцию / чек об оплате (PDF, JPG, PNG до 5MB) <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                type="file" 
                                name="receipt_file" 
                                id="receipt_file" 
                                accept="image/*,.pdf" 
                                required 
                                class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-600 file:text-white hover:file:bg-emerald-500 cursor-pointer"
                            >
                            @error('receipt_file')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <x-buttons.primary type="submit" size="default">
                                Подтвердить отправку чека
                            </x-buttons.primary>
                            <button @click="showPaymentForm = false" type="button" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                                Отмена
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

</x-layouts.app>