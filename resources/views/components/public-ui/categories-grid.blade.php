@props(['categories' => []])

<section class="w-full px-4 sm:px-6 lg:px-10 py-2">
    <!-- Шапка секции с подзаголовком -->
    <div class="text-center mb-8 max-w-3xl mx-auto px-4">
        <h2 class="apple-section-title text-2xl sm:text-3xl mb-2">
            {{ __('Популярные категории') }}
        </h2>
        <p class="app-text-body text-[#515154] text-sm sm:text-base leading-relaxed">
            <span>{{ __('Найдите нужный товар в каталоге BestPrice.gr — крупнейшем греческом каталоге, объединяющем 3 741 магазин и 27 644 929 товаров. Откройте страницу понравившегося товара и отправьте нам ссылку.') }}</span>
            
            <!-- Вызов изолированного компонента тултипа -->
            <x-public-ui.nav-tooltip />
        </p>
    </div>

    <!-- Блок во всю ширину экрана (w-full) с 10 крупными колонками -->
    <div class="grid grid-cols-2 sm:grid-cols-5 lg:grid-cols-10 gap-3 sm:gap-4">
        @foreach($categories as $cat)
            <a href="{{ $cat['url'] ?? '#' }}" 
               target="_blank" 
               rel="noopener noreferrer"
               class="group bg-[#ffffff] shadow-sm hover:bg-white border border-black/[0.04] rounded-2xl p-3 sm:p-4 flex flex-col items-center justify-between text-center transition-all duration-300 hover:shadow-lg hover:-translate-y-1 h-44 sm:h-48">
                <div class="w-full h-28 sm:h-32 flex items-center justify-center overflow-hidden">
                    <img src="{{ asset('images/categories/' . $cat['img']) }}" 
                         alt="{{ $cat['name'] }}" 
                         class="max-h-full max-w-full object-contain group-hover:scale-105 transition-transform duration-300">
                </div>
                <span class="text-xs sm:text-sm font-medium text-[#1d1d1f] tracking-tight line-clamp-1 mt-2">
                    {{ __($cat['name']) }}
                </span>
            </a>
        @endforeach
    </div>
</section>