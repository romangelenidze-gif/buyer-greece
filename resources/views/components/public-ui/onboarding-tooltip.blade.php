@props(['show' => false])

<div x-show="{{ $show }}" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-2 scale-95"
     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
     x-transition:leave-end="opacity-0 translate-y-2 scale-95"
     style="display: none;"
     class="absolute right-0 bottom-full mb-4 w-72 sm:w-80 p-5 bg-[#1d1d1f]/95 backdrop-blur-2xl text-white rounded-[1.25rem] shadow-[0_20px_50px_rgba(0,0,0,0.3)] border border-white/15 z-[99]">
    
    <div class="flex items-start justify-between gap-3 mb-2">
        <span class="font-semibold text-white text-sm tracking-tight flex items-center gap-2">
            <span class="w-6 h-6 rounded-full bg-[#26b3aa]/20 text-[#26b3aa] flex items-center justify-center shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </span>
            {{ __('Как сделать заказ?') }}
        </span>
        <button @click="dismissTooltip()" class="text-white/50 hover:text-white transition-colors cursor-pointer p-1 -mr-1 rounded-full hover:bg-white/10" aria-label="{{ __('Закрыть') }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
    
    <p class="text-white/85 text-xs sm:text-[0.8125rem] leading-relaxed font-normal">
        {{ __('Кликните по карточке, чтобы изучить товар на BestPrice, а затем вернитесь и нажмите на эту кнопку, чтобы оформить заказ.') }}
    </p>

    <!-- Элегантный хвостик тултипа -->
    <div class="absolute -bottom-1.5 right-6 w-3 h-3 bg-[#1d1d1f] rotate-45 border-r border-b border-white/15"></div>
</div>