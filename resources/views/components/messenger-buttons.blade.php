@props([
    'orderNumber' => null,
    'text' => null,
    'size' => 'default', // small, default, large
])

@php
    // Берём контакты из конфига или задаем умолчания
    $telegramUsername = config('services.telegram.username', 'buyergreece_support');
    $whatsappPhone = config('services.whatsapp.phone', '306900000000');

    $defaultText = $orderNumber 
        ? "Здравствуйте! Нужна помощь по заказу {$orderNumber}."
        : ($text ?? "Здравствуйте! У меня есть вопрос по услугам Buyer Greece.");

    $encodedText = urlencode($defaultText);

    $tgUrl = "https://t.me/{$telegramUsername}?text={$encodedText}";
    $waUrl = "https://wa.me/{$whatsappPhone}?text={$encodedText}";

    $paddingClass = match($size) {
        'small' => 'px-2.5 py-1 text-[11px]',
        'large' => 'px-5 py-2.5 text-sm',
        default => 'px-3.5 py-1.5 text-xs',
    };
@endphp

<div class="flex flex-wrap items-center gap-2">
    <!-- TELEGRAM BUTTON -->
    <a 
        href="{{ $tgUrl }}" 
        target="_blank" 
        rel="noopener noreferrer"
        class="inline-flex items-center gap-1.5 font-semibold text-white bg-[#229ED9] hover:bg-[#1e8dbf] rounded-lg transition-colors {{ $paddingClass }}"
    >
        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
            <path d="M12 0C5.37 0 0 5.37 0 12s5.37 12 12 12 12-5.37 12-12S18.63 0 12 0zm5.56 8.16l-2.03 9.56c-.15.68-.55.84-1.12.52l-3.11-2.29-1.5 1.45c-.17.17-.31.31-.63.31l.22-3.16 5.76-5.2c.25-.22-.05-.34-.39-.12l-7.12 4.48-3.07-.96c-.67-.21-.68-.67.14-.99l12-4.63c.56-.2 1.05.14.86.84z"/>
        </svg>
        <span>Telegram</span>
    </a>

    <!-- WHATSAPP BUTTON -->
    <a 
        href="{{ $waUrl }}" 
        target="_blank" 
        rel="noopener noreferrer"
        class="inline-flex items-center gap-1.5 font-semibold text-white bg-[#25D366] hover:bg-[#20bd5a] rounded-lg transition-colors {{ $paddingClass }}"
    >
        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
        </svg>
        <span>WhatsApp</span>
    </a>
</div>