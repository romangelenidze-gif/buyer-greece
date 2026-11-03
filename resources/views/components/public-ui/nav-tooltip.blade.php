<span class="relative inline-flex items-center ml-1.5 align-middle" x-data="{ tipOpen: false }" @click.outside="tipOpen = false">
    <button @click="tipOpen = !tipOpen" 
            type="button"
            class="inline-flex items-center justify-center text-lg transition-transform duration-300 hover:scale-110 focus:outline-none cursor-pointer select-none group"
            :class="tipOpen ? 'text-amber-400' : 'text-gray-400 hover:text-amber-400'"
            aria-label="{{ __('Совет по навигации') }}"
    >
        <svg class="w-5 h-5 transition-colors duration-300" :class="tipOpen ? 'text-amber-400' : 'text-gray-400 hover:text-amber-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
        </svg>
    </button>

    <!-- Тултип с адаптивной шириной и защитой от вылезания за границы экрана на мобилках -->
    <span x-show="tipOpen" 
          x-transition:enter="transition ease-out duration-200"
          x-transition:enter-start="opacity-0 translate-y-1 scale-95"
          x-transition:enter-end="opacity-100 translate-y-0 scale-100"
          x-transition:leave="transition ease-in duration-150"
          x-transition:leave-start="opacity-100 translate-y-0 scale-100"
          x-transition:leave-end="opacity-0 translate-y-1 scale-95"
          style="display: none;"
          class="absolute top-full mt-2 left-1/2 -translate-x-1/2 w-64 sm:w-80 max-w-[calc(100vw-2rem)] p-4 bg-[#1d1d1f]/95 backdrop-blur-2xl text-white rounded-2xl shadow-2xl border border-white/15 z-50 text-left block"
    >
 
        <span class="block text-white/85 text-[11px] sm:text-sm leading-relaxed font-normal whitespace-normal">
            {{ __('Совет: сайт BestPrice.gr доступен на греческом языке. Для удобства навигации воспользуйтесь встроенной функцией перевода страниц вашего браузера и выберите английский язык (Translate to English).') }}
        </span>

        <!-- Хвостик тултипа -->
        <span class="absolute -top-1 left-1/2 -translate-x-1/2 w-2.5 h-2.5 bg-[#1d1d1f] rotate-45 border-l border-t border-white/15 block"></span>
    </span>
</span>