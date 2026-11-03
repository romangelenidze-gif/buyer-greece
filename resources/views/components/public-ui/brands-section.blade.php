@props(['brands' => []])

<section class="max-w-4xl mx-auto px-6 py-12 text-center space-y-6">
    <div class="space-y-2">
        <h2 class="apple-section-title text-2xl mb-2">
            {{ __('Безграничные возможмости') }}
        </h2>
        <p class="app-text-body text-[#424245] max-w-2xl mx-auto leading-relaxed">
            {{ __('Найдите нужный товар на сайте любого магазина Греции, скопируйте ссылку и отправьте её нам для выкупа или укажите наш греческий адрес при самостоятельном оформлении.') }}
        </p>
    </div>

    <!-- Брендовые капсулы -->
    <div class="flex flex-wrap items-center justify-center gap-2">
        @foreach($brands as $brand)
            <a href="{{ route('product-search', ['q' => $brand]) }}" class="apple-brand-pill">
                {{ $brand }}
            </a>
        @endforeach
    </div>

    <!-- Контурные Apple-кнопки -->
    <div class="flex flex-wrap items-center justify-center gap-3 pt-3">
        <a href="{{ route('buy-for-me') }}" class="apple-outline-pill">
            {{ __('Buy For Me') }}
        </a>
        
        <a href="{{ route('forwarding') }}?type=forwarding" class="apple-outline-pill">
            {{ __('Forwarding') }}
        </a>
    </div>
</section>