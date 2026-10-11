<x-layouts.public 
    title="{{ __('Выкупите за меня') }} | Buyer Greece"
    description="{{ __('Отправьте ссылку на товар из любого интернет-магазина Греции, и мы рассчитаем точную стоимость перед покупкой.') }}"
>
    <div class="py-12 bg-[#f2f2f2] min-h-[calc(100vh-4rem)]">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- HEADER SECTION -->
            <div class="text-center mb-8 space-y-2">
                <h1 class="apple-section-title">
                    {{ __('Пришлите ссылку на товар, который хотите купить в Греции') }}
                </h1>
                <p class="apple-section-subtitle">
                    {{ __('Мы проверим наличие товара и подготовим точный расчет (Quote) перед вашей оплатой.') }}
                </p>
                
                <div class="mt-4 p-4 bg-blue-50/70 border border-blue-200/60 rounded-2xl text-xs text-blue-900 flex items-center gap-3 text-left">
                    <svg class="w-5 h-5 text-[#0071e3] flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{!! __('Международная доставка в Грузию оплачивается отдельно транспортной компании Camex при получении.') !!}</span>
                </div>
            </div>

            <!-- SUCCESS STATE -->
            @if (session('order_created'))
                <div class="apple-card text-center space-y-4 mb-8">
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mx-auto">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold text-[#1d1d1f]">{{ __('Заявка принята!') }}</h2>
                    <p class="text-sm text-[#86868b]">
                        {{ __('Номер вашего заказа:') }} <strong class="text-[#1d1d1f] font-mono">{{ session('order_number') }}</strong>. 
                        {{ __('Мы уже проверяем товар и скоро подготовим расчет стоимости.') }}
                    </p>
                    <div class="pt-2 flex flex-col sm:flex-row gap-3 justify-center">
                        <x-buttons.primary href="{{ route('orders.show', session('order_id')) }}">
                            {{ __('Посмотреть заказ') }}
                        </x-buttons.primary>
                        <a href="{{ route('dashboard') }}" class="px-6 py-3 bg-black/[0.05] hover:bg-black/[0.1] text-[#1d1d1f] font-medium text-sm rounded-full transition-all duration-300">
                            {{ __('В личный кабинет') }}
                        </a>
                    </div>
                </div>
            @else

                <!-- GUEST NOTICE -->
                @guest
                    <div class="mb-6 p-4 bg-amber-50/70 border border-amber-200/60 rounded-2xl text-xs text-amber-900 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div>
                            <strong>{{ __('Требуется авторизация:') }}</strong> {{ __('Чтобы отправить заявку и отслеживать её статус, войдите в аккаунт или зарегистрируйтесь.') }}
                        </div>
                        <div class="flex gap-2 w-full sm:w-auto">
                            <a href="{{ route('login') }}" class="px-4 py-1.5 bg-[#0071e3] text-white text-xs font-medium rounded-full">{{ __('Войти') }}</a>
                            <a href="{{ route('register') }}" class="px-4 py-1.5 bg-black/[0.05] text-[#1d1d1f] text-xs font-medium rounded-full">{{ __('Регистрация') }}</a>
                        </div>
                    </div>
                @endguest

                <!-- ORDER FORM -->
                <form action="{{ route('orders.store') }}" method="POST" enctype="multipart/form-data" class="apple-card space-y-6">
                    @csrf

                    <!-- SECTION 1: PRODUCT INFO -->
                    <div>
                        <h2 class="text-lg font-semibold text-[#1d1d1f] border-b border-black/[0.06] pb-3 mb-4">{{ __('Информация о товаре') }}</h2>
                        
                        <div class="space-y-4">
                            <!-- Product URL -->
                            <div>
                                <label for="product_url" class="block text-xs font-semibold text-[#1d1d1f] mb-1.5">
                                    {{ __('Ссылка на товар (URL)') }} <span class="text-rose-500">*</span>
                                </label>
                                <input 
                                    type="url" 
                                    name="product_url" 
                                    id="product_url" 
                                    value="{{ old('product_url', request('url')) }}"
                                    required 
                                    placeholder="https://www.zara.com/gr/..." 
                                    class="apple-search-input !px-4 !py-3 !rounded-2xl"
                                >
                                @error('product_url')
                                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Product Name & Quantity -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="sm:col-span-2">
                                    <label for="product_name" class="block text-xs font-semibold text-[#1d1d1f] mb-1.5">
                                        {{ __('Название товара') }} <span class="text-rose-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        name="product_name" 
                                        id="product_name" 
                                        value="{{ old('product_name') }}"
                                        required 
                                        placeholder="{{ __('например: Leather Jacket') }}" 
                                        class="apple-search-input !px-4 !py-3 !rounded-2xl"
                                    >
                                    @error('product_name')
                                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="quantity" class="block text-xs font-semibold text-[#1d1d1f] mb-1.5">
                                        {{ __('Количество') }} <span class="text-rose-500">*</span>
                                    </label>
                                    <input 
                                        type="number" 
                                        name="quantity" 
                                        id="quantity" 
                                        value="{{ old('quantity', 1) }}"
                                        min="1" 
                                        required 
                                        class="apple-search-input !px-4 !py-3 !rounded-2xl"
                                    >
                                    @error('quantity')
                                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Optional Attributes -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                                <div>
                                    <label for="size" class="block text-xs font-medium text-[#86868b] mb-1.5">{{ __('Размер (необязательно)') }}</label>
                                    <input type="text" name="size" id="size" value="{{ old('size') }}" placeholder="M, L, 42..." class="apple-search-input !px-4 !py-3 !rounded-2xl">
                                </div>
                                <div>
                                    <label for="color" class="block text-xs font-medium text-[#86868b] mb-1.5">{{ __('Цвет (необязательно)') }}</label>
                                    <input type="text" name="color" id="color" value="{{ old('color') }}" placeholder="Black, Blue..." class="apple-search-input !px-4 !py-3 !rounded-2xl">
                                </div>
                                <div>
                                    <label for="model" class="block text-xs font-medium text-[#86868b] mb-1.5">{{ __('Модель / Артикул') }}</label>
                                    <input type="text" name="model" id="model" value="{{ old('model') }}" placeholder="Art. 12345" class="apple-search-input !px-4 !py-3 !rounded-2xl">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2: ADDITIONAL INFO -->
                    <div>
                        <h2 class="text-lg font-semibold text-[#1d1d1f] border-b border-black/[0.06] pb-3 mb-4">{{ __('Дополнительно') }}</h2>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="comment" class="block text-xs font-medium text-[#86868b] mb-1.5">{{ __('Комментарий к заказу') }}</label>
                                <textarea name="comment" id="comment" rows="3" placeholder="{{ __('Укажите пожелания по проверке, альтернативные размеры или промокоды...') }}" class="apple-search-input !rounded-2xl !px-4 !py-3">{{ old('comment') }}</textarea>
                            </div>

                            <div>
                                <label for="screenshot" class="block text-xs font-medium text-[#86868b] mb-1.5">{{ __('Скриншот или изображение (JPG, PNG, PDF до 5MB)') }}</label>
                                <input type="file" name="screenshot" id="screenshot" accept="image/*,.pdf" class="block w-full text-xs text-[#86868b] file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-black/[0.05] file:text-[#1d1d1f] hover:file:bg-black/[0.1]">
                                @error('screenshot')
                                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- SUBMIT BUTTON AREA -->
                    <div class="pt-4 border-t border-black/[0.06] flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-xs text-[#86868b]">
                            {{ __('Нажимая кнопку, вы отправляете товар на бесплатную проверку менеджером.') }}
                        </div>

                        <button type="submit" @guest disabled @endguest class="apple-search-button !relative !right-auto !top-auto !transform-none w-full sm:w-auto px-8 py-3 text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                            {{ __('Отправить заявку') }}
                        </button>
                    </div>

                </form>

                <!-- ASSISTANCE BOX -->
                <div class="mt-6 p-4 bg-[#f5f5f7] border border-black/[0.04] rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-[#86868b]">
                    <span>{{ __('Затрудняетесь с заполнением ссылки или подбором товара?') }}</span>
                    <div class="flex gap-2">
                        <x-messenger type="telegram" size="small" />
                        <x-messenger type="whatsapp" size="small" />
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-layouts.public>