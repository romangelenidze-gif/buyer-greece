<x-layouts.public 
    title="Выкупите за меня | Buyer Greece"
    description="Отправьте ссылку на товар из любого интернет-магазина Греции, и мы рассчитаем точную стоимость перед покупкой."
>
    <div class="py-12 bg-slate-50 min-h-[calc(100vh-4rem)]">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- HEADER SECTION -->
            <div class="text-center mb-8">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Пришліте ссылку на товар, который хотите купить в Греции
                </h1>
                <p class="text-sm sm:text-base text-slate-600 mt-2">
                    Мы проверим наличие товара и подготовим точный расчет (Quote) перед вашей оплатой.
                </p>
                
                <div class="mt-4 inline-flex items-center gap-2 p-3 bg-blue-50 border border-blue-200 rounded-lg text-xs text-blue-800 text-left">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Международная доставка в Грузию оплачивается отдельно транспортной компании <strong>Camex</strong> при получении.</span>
                </div>
            </div>

            <!-- SUCCESS STATE (Показывается при успешном создании заказа бэкендом) -->
            @if (session('order_created'))
                <div class="bg-white border border-emerald-200 rounded-2xl p-6 shadow-sm text-center space-y-4 mb-8">
                    <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-slate-900">Заявка принята!</h2>
                    <p class="text-sm text-slate-600">
                        Номер вашего заказа: <strong class="text-slate-900 font-mono">{{ session('order_number') }}</strong>. 
                        Мы уже проверяем товар и скоро подготовим расчет стоимости.
                    </p>
                    <div class="pt-2 flex flex-col sm:flex-row gap-3 justify-center">
                        <x-buttons.primary href="{{ route('orders.show', session('order_id')) }}">
                            Посмотреть заказ
                        </x-buttons.primary>
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg text-center transition-colors">
                            В личный кабинет
                        </a>
                    </div>
                </div>
            @else

                <!-- GUEST NOTICE (Если пользователь не вошел) -->
                @guest
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-xs text-amber-900">
                            <strong>Требуется авторизация:</strong> Чтобы отправить заявку и отслеживать её статус, вам необходимо войти в аккаунт или зарегистрироваться.
                        </div>
                        <div class="flex gap-2 flex-shrink-0 w-full sm:w-auto">
                            <a href="{{ route('login') }}" class="w-1/2 sm:w-auto text-center px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white font-semibold text-xs rounded-md transition-colors">
                                Войти
                            </a>
                            <a href="{{ route('register') }}" class="w-1/2 sm:w-auto text-center px-3 py-1.5 bg-white border border-amber-300 text-amber-900 font-semibold text-xs rounded-md hover:bg-amber-100 transition-colors">
                                Регистрация
                            </a>
                        </div>
                    </div>
                @endguest

                <!-- ORDER FORM -->
                <form action="{{ route('orders.store') }}" method="POST" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-sm space-y-6">
                    @csrf

                    <!-- SECTION 1: PRODUCT INFO -->
                    <div>
                        <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-2 mb-4">Информация о товаре</h2>
                        
                        <div class="space-y-4">
                            <!-- Product URL -->
                            <div>
                                <label for="product_url" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Ссылка на товар (URL) <span class="text-rose-500">*</span>
                                </label>
                                <input 
                                    type="url" 
                                    name="product_url" 
                                    id="product_url" 
                                    value="{{ old('product_url') }}"
                                    required 
                                    placeholder="https://www.zara.com/gr/..." 
                                    class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500"
                                >
                                @error('product_url')
                                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Product Name & Quantity -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="sm:col-span-2">
                                    <label for="product_name" class="block text-xs font-semibold text-slate-700 mb-1">
                                        Название товара <span class="text-rose-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        name="product_name" 
                                        id="product_name" 
                                        value="{{ old('product_name') }}"
                                        required 
                                        placeholder="например: Leather Jacket" 
                                        class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500"
                                    >
                                    @error('product_name')
                                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="quantity" class="block text-xs font-semibold text-slate-700 mb-1">
                                        Количество <span class="text-rose-500">*</span>
                                    </label>
                                    <input 
                                        type="number" 
                                        name="quantity" 
                                        id="quantity" 
                                        value="{{ old('quantity', 1) }}"
                                        min="1" 
                                        required 
                                        class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500"
                                    >
                                    @error('quantity')
                                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Optional Attributes: Size, Color, Model -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                                <div>
                                    <label for="size" class="block text-xs font-medium text-slate-600 mb-1">Размер (необязательно)</label>
                                    <input type="text" name="size" id="size" value="{{ old('size') }}" placeholder="M, L, 42..." class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                                </div>
                                <div>
                                    <label for="color" class="block text-xs font-medium text-slate-600 mb-1">Цвет (необязательно)</label>
                                    <input type="text" name="color" id="color" value="{{ old('color') }}" placeholder="Black, Blue..." class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                                </div>
                                <div>
                                    <label for="model" class="block text-xs font-medium text-slate-600 mb-1">Модель / Артикул</label>
                                    <input type="text" name="model" id="model" value="{{ old('model') }}" placeholder="Art. 12345" class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2: ADDITIONAL INFO -->
                    <div>
                        <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-2 mb-4">Дополнительно</h2>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="comment" class="block text-xs font-medium text-slate-600 mb-1">Комментарий к заказу</label>
                                <textarea name="comment" id="comment" rows="3" placeholder="Укажите пожелания по проверке, альтернативные размеры или промокоды..." class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">{{ old('comment') }}</textarea>
                            </div>

                            <div>
                                <label for="screenshot" class="block text-xs font-medium text-slate-600 mb-1">Скриншот или изображение (JPG, PNG, PDF до 5MB)</label>
                                <input type="file" name="screenshot" id="screenshot" accept="image/*,.pdf" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                                @error('screenshot')
                                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- SUBMIT BUTTON AREA -->
                    <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-xs text-slate-500">
                            Нажимая кнопку, вы отправляете товар на бесплатную проверку менеджером.
                        </div>

                        <x-buttons.primary type="submit" size="large" :disabled="Auth::guest()" class="w-full sm:w-auto">
                            Отправить заявку
                        </x-buttons.primary>
                    </div>

                </form>
            @endif

        </div>
    </div>
</x-layouts.public>