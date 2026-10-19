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
@endphp

<div class="apple-product-card group">
    
    <!-- Верхний белый блок под фото -->
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

        <!-- Бейдж магазина и внешняя ссылка -->
        <div class="absolute inset-x-0 top-0 p-3.5 flex items-center justify-between z-10 pointer-events-none">
            <span class="inline-flex items-center px-2.5 py-1 rounded-full app-badge-text bg-white/80 backdrop-blur-md text-[#1d1d1f] shadow-2xs pointer-events-auto">
                {{ $product['merchant_name'] ?? __('Греция') }}
            </span>
            <a href="{{ $product['product_url'] ?? '#' }}" target="_blank" rel="noopener noreferrer" 
               title="{{ __('Перейти в магазин') }}"
               class="w-8 h-8 rounded-full bg-white/80 backdrop-blur-md flex items-center justify-center text-[#1d1d1f] shadow-2xs hover:bg-white transition-colors pointer-events-auto">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
            </a>
        </div>
    </div>

    <!-- Текстовый блок внизу на серой плашке -->
    <div class="apple-product-card-bottom">
        
        <!-- Левая колонка: Название, рейтинг, описание -->
        <div class="space-y-1.5 flex-1 pr-1 min-w-0">
            <h3 class="apple-product-title">
                {{ $product['title'] ?? '' }}
            </h3>
            
            <div class="apple-product-rating">
                <span class="apple-product-stars">★★★★☆</span>
                <span>({{ $product['offers_count'] ?? 1 }})</span>
            </div>

            <p class="apple-product-desc">
                {{ $product['short_description'] ?? '' }}
            </p>
        </div>

        <!-- Правая колонка: Цена и синяя кнопка -->
        <div class="flex flex-col items-end justify-between space-y-3 shrink-0">
            <div class="text-right">
                <div class="apple-product-price">
                    @if(!empty($product['price_amount']))
                        €{{ number_format($product['price_amount'], 2) }}
                    @else
                        <span class="apple-product-price-note">{{ $product['price_note'] ?? __('Уточняется') }}</span>
                    @endif
                </div>
            </div>

            <div class="flex flex-col space-y-2">
                <a href="{{ route('buy-for-me') }}?url={{ urlencode($product['product_url'] ?? '') }}" 
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