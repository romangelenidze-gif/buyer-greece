@props([
    'type' => 'telegram', // telegram или whatsapp
    'orderNumber' => null,
    'message' => null,
    'size' => 'default',
])

@php
    $telegramUsername = config('services.telegram.username', 'buyer_greece_bot');
    $whatsappPhone = config('services.whatsapp.phone', '306900000000');

    // Формирование безопасного текста с экранированием URL
    $defaultMessage = $orderNumber 
        ? "Здравствуйте, нужна помощь по заказу #{$orderNumber}" 
        : "Здравствуйте, у меня есть вопрос по сервису Buyer Greece";

    $encodedText = urlencode($message ?? $defaultMessage);

    if ($type === 'telegram') {
        $url = "https://t.me/{$telegramUsername}?text={$encodedText}";
        $label = 'Telegram';
        $bgColor = 'bg-sky-500 hover:bg-sky-600 text-white';
    } else {
        $url = "https://wa.me/{$whatsappPhone}?text={$encodedText}";
        $label = 'WhatsApp';
        $bgColor = 'bg-emerald-600 hover:bg-emerald-500 text-white';
    }

    $sizeClasses = match($size) {
        'small' => 'px-3 py-1.5 text-xs font-medium',
        'large' => 'px-6 py-3 text-sm font-bold',
        default => 'px-4 py-2 text-xs font-semibold',
    };
@endphp

<a 
    href="{{ $url }}" 
    target="_blank" 
    rel="noopener noreferrer"
    {{ $attributes->merge(['class' => "inline-flex items-center justify-center gap-2 rounded-lg transition-colors {$bgColor} {$sizeClasses}"]) }}
>
    @if($type === 'telegram')
        <svg class="w-4 h-4 fill-current flex-shrink-0" viewBox="0 0 24 24">
            <path d="M12 0C5.37 0 0 5.37 0 12s5.37 12 12 12 12-5.37 12-12S18.63 0 12 0zm5.56 8.16l-2.02 9.52c-.15.68-.56.84-1.13.53l-3.1-2.28-1.5 1.44c-.17.17-.31.31-.63.31l.22-3.15 5.74-5.18c.25-.22-.05-.34-.39-.12l-7.09 4.46-3.05-.95c-.66-.21-.67-.66.14-.98l11.93-4.6c.55-.2 1.03.13.88.82z"/>
        </svg>
    @else
        <svg class="w-4 h-4 fill-current flex-shrink-0" viewBox="0 0 24 24">
            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.705 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
        </svg>
    @endif
    <span>{{ $slot->isEmpty() ? $label : $slot }}</span>
</a>