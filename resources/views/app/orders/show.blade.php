<x-layouts.app title="{{ __('Заказ') }} {{ $order->public_order_number }} | Buyer Greece">
    <div class="max-w-4xl mx-auto space-y-6">

        <!-- HEADER & BACK BUTTON -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1 app-text-sub hover:text-[#1d1d1f] transition-colors mb-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    {{ __('Назад к заказам') }}
                </a>
                <div class="flex items-center gap-3">
                    <h1 class="apple-section-title">
                        {{ __('Заказ №') }} {{ $order->public_order_number }}
                    </h1>
                    <span class="px-3 py-1 app-text-sub font-medium rounded-full bg-amber-100/80 text-amber-900 border border-amber-200/60">
                        {{ is_object($order->status) ? $order->status->value : $order->status }}
                    </span>
                </div>
            </div>
            
            <div class="app-text-sub">
                {{ __('Создан:') }} {{ $order->created_at ? $order->created_at->format('d.m.Y H:i') : now()->format('d.m.Y') }}
            </div>
        </div>

        <!-- SECTION 1: ORDER ITEMS -->
        <div class="apple-card space-y-4">
            <h2 class="app-text-title border-b border-black/[0.06] pb-3">{{ __('Товары в заказе') }}</h2>

            @forelse($order->items as $item)
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 py-3 border-b border-black/[0.06] last:border-0">
                    <div class="space-y-1">
                        <h3 class="app-text-title">{{ $item->product_name }}</h3>
                        @if($item->product_url)
                            <a href="{{ $item->product_url }}" target="_blank" class="app-text-sub text-[#0071e3] hover:underline inline-flex items-center gap-1">
                                <span>{{ __('Открыть ссылку на товар') }}</span>
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </a>
                        @endif
                        <div class="flex flex-wrap gap-2 app-text-sub pt-1">
                            @if($item->size) <span>{{ __('Размер:') }} <strong class="text-[#1d1d1f]">{{ $item->size }}</strong></span> @endif
                            @if($item->color) <span>{{ __('Цвет:') }} <strong class="text-[#1d1d1f]">{{ $item->color }}</strong></span> @endif
                            @if($item->model) <span>{{ __('Модель:') }} <strong class="text-[#1d1d1f]">{{ $item->model }}</strong></span> @endif
                        </div>
                    </div>

                    <div class="text-right flex sm:flex-col justify-between items-center sm:items-end app-text-sub">
                        <span>{{ __('Количество:') }} <strong>{{ $item->quantity }} {{ __('шт.') }}</strong></span>
                        @if($item->total_price)
                            <span class="app-text-title font-semibold">€{{ number_format($item->total_price, 2) }}</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="app-text-sub py-4 text-center">
                    {{ __('Информация о позициях обновляется менеджером.') }}
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

            <div class="apple-card space-y-6">
                <div class="flex items-center justify-between border-b border-black/[0.06] pb-3">
                    <h2 class="app-text-title">{{ __('Расчет стоимости (Quote)') }}</h2>
                    
                    @if($isAccepted)
                        <span class="app-text-sub text-emerald-700 font-medium bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200/60">
                            {{ __('Расчет принят') }}
                        </span>
                    @elseif($isRejected)
                        <span class="app-text-sub text-rose-700 font-medium bg-rose-50 px-3 py-1 rounded-full border border-rose-200/60">
                            {{ __('Расчет отклонен') }}
                        </span>
                    @else
                        <span class="app-text-sub text-amber-700 font-medium bg-amber-50 px-3 py-1 rounded-full border border-amber-200/60">
                            {{ __('Ожидает вашего подтверждения') }}
                        </span>
                    @endif
                </div>

                <!-- PRICE BREAKDOWN TABLE -->
                <div class="space-y-2 app-text-body text-[#86868b]">
                    <div class="flex justify-between">
                        <span>{{ __('Стоимость товаров:') }}</span>
                        <span class="font-medium text-[#1d1d1f]">€{{ number_format($quote->product_total, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>{{ __('Доставка по Греции:') }}</span>
                        <span class="font-medium text-[#1d1d1f]">€{{ number_format($quote->local_shipping, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>{{ __('Комиссия выкупа:') }}</span>
                        <span class="font-medium text-[#1d1d1f]">€{{ number_format($quote->buyer_fee, 2) }}</span>
                    </div>

                    @if($quote->services_total > 0)
                        <div class="flex justify-between">
                            <span>{{ __('Доп. услуги:') }}</span>
                            <span class="font-medium text-[#1d1d1f]">€{{ number_format($quote->services_total, 2) }}</span>
                        </div>
                    @endif

                    @if($quote->discount > 0)
                        <div class="flex justify-between text-emerald-600">
                            <span>{{ __('Скидка:') }}</span>
                            <span class="font-medium">-€{{ number_format($quote->discount, 2) }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between pt-3 border-t border-black/[0.06] app-text-title">
                        <span>{{ __('Итого к оплате:') }}</span>
                        <span class="text-[#0071e3]">€{{ number_format($quote->total, 2) }}</span>
                    </div>
                </div>

                <!-- ACCEPT / REJECT BUTTONS -->
                @if(!$isAccepted && !$isRejected)
                    <div class="p-4 bg-[#f5f5f7] border border-black/[0.04] rounded-2xl space-y-3">
                        <p class="app-text-sub">
                            {{ __('Пожалуйста, проверьте итоговую стоимость. После принятия расчета станет доступна форма загрузки чека оплаты.') }}
                        </p>
                        
                        <div class="flex flex-col sm:flex-row gap-3 pt-1">
                            <form action="{{ route('orders.quotes.accept', [$order->id, $quote->id]) }}" method="POST" class="w-full sm:w-auto">
                                @csrf
                                <button type="submit" class="apple-btn-primary w-full text-center">
                                    {{ __('Принять расчет и перейти к оплате') }}
                                </button>
                            </form>

                            <form action="{{ route('orders.quotes.reject', [$order->id, $quote->id]) }}" method="POST" class="w-full sm:w-auto">
                                @csrf
                                <button type="submit" class="w-full px-5 py-3 app-text-sub font-medium text-rose-700 bg-rose-50 hover:bg-rose-100/70 border border-rose-200/60 rounded-full transition-colors text-center cursor-pointer">
                                    {{ __('Отклонить расчет') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

                <!-- BANK TRANSFER PAYMENT FORM -->
                @if($isAccepted || $order->active_quote_id === $quote->id)
                    <div class="bg-[#f5f5f7] border border-black/[0.04] rounded-2xl p-5 space-y-4">
                        <h3 class="app-text-title">{{ __('Оплата банковским переводом (Bank Transfer)') }}</h3>
                        <p class="app-text-sub leading-relaxed">
                            {{ __('Переведите итоговую сумму') }} <strong>€{{ number_format($quote->total, 2) }}</strong> {{ __('на банковские реквизиты сервиса и загрузите квитанцию об оплате ниже.') }}
                        </p>

                        <!-- REQUISITES -->
                        <div class="space-y-3 pt-1">
                            <x-forms.copy-field 
                                label="{{ __('IBAN / Расчетный счет') }}" 
                                value="{{ config('services.bank.iban', 'GR1234567890123456789012345') }}" 
                            />
                            <x-forms.copy-field 
                                label="{{ __('Сумма к оплате (€)') }}" 
                                value="{{ number_format($quote->total, 2, '.', '') }}" 
                            />
                            <x-forms.copy-field 
                                label="{{ __('Назначение платежа') }}" 
                                value="{{ __('Оплата заказа №') }} {{ $order->public_order_number }}" 
                            />
                        </div>

                        <form action="{{ route('orders.payments.store', $order->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4 pt-3 border-t border-black/[0.06]">
                            @csrf
                            <div>
                                <label for="receipt" class="block app-text-sub font-semibold mb-1.5">
                                    {{ __('Квитанция / Чек оплаты (PDF, JPG, PNG до 10MB)') }} <span class="text-rose-500">*</span>
                                </label>
                                <input 
                                    type="file" 
                                    name="receipt" 
                                    id="receipt" 
                                    accept="image/*,.pdf" 
                                    required 
                                    class="block w-full app-text-sub file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:app-text-sub file:font-semibold file:bg-black/[0.05] file:text-[#1d1d1f] hover:file:bg-black/[0.1] cursor-pointer"
                                >
                                @error('receipt')
                                    <p class="app-text-sub text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit" class="apple-btn-primary">
                                {{ __('Отправить чек на проверку') }}
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        @else
            <div class="apple-card bg-blue-50/70 border-blue-200/60 text-center space-y-2">
                <h3 class="app-text-title text-blue-900">{{ __('Расчет готовится менеджером') }}</h3>
                <p class="app-text-sub text-blue-800">
                    {{ __('Мы проверяем наличие товара в греческом магазине. Когда расчет будет готов, здесь появится итоговая сумма и кнопка подтверждения.') }}
                </p>
            </div>
        @endif

       

    </div>
</x-layouts.app>