<div class="fixed bottom-6 right-6 z-50 flex flex-col items-end space-y-3" x-data="{ open: false }" @click.outside="open = false">
    
    <!-- Выпадающее меню мессенджеров -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-2 scale-95"
         x-cloak
         class="bg-white/90 backdrop-blur-2xl border border-black/10 rounded-2xl shadow-2xl p-3 flex flex-col space-y-2.5 min-w-[200px]">
        
        <div class="px-3 py-1 text-[11px] font-semibold text-gray-400 uppercase tracking-wider border-b border-gray-100 pb-1.5">
            {{ __('Связаться с нами') }}
        </div>

        <!-- Telegram -->
        <a href="https://t.me/{{ config('services.telegram.username', 'buyer_greece_bot') }}" target="_blank" rel="noopener noreferrer" 
           class="flex items-center space-x-3 px-3 py-2 rounded-xl hover:bg-gray-100 transition-colors group">
            <div class="w-11 h-11 rounded-full bg-[#38bdf8] text-white flex items-center justify-center shrink-0 shadow-sm">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                    <path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm5.894 8.221l-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.14.14-.26.26-.534.26l.213-3.05 5.56-5.022c.24-.213-.054-.334-.373-.121l-6.869 4.326-2.96-.924c-.64-.203-.658-.64.135-.954l11.566-4.458c.538-.196 1.006.128.832.941z"/>
                </svg>
            </div>
            <span class="text-xs font-semibold text-gray-800">Telegram</span>
        </a>

        <!-- WhatsApp -->
        <a href="https://wa.me/{{ config('services.whatsapp.phone', '306900000000') }}" target="_blank" rel="noopener noreferrer" 
           class="flex items-center space-x-3 px-3 py-2 rounded-xl hover:bg-gray-100 transition-colors group">
            <div class="w-11 h-11 rounded-full bg-[#10b981] text-white flex items-center justify-center shrink-0 shadow-sm">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                </svg>
            </div>
            <span class="text-xs font-semibold text-gray-800">WhatsApp</span>
        </a>

        <!-- Facebook Messenger -->
        <a href="https://m.me/{{ config('services.messenger.page', 'buyer.greece') }}" target="_blank" rel="noopener noreferrer" 
           class="flex items-center space-x-3 px-3 py-2 rounded-xl hover:bg-gray-100 transition-colors group">
            <div style="background: linear-gradient(65deg, #1b75ff 0%, #a238ff 55%, #ff5263 100%);"
                 class="w-11 h-11 rounded-full text-white flex items-center justify-center shadow-sm shrink-0">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                    <path d="M12 0C5.373 0 0 4.974 0 11.111c0 3.497 1.745 6.616 4.472 8.652V24l4.085-2.242c1.05.291 2.164.448 3.443.448 6.627 0 12-4.974 12-11.111C24 4.974 18.627 0 12 0zm1.191 14.963l-3.055-3.262-5.963 3.262 6.559-6.963 3.13 3.262 5.888-3.262-6.559 6.963z"/>
                </svg>
            </div>
            <span class="text-xs font-semibold text-gray-800">Messenger</span>
        </a>

    </div>

    <!-- Главная плавающая кнопка вызова -->
    <button @click="open = !open" 
            class="w-14 h-14 rounded-full bg-[#1d1d1f] hover:bg-black text-white shadow-2xl flex items-center justify-center transition-all duration-300 hover:scale-105 active:scale-95 cursor-pointer border border-white/20">
        <svg x-show="!open" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
        <svg x-show="open" x-cloak class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>

</div>