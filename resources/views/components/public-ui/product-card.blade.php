@props(['product'])

@php
    $rawImg = $product['image_url'] ?? $product['image_path'] ?? $product['image'] ?? null;
    if (is_array($rawImg)) {
        $rawImg = reset($rawImg);
    }
    
    $imgUrl = null;
    if (!empty($rawImg)) {
        $imgUrl = filter_var($rawImg, FILTER_VALIDATE_URL) 
            ? $rawImg 
            : (\Illuminate\Support\Facades\Storage::disk('public')->exists($rawImg) 
                ? \Illuminate\Support\Facades\Storage::url($rawImg) 
                : asset('storage/' . $rawImg));
    }
    
    $productJson = htmlspecialchars(json_encode($product, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');
@endphp

<div class="apple-product-card group relative">
    
    <!-- Верхний блок под фото (белый закругленный) -->
    <div class="apple-product-card-top">
        <div class="w-full h-full flex items-center justify-center relative">
            @if(!empty($imgUrl))
                <img src="{{ $imgUrl }}" 
                     alt="{{ $product['title'] ?? '' }}" 
                     class="max-h-full max-w-full object-contain object-center group-hover:scale-105 transition-transform duration-700">
            @else
                <div class="flex flex-col items-center justify-center h-full text-[#86868b] space-y-1">
                    <svg class="w-10 h-10 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            @endif
        </div>

        <!-- Кнопки вверху с серым фоном -->
        <div class="absolute inset-x-0 top-0 p-3.5 flex items-center justify-between z-10 pointer-events-none">
            <!-- Левая сторона: Кнопка сравнения -->
            <div class="pointer-events-auto">               
            </div>
            
            <!-- Правая сторона: Ссылка на магазин -->
            <div class="pointer-events-auto">
                <a href="{{ $product['product_url'] ?? '#' }}" target="_blank" rel="noopener noreferrer" 
                   title="{{ __('Перейти в магазин') }}"
                   class="w-8 h-8 rounded-full bg-black/5 hover:bg-black/10 backdrop-blur-md flex items-center justify-center text-[#1d1d1f] transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Нижняя серая плашка -->
    <div class="apple-product-card-bottom flex flex-col justify-between">
        
        <!-- Название товара (до 2 полноценных строк) -->
        <div>
            <h3 class="apple-product-title line-clamp-2 leading-snug">
                {{ $product['title'] ?? '' }}
            </h3>
        </div>

        <!-- Нижняя зона: рейтинг и цена слева, кнопка корзины справа (выровнены по низу) -->
        <div class="w-full flex items-end justify-between mt-3">
            <div class="space-y-1">
                <div class="apple-product-rating">
                    <span class="apple-product-stars">★★★★☆</span>
                    <span>({{ $product['offers_count'] ?? 1 }})</span>
                </div>
                
                <div class="apple-product-price text-left leading-none">
                    @if(!empty($product['price_amount']))
                        €{{ number_format($product['price_amount'], 2) }}
                    @else
                        <span class="apple-product-price-note">{{ $product['price_note'] ?? __('Уточняется') }}</span>
                    @endif
                </div>
            </div>

            <div>
                <a href="{{ route('buy-for-me') }}?url={{ urlencode($product['product_url'] ?? '') }}&name={{ urlencode($product['title'] ?? '') }}&price={{ $product['price_amount'] ?? '' }}"
                   title="{{ __('Оформить заказ') }}"
                   class="apple-product-cart-btn">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </a>
            </div>
        </div>

    </div>
</div>