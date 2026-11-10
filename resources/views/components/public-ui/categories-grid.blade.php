@props(['categories' => []])

<section class="apple-categories">
    <!-- Шапка секции с подзаголовком -->
    <div class="apple-categories-head">
        <h2 class="apple-section-title">
            {{ __('Популярные категории') }}
        </h2>
        <p class="apple-section-subtitle">
            <span>{{ __('Найдите нужный товар в каталоге BestPrice.gr — крупнейшем греческом каталоге, объединяющем 3 741 магазин и 27 644 929 товаров. Откройте страницу понравившегося товара и отправьте нам ссылку.') }}</span>

            <!-- Вызов изолированного компонента тултипа -->
            <x-public-ui.nav-tooltip />
        </p>
    </div>

    <!-- Сетка категорий: все размеры и брейкпоинты в app.css (.apple-categories-grid) -->
    <div class="apple-categories-grid">
        @foreach($categories as $cat)
            <a href="{{ $cat['url'] ?? '#' }}"
               target="_blank"
               rel="noopener noreferrer"
               class="apple-category-card">
                <div class="apple-category-media">
                    <img src="{{ asset('images/categories/' . $cat['img']) }}"
                         alt="{{ $cat['name'] }}"
                         class="apple-category-img">
                </div>
                <span class="apple-category-name">
                    {{ __($cat['name']) }}
                </span>
            </a>
        @endforeach
    </div>
</section>