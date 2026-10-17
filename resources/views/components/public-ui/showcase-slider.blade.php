@props([
    'products' => null
])

@php
    $products = $products ?? \App\Models\ShowcaseProduct::where('is_published', true)->orderBy('sort_order')->get();
@endphp

@if($products->isNotEmpty())
<section class="py-16 bg-white border-t border-b border-black/[0.06] overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4">
            <div>                
                <h2 class="apple-section-title text-left">
                    {{ __('Популярные товары') }}
                </h2>
                <p class="apple-section-subtitle text-left mt-1">
                    {{ __('Лучшие находки из греческих интернет-магазинов с доставкой') }}
                </p>
            </div>
            
            @if($products->count() > 1)
            <div class="flex gap-2 shrink-0">
                <button onclick="document.getElementById('showcase-scroll-container').scrollBy({left: -320, behavior: 'smooth'})" 
                        class="w-10 h-10 rounded-full bg-white hover:bg-black/[0.04] text-[#1d1d1f] border border-black/[0.08] shadow-xs flex items-center justify-center transition-all cursor-pointer active:scale-95" 
                        aria-label="Назад">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button onclick="document.getElementById('showcase-scroll-container').scrollBy({left: 320, behavior: 'smooth'})" 
                        class="w-10 h-10 rounded-full bg-white hover:bg-black/[0.04] text-[#1d1d1f] border border-black/[0.08] shadow-xs flex items-center justify-center transition-all cursor-pointer active:scale-95" 
                        aria-label="Вперед">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
            @endif
        </div>
    </div>

    <div class="w-full">
        <div id="showcase-scroll-container" 
             class="flex overflow-x-auto space-x-6 py-4 px-4 sm:px-8 lg:px-[max(2rem,calc((100vw-80rem)/2))] no-scrollbar snap-x snap-center scroll-smooth">
            @foreach($products as $item)
                @php
                    $productData = [
                        'title' => $item->name, // В миграции поле называется name
                        'image_url' => $item->image_path, // В миграции поле называется image_path
                        'merchant_name' => $item->partner_name ?? __('Греция'), // В миграции partner_name
                        'product_url' => $item->partner_product_url, // В миграции partner_product_url
                        'price_amount' => $item->price,
                        'price_note' => null,
                        'short_description' => $item->short_description ?? '',
                        'offers_count' => 1,
                    ];
                @endphp
                <div class="snap-center flex-shrink-0 w-[80%] sm:w-[40%] lg:w-[24%] min-w-[260px]">
                    <x-public-ui.product-card :product="$productData" />
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif