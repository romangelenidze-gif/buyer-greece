@props([
    'align' => 'justify-center lg:justify-start'
])

<!-- Кнопки мессенджеров -->
<div class="pt-3 flex flex-wrap items-center {{ $align }} gap-2.5 sm:gap-3">
    
    <!-- Telegram -->
    <a href="https://t.me/{{ config('services.telegram.username', 'buyer_greece_bot') }}" target="_blank" rel="noopener noreferrer" 
       title="Telegram"
       class="inline-flex items-center justify-center w-11 h-11 sm:w-auto sm:h-auto sm:px-5 sm:py-3 rounded-full sm:rounded-full bg-[#38bdf8] hover:bg-[#0ea5e9] text-white text-xs font-semibold shadow-sm transition-all shrink-0">
        <svg class="w-5 h-5 sm:w-4 sm:h-4 fill-current shrink-0" viewBox="0 0 24 24">
            <path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm5.894 8.221l-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.14.14-.26.26-.534.26l.213-3.05 5.56-5.022c.24-.213-.054-.334-.373-.121l-6.869 4.326-2.96-.924c-.64-.203-.658-.64.135-.954l11.566-4.458c.538-.196 1.006.128.832.941z"/>
        </svg>
        <span class="hidden sm:inline sm:ml-2">Telegram</span>
    </a>

    <!-- WhatsApp -->
    <a href="https://wa.me/{{ config('services.whatsapp.phone', '306900000000') }}" target="_blank" rel="noopener noreferrer" 
       title="WhatsApp"
       class="inline-flex items-center justify-center w-11 h-11 sm:w-auto sm:h-auto sm:px-5 sm:py-3 rounded-full sm:rounded-full bg-[#10b981] hover:bg-[#059669] text-white text-xs font-semibold shadow-sm transition-all shrink-0">
        <svg class="w-5 h-5 sm:w-4 sm:h-4 fill-current shrink-0" viewBox="0 0 24 24">
            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
        </svg>
        <span class="hidden sm:inline sm:ml-2">WhatsApp</span>
    </a>

    <!-- Messenger (с градиентом) -->
    <a href="https://m.me/{{ config('services.messenger.page', 'buyer.greece') }}" target="_blank" rel="noopener noreferrer" 
       title="Messenger"
       style="--gradient: linear-gradient(65deg, #1b75ff 0%, #a238ff 55%, #ff5263 100%);"
       class="relative inline-flex items-center justify-center w-11 h-11 sm:w-auto sm:h-auto sm:px-5 sm:py-3 rounded-full sm:rounded-full text-white text-xs font-semibold shadow-sm overflow-hidden z-0
              before:content-[''] before:absolute before:inset-0 before:[background:var(--gradient)] before:z-[-1] before:transition-all before:duration-300
              hover:before:brightness-75 shrink-0">
        <svg class="w-5 h-5 sm:w-4 sm:h-4 fill-current shrink-0" viewBox="0 0 24 24">
            <path d="M12 0C5.373 0 0 4.974 0 11.111c0 3.497 1.745 6.616 4.472 8.652V24l4.085-2.242c1.05.291 2.164.448 3.443.448 6.627 0 12-4.974 12-11.111C24 4.974 18.627 0 12 0zm1.191 14.963l-3.055-3.262-5.963 3.262 6.559-6.963 3.13 3.262 5.888-3.262-6.559 6.963z"/>
        </svg>
        <span class="hidden sm:inline sm:ml-2">Messenger</span>
    </a>

</div>