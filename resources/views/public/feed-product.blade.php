<x-layouts.public
    title="{{ $title }} | {{ $product->brand }} | Buyer Greece"
    description="{{ __('Цена, размеры и наличие в магазине. Переход в магазин по партнёрской ссылке, цена для вас не меняется.') }}"
>

<div class="apple-pdp"
     x-data="{ zoom: false }"
     x-effect="document.body.classList.toggle('overflow-hidden', zoom)"
     @keydown.escape.window="zoom = false"
>

    <a href="{{ $backUrl }}" class="apple-pdp-back">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
        {{ __('К результатам поиска') }}
    </a>

    <div class="apple-pdp-grid">

        <!-- Фото: по клику открывается на весь экран; под ним ссылка на остальные фото в магазине -->
        <div class="apple-pdp-media">
        <button type="button" class="apple-pdp-gallery" @click="zoom = true" aria-label="{{ __('Увеличить фото') }}">
            @if($product->discount_percent > 0)
                <span class="apple-pdp-badge">-{{ $product->discount_percent }}%</span>
            @endif

            @if($product->image_url)
                <img src="{{ $product->image_url }}" alt="{{ $title }}" class="apple-pdp-image apple-photo-lift">
                <span class="apple-pdp-zoom-hint" aria-hidden="true">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m-3-3h6" />
                    </svg>
                </span>
            @endif
        </button>

        <p class="apple-pdp-photo-note">
            {{ __('Больше фото этого товара') }}:
            <a href="{{ $outboundUrl }}" target="_blank" rel="sponsored nofollow noopener noreferrer">{{ __('смотреть на :store', ['store' => $storeName]) }}</a>
        </p>
        </div>

        <!-- Информация -->
        <div class="apple-pdp-info">

            @if(!empty($categories))
                <p class="apple-pdp-crumbs">{{ implode(' › ', $categories) }}</p>
            @endif

            <p class="apple-pdp-brand">{{ $product->brand }}</p>
            <h1 class="apple-pdp-title">{{ $title }}</h1>

            <div class="apple-pdp-price-row">
                <span class="apple-pdp-price">€{{ number_format($product->price, 2) }}</span>
                @if($product->old_price)
                    <span class="apple-pdp-price-old">€{{ number_format($product->old_price, 2) }}</span>
                @endif
            </div>


            @if($product->colour)
                <p class="apple-pdp-colour">
                    <span class="apple-pdp-label">{{ __('Цвет') }}:</span> {{ $product->colour }}
                </p>
            @endif

            <!-- Другие расцветки -->
            @if($colorways->count() > 1)
                <div class="apple-pdp-colors" role="list">
                    @foreach($colorways as $cw)
                        <a href="{{ route('feed-product.show', ['feed' => $cw->feed, 'sku' => $cw->sku]) }}"
                           role="listitem"
                           title="{{ $cw->colour }}"
                           class="apple-pdp-color @if($cw->sku === $product->sku) is-active @endif">
                            @if($cw->image_url)
                                <img src="{{ $cw->image_url }}" alt="{{ $cw->colour }}" loading="lazy" class="apple-photo-lift">
                            @endif
                        </a>
                    @endforeach
                </div>
            @endif

            <!-- Размеры -->
            @if(!empty($product->sizes))
                <div>
                    <p class="apple-pdp-label">{{ __('Размеры в наличии') }}</p>
                    <div class="apple-pdp-sizes">
                        @foreach($product->sizes as $size)
                            <span class="apple-pdp-size @if(isset($size['stock']) && $size['stock'] <= 0) is-out @endif">{{ $size['size'] }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            @unless($product->in_stock)
                <p class="apple-pdp-note">{{ __('Сейчас нет в наличии') }}</p>
            @endunless

            <!-- Действие -->
            <div class="apple-pdp-actions">
                <a href="{{ $outboundUrl }}" target="_blank" rel="sponsored nofollow noopener noreferrer" class="apple-btn-primary">
                    {{ __('Купить на :store', ['store' => $storeName]) }}
                </a>
            </div>

            <p class="apple-pdp-note">
                {{ __('Цена и наличие обновляются ежедневно, итоговые данные смотрите на сайте магазина.') }}
                {{ __('Ссылка на магазин партнёрская: мы получаем комиссию, а цена для вас не меняется.') }}
            </p>

            <!-- Характеристики -->
            @if(!empty($specs))
                <div>
                    <h2 class="apple-pdp-section-title">{{ __('Характеристики') }}</h2>
                    <ul class="apple-pdp-specs">
                        @foreach($specs as $spec)
                            <li>{{ $spec }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

        </div>
    </div>

    <!-- Просмотр фото на весь экран -->
    @if($product->image_url)
        <template x-teleport="body">
            <div x-show="zoom" x-cloak x-transition.opacity.duration.200ms
                 @click="zoom = false"
                 class="apple-lightbox" role="dialog" aria-modal="true" aria-label="{{ $title }}">
                <button type="button" class="apple-lightbox-close" @click.stop="zoom = false" aria-label="{{ __('Закрыть') }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <img src="{{ $product->image_url }}" alt="{{ $title }}" class="apple-lightbox-image apple-photo-lift" @click.stop>
            </div>
        </template>
    @endif
</div>

</x-layouts.public>
