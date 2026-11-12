@props(['product', 'isFirst' => false])

@php
    if ($product instanceof \Illuminate\Database\Eloquent\Model) {
        $card = method_exists($product, 'toCard') ? $product->toCard() : $product->toArray();
    } else {
        $card = (array) $product;
    }

    $name = $card['title'] ?? $card['name'] ?? '';
    $imgUrl = $card['image_url'] ?? $card['image'] ?? '';
    $price = $card['price'] ?? $card['price_amount'] ?? 0;
    $oldPrice = $card['old_price'] ?? null;
    $discount = (int) ($card['discount_percent'] ?? 0);
    $colour = $card['colour'] ?? $card['color'] ?? $card['attributes']['colour'] ?? $card['attributes']['color'] ?? null;
    $detailUrl = $card['detail_url'] ?? $card['product_url'] ?? '#';
    $brand = $card['brand'] ?? null;
@endphp

<div class="adidas-product-card group relative">
    
    <a href="{{ $detailUrl }}" class="absolute inset-0 z-10" aria-label="{{ $name }}"></a>

    <div class="adidas-product-top">
        @if(!empty($imgUrl))
            <img src="{{ $imgUrl }}"
                 alt="{{ $name }}"
                 loading="{{ $isFirst ? 'eager' : 'lazy' }}"
                 decoding="async"
                 class="max-h-full max-w-full object-contain group-hover:scale-105 transition-transform duration-500">
        @endif
        @if($discount > 0)
            <span class="absolute top-3 left-3 z-20 bg-red-600 text-white text-xs font-bold px-2.5 py-1 rounded-full shadow">
                -{{ $discount }}%
            </span>
        @endif
    </div>

    <div class="adidas-product-bottom">
        <div class="space-y-1 mb-3">
            @if($brand)
                <div class="text-xs font-bold text-zinc-400 uppercase tracking-wider">{{ $brand }}</div>
            @endif
            <h3 class="font-semibold text-zinc-900 text-sm line-clamp-2">
                {{ $name }}
            </h3>
            
            @if(!empty($colour))
                <div class="pt-1">
                    <x-public-ui.color-dots :colour="$colour" :only-dots="true" />
                </div>
            @endif
        </div>

        <div class="flex items-end justify-between pt-3 border-t border-zinc-100">
            <div>
                <div class="text-lg font-bold text-zinc-900">€{{ number_format((float)$price, 2) }}</div>
                @if(!empty($oldPrice))
                    <div class="text-xs text-zinc-400 line-through">€{{ number_format((float)$oldPrice, 2) }}</div>
                @endif
            </div>

            <div class="relative z-20">
                <a href="{{ $detailUrl }}" class="adidas-card-action-btn" title="{{ __('Открыть') }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>