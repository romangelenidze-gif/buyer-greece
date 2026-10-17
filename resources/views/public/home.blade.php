<x-layouts.public 
    title="{{ __('Buyer Greece → Georgia | Выкуп и доставка товаров из Греции') }}"
    description="{{ __('Сервис выкупа и приема покупок в интернет-магазинах Греции с последующей доставкой в Грузию через Camex.') }}"
>

    <!-- HERO SECTION -->
    <x-public-ui.hero />

    <!-- POPULAR PRODUCTS SHOWCASE -->
    <x-public-ui.showcase-slider />

    <!-- BENTO GRID SCENARIOS -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <h2 class="apple-section-title">{{ __('3 формата работы') }}</h2>
                <p class="apple-section-subtitle">{{ __('Выберите подходящий вариант сервиса') }}</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <x-public-ui.bento-card 
                    step="01"
                    :title="__('Выкупите за меня')"
                    :description="__('Отправьте ссылку на товар. Мы согласуем точную калькуляцию (Quote) и организуем выкуп.')"
                    :linkUrl="route('buy-for-me')"
                    :linkText="__('Отправить ссылку')"
                />

                <x-public-ui.bento-card 
                    step="02"
                    :title="__('Помогите купить')"
                    :description="__('Нужна консультация по выбору продавца или подбору размера? Менеджеры помогут в чате.')"
                    :linkUrl="route('help')"
                    :linkText="__('Задать вопрос')"
                />

                <x-public-ui.bento-card 
                    step="03"
                    :title="__('Форвардинг')"
                    :description="__('Оплачивайте покупки самостоятельно и отправляйте их на наш адрес склада в Греции.')"
                    :linkUrl="route('forwarding')"
                    :linkText="__('Получить адрес')"
                />
            </div>
        </div>
    </section>

</x-layouts.public>