@props(['product'])

<div class="relative h-[490px] w-full rounded-[36px] overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-500 group flex flex-col justify-between bg-[#f2f1ed] border border-black/[0.04]">
    
    <!-- Background Image (Exact from old working version) -->
    <div class="absolute inset-0 w-full h-full bg-[#f2f1ed]">
        @if(!empty($product['image_url']))
            <img src="{{ filter_var($product['image_url'], FILTER_VALIDATE_URL) ? $product['image_url'] : asset('storage/' . $product['image_url']) }}" 
                 alt="{{ $product['title'] }}" 
                 class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700">
        @else
            <div class="flex flex-col items-center justify-center h-full text-[#86868b] space-y-1">
                <svg class="w-12 h-12 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        @endif
    </div>

    <!-- Soft Bottom Gradient Overlay for readability -->
    <div class="absolute inset-x-0 bottom-0 h-[45%] bg-gradient-to-t from-[#e5e3dd]/95 via-[#f2f1ed]/60 to-transparent pointer-events-none"></div>

    <!-- Top Header Overlay (Merchant & External Link) -->
    <div class="relative z-10 p-5 flex items-center justify-between">
        <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold bg-white/70 backdrop-blur-md text-[#1d1d1f] shadow-2xs uppercase tracking-wider">
            {{ $product['merchant_name'] }}
        </span>
        <a href="{{ $product['product_url'] }}" target="_blank" rel="noopener noreferrer" 
           title="{{ __('Перейти в магазин') }}"
           class="w-9 h-9 rounded-full bg-white/70 backdrop-blur-md flex items-center justify-center text-[#1d1d1f] shadow-2xs hover:bg-white transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
            </svg>
        </a>
    </div>

    <!-- Bottom Content Overlay -->
    <div class="relative z-10 p-6 flex items-end justify-between gap-3">
        
        <!-- Left Column: Title, Rating & Description -->
        <div class="space-y-2 flex-1 pr-2">
            <h3 class="text-base font-bold text-[#1d1d1f] line-clamp-2 leading-snug tracking-tight">
                {{ $product['title'] }}
            </h3>
            
            <!-- Rating Stars -->
            <div class="flex items-center gap-1 text-[#1d1d1f] text-xs">
                <span class="text-black font-semibold">★★★★☆</span>
                <span class="text-xs text-[#86868b] font-normal ml-1">({{ $product['offers_count'] ?? 1 }})</span>
            </div>

            <!-- Short Description / Details -->
            <p class="text-[11px] text-[#86868b] line-clamp-2 leading-relaxed">
                {{ $product['short_description'] }}
            </p>
        </div>

        <!-- Right Column: Price & Order Button (Heart removed) -->
        <div class="flex flex-col items-end justify-between space-y-4">
            
            <!-- Price Badge -->
            <div class="text-right">
                <div class="text-lg font-black text-[#1d1d1f] tracking-tight">
                    @if(!empty($product['price_amount']))
                        €{{ number_format($product['price_amount'], 2) }}
                    @else
                        {{ $product['price_note'] ?: __('Уточняется') }}
                    @endif
                </div>
            </div>

            <!-- Single Action Button (Bag/Order) -->
            <div class="flex flex-col space-y-2.5">
                <a href="{{ route('buy-for-me') }}?url={{ urlencode($product['product_url']) }}" 
                   title="{{ __('Оформить заказ') }}"
                   class="w-12 h-12 rounded-full bg-[#1d1d1f] hover:bg-black text-white shadow-md hover:shadow-lg hover:scale-105 active:scale-95 flex items-center justify-center transition-all">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </a>
            </div>

        </div>

    </div>

</div>