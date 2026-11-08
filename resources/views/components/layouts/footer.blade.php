@php
    $sections = [
        [
            'title' => __('Услуги'),
            'links' => [
                ['route' => 'buy-for-me', 'label' => __('Выкупите за меня')],
                ['route' => 'forwarding', 'label' => __('Форвардинг')],
            ],
        ],
        [
            'title' => __('Информация'),
            'links' => [
                ['route' => 'how-it-works', 'label' => __('Как это работает')],
                ['route' => 'help',         'label' => __('Помощь / FAQ')],
                ['route' => 'help',         'label' => __('Условия обслуживания')],
                ['route' => 'help',         'label' => __('Политика конфиденциальности')],
            ],
        ],
    ];
@endphp

<footer class="apple-footer">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="apple-footer-grid">

            {{-- Колонка 1: о бренде --}}
            <div>
                <div class="apple-footer-brand">exereto</div>
                <p class="apple-footer-text">
                    {{ __('Интеллектуальная платформа поиска и заказа товаров из греческих каталогов и интернет-магазинов.') }}
                </p>
            </div>

            {{-- Колонки 2 и 3: услуги и информация --}}
            @foreach ($sections as $section)
                <div>
                    <h4 class="apple-footer-title">{{ $section['title'] }}</h4>
                    <ul class="apple-footer-list">
                        @foreach ($section['links'] as $link)
                            <li><a href="{{ route($link['route']) }}" class="apple-footer-link">{{ $link['label'] }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endforeach

        </div>

        <div class="apple-footer-bottom">
            <div>© {{ date('Y') }} exereto. {{ __('Все права защищены.') }}</div>
        </div>
    </div>
</footer>