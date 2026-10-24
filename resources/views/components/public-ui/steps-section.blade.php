<section class="py-20 bg-gradient-to-b from-white via-gray-50/60 to-white relative overflow-hidden">
    <!-- Декоративные фоновые элементы в стиле иллюстраций -->
    <div class="absolute top-10 left-10 w-72 h-72 bg-[#00A896]/[0.03] rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-10 right-10 w-96 h-96 bg-blue-500/[0.03] rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        <!-- Заголовок и описание секции -->
        <div class="max-w-2xl text-left mb-16 space-y-3">
            <h2 class="apple-section-title">
                {{ __('Как ваш заказ добирается до Грузии') }}
            </h2>
            <p class="apple-section-subtitle">
                {{ __('Прозрачный и простой процесс — от поиска товара до передачи Camex.') }}
            </p>
        </div>

        <!-- 5 ШАГОВ С ИЛЛЮСТРАЦИЯМИ И НАШИМИ ТЕКСТОВЫМИ КЛАССАМИ -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6 items-stretch">
            
            <!-- ШАГ 1: Запрос -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:border-[#00A896]/30 transition-all duration-500 flex flex-col justify-between group relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-[#00A896]/10 to-transparent rounded-bl-full pointer-events-none"></div>
                
                <div>
                    <!-- Иллюстрация / Иконка шага -->
                    <div class="w-16 h-16 rounded-2xl bg-[#00A896]/10 flex items-center justify-center text-[#00A896] mb-6 group-hover:scale-110 transition-transform duration-500 shadow-inner">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>

                    <span class="text-xs font-bold text-[#00A896] uppercase tracking-wider">Шаг 1</span>
                    <h3 class="apple-step-title mt-1 mb-2">
                        {{ __('Вы оставляете запрос') }}
                    </h3>
                    <p class="apple-step-description">
                        {{ __('Находите товар или описываете потребность на любом языке.') }}
                    </p>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-50 flex items-center text-xs font-medium text-[#00A896]">
                    <span>Старт процесса</span>
                </div>
            </div>

            <!-- ШАГ 2: ИИ подбор -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:border-indigo-500/30 transition-all duration-500 flex flex-col justify-between group relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-indigo-500/10 to-transparent rounded-bl-full pointer-events-none"></div>
                
                <div>
                    <div class="w-16 h-16 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 mb-6 group-hover:scale-110 transition-transform duration-500 shadow-inner">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                    </div>

                    <span class="text-xs font-bold text-indigo-600 uppercase tracking-wider">Шаг 2</span>
                    <h3 class="apple-step-title mt-1 mb-2">
                        {{ __('Мы подбираем лучшие варианты') }}
                    </h3>
                    <p class="apple-step-description">
                        {{ __('ИИ анализирует запрос, переводит и расширяет его ключевыми словами.') }}
                    </p>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-50 flex items-center text-xs font-medium text-indigo-600">
                    <span>Умный анализ ИИ</span>
                </div>
            </div>

            <!-- ШАГ 3: Покупка -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:border-amber-500/30 transition-all duration-500 flex flex-col justify-between group relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-amber-500/10 to-transparent rounded-bl-full pointer-events-none"></div>
                
                <div>
                    <div class="w-16 h-16 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600 mb-6 group-hover:scale-110 transition-transform duration-500 shadow-inner">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>

                    <span class="text-xs font-bold text-amber-600 uppercase tracking-wider">Шаг 3</span>
                    <h3 class="apple-step-title mt-1 mb-2">
                        {{ __('Покупаем в Греции') }}
                    </h3>
                    <p class="apple-step-description">
                        {{ __('Находим товар в магазинах, проверяем наличие и стоимость.') }}
                    </p>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-50 flex items-center text-xs font-medium text-amber-600">
                    <span>Выкуп товаров</span>
                </div>
            </div>

            <!-- ШАГ 4: Упаковка -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:border-blue-500/30 transition-all duration-500 flex flex-col justify-between group relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-blue-500/10 to-transparent rounded-bl-full pointer-events-none"></div>
                
                <div>
                    <div class="w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600 mb-6 group-hover:scale-110 transition-transform duration-500 shadow-inner">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>

                    <span class="text-xs font-bold text-blue-600 uppercase tracking-wider">Шаг 4</span>
                    <h3 class="apple-step-title mt-1 mb-2">
                        {{ __('Принимаем и отправляем') }}
                    </h3>
                    <p class="apple-step-description">
                        {{ __('Упаковываем, формируем посылку и передаём Camex.') }}
                    </p>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-50 flex items-center text-xs font-medium text-blue-600">
                    <span>Склад в Греции</span>
                </div>
            </div>

            <!-- ШАГ 5: Доставка в Грузию -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:border-sky-500/30 transition-all duration-500 flex flex-col justify-between group relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-sky-500/10 to-transparent rounded-bl-full pointer-events-none"></div>
                
                <div>
                    <div class="w-16 h-16 rounded-2xl bg-sky-50 flex items-center justify-center text-sky-600 mb-6 group-hover:scale-110 transition-transform duration-500 shadow-inner">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </div>

                    <span class="text-xs font-bold text-sky-600 uppercase tracking-wider">Шаг 5</span>
                    <h3 class="apple-step-title mt-1 mb-2">
                        {{ __('Доставка в Грузию') }}
                    </h3>
                    <p class="apple-step-description">
                        {{ __('Camex доставляет ваш заказ в Грузию до двери.') }}
                    </p>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-50 flex items-center text-xs font-medium text-sky-600">
                    <span>Получение дома</span>
                </div>
            </div>

        </div>

    </div>
</section>