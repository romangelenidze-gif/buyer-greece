@props(['product'])

<div class="group relative bg-[#f5f5f7] rounded-3xl p-6 flex flex-col justify-between h-full transition-all duration-300 hover:shadow-xl hover:-translate-y-1 border border-black/[0.04]">
    <div>
        <!-- Бейдж скидки -->
        @if($product->discount_percent)
            <div class="absolute top-5 left-5 z-10">
                <span class="inline-block bg-[#ff3b30] text-white text-[11px] font-semibold px-3 py-1 rounded-full shadow-sm tracking-wide">
                    -{{ $product->discount_percent }}%
                </span>
            </div>
        @endif

        <!-- Фото товара в белом блоке с эффектом приближения -->
        <div class="relative h-80 mb-5 rounded-2xl overflow-hidden bg-white flex items-center justify-center p-4 shadow-2xs">
            <img src="{{ $product->image_url ?? asset('storage/' . $product->image_path) }}" 
                 alt="{{ $product->name }}" 
                 class="w-full h-full object-contain object-center transition-transform duration-500 group-hover:scale-105">
        </div>

        <!-- Магазин / Партнер -->
        <div class="flex items-center justify-between mb-1.5">
            <span class="text-[11px] font-bold text-[#86868b] uppercase tracking-wider">{{ $product->partner_name }}</span>
            
        </div>
        
        <!-- Название товара -->
        <h3 class="text-base font-semibold text-[#1d1d1f] line-clamp-1 tracking-tight group-hover:text-[#0071e3] transition-colors">
            {{ $product->name }}
        </h3>
        
        <!-- Описание -->
        <p class="text-xs text-[#86868b] mt-1 line-clamp-2 leading-relaxed">
            {{ $product->short_description }}
        </p>
    </div>

    <!-- Цена и действия -->
    <div class="mt-6 pt-4 border-t border-black/[0.06]">
        <div class="flex items-baseline justify-between mb-4">
            <div class="flex items-baseline gap-2">
                @if($product->price)
                    <span class="text-xl font-bold text-[#1d1d1f] tracking-tight">€{{ number_format($product->price, 2) }}</span>
                @endif
                @if($product->old_price)
                    <span class="text-xs text-[#86868b] line-through font-medium">€{{ number_format($product->old_price, 2) }}</span>
                @endif
            </div>
            <span class="text-[10px] text-[#86868b] font-medium">Цена уточняется</span>
        </div>

        <div class="grid grid-cols-2 gap-2.5">
            <a href="{{ $product->partner_product_url }}" target="_blank" rel="noopener noreferrer" 
               class="inline-flex justify-center items-center px-3 py-2.5 text-xs font-medium text-[#1d1d1f] bg-white rounded-xl hover:bg-[#e8e8ed] transition shadow-2xs">
                {{ __('В магазин') }}
            <a href="{{ route('buy-for-me') }}?showcase_product_id={{ $product->id }}" 
                class="inline-flex justify-center items-center py-2.5 px-3 text-xs font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition shadow-md hover:shadow-blue-500/25">
                    {{ __('В заказ') }}
                </a>
        </div>
    </div>
</div>