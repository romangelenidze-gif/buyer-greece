@props(['filters' => [], 'query' => ''])

<div class="flex items-center gap-2 pt-3 pb-2 text-xs px-3 sm:px-1">
    
    <!-- Скроллируемая область для инпутов цены -->
    <div class="flex items-center gap-2 overflow-x-auto no-scrollbar shrink-0 py-1.5 px-1">
        
        <!-- MIN PRICE -->
        <div class="apple-filter-pill shrink-0 px-4 py-1.5 flex items-center">
            <input 
                type="number" 
                name="min_price" 
                value="{{ request('min_price', $filters['min_price'] ?? '') }}" 
                placeholder="{{ __('от') }}" 
                step="0.01" 
                min="0"
                @change="loading = true; $refs.searchForm.submit()"
                class="apple-filter-input bg-transparent border-0 p-0 text-xs text-[#1d1d1f] focus:outline-none focus:ring-0 w-16"
            >
            <span class="text-[#86868b] text-xs ml-1 font-normal">€</span>
        </div>

        <!-- MAX PRICE -->
        <div class="apple-filter-pill shrink-0 px-4 py-1.5 flex items-center">
            <input 
                type="number" 
                name="max_price" 
                value="{{ request('max_price', $filters['max_price'] ?? '') }}" 
                placeholder="{{ __('до') }}" 
                step="0.01" 
                min="0"
                @change="loading = true; $refs.searchForm.submit()"
                class="apple-filter-input bg-transparent border-0 p-0 text-xs text-[#1d1d1f] focus:outline-none focus:ring-0 w-16"
            >
            <span class="text-[#86868b] text-xs ml-1 font-normal">€</span>
        </div>

    </div>

    <!-- ДЕСКТОП: СОРТИРОВКА -->
    <div class="hidden sm:flex ml-auto items-center relative pr-1" x-data="{ sortOpen: false }" @click.outside="sortOpen = false">
        
        <!-- Кнопка-триггер -->
        <div @click="sortOpen = !sortOpen" 
             class="apple-filter-pill shrink-0 px-4 py-2 flex items-center justify-between cursor-pointer select-none space-x-3 transition-all duration-200 hover:bg-[#e8e8ed] active:scale-[0.98]">
            <span class="text-xs font-medium text-[#1d1d1f]" x-text="
                activeSort === 'price_asc' ? '{{ __('Подешевле') }}' : 
                (activeSort === 'price_desc' ? '{{ __('Подороже') }}' : '{{ __('Популярные') }}')
            "></span>
            <svg class="w-3.5 h-3.5 text-[#86868b] transition-transform duration-300 ease-out" :class="{ 'rotate-180': sortOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
        </div>

        <!-- Выпадающее меню Apple Glassmorphism -->
        <div x-show="sortOpen" 
             x-transition:enter="transition ease-out duration-150" 
             x-transition:enter-start="opacity-0 scale-95 -translate-y-1" 
             x-transition:enter-end="opacity-100 scale-100 translate-y-0" 
             x-transition:leave="transition ease-in duration-100" 
             x-transition:leave-start="opacity-100 scale-100 translate-y-0" 
             x-transition:leave-end="opacity-0 scale-95 -translate-y-1" 
             x-cloak 
             class="absolute right-0 top-full mt-2 w-52 rounded-2xl overflow-hidden shadow-[0_10px_30px_rgba(0,0,0,0.08)] py-1.5 z-[100] text-xs font-medium bg-white/95 backdrop-blur-xl border border-black/[0.08] text-[#1d1d1f]">
            
            <div @click="activeSort = 'relevance'; sortProducts('relevance'); sortOpen = false" 
                 class="flex items-center justify-between px-3.5 py-2 transition-colors cursor-pointer hover:bg-black/[0.04]"
                 :class="{ 'text-[#0071e3] font-semibold bg-[#0071e3]/5': activeSort === 'relevance' }">
                <span>{{ __('Популярные') }}</span>
                <svg x-show="activeSort === 'relevance'" class="w-3.5 h-3.5 text-[#0071e3]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>

            <div @click="activeSort = 'price_asc'; sortProducts('price_asc'); sortOpen = false" 
                 class="flex items-center justify-between px-3.5 py-2 transition-colors cursor-pointer hover:bg-black/[0.04]"
                 :class="{ 'text-[#0071e3] font-semibold bg-[#0071e3]/5': activeSort === 'price_asc' }">
                <span>{{ __('Подешевле') }}</span>
                <svg x-show="activeSort === 'price_asc'" class="w-3.5 h-3.5 text-[#0071e3]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>

            <div @click="activeSort = 'price_desc'; sortProducts('price_desc'); sortOpen = false" 
                 class="flex items-center justify-between px-3.5 py-2 transition-colors cursor-pointer hover:bg-black/[0.04]"
                 :class="{ 'text-[#0071e3] font-semibold bg-[#0071e3]/5': activeSort === 'price_desc' }">
                <span>{{ __('Подороже') }}</span>
                <svg x-show="activeSort === 'price_desc'" class="w-3.5 h-3.5 text-[#0071e3]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
        </div>
    </div>

    <!-- МОБИЛКА: КНОПКА СО СТРЕЛОЧКАМИ -->
    <button 
        type="button" 
        @click="sortModalOpen = true"
        class="flex sm:hidden w-8 h-8 rounded-full bg-[#e5e5ea] active:bg-[#d1d1d6] items-center justify-center text-[#1d1d1f] transition-all shrink-0 ml-auto"
        title="{{ __('Сортировка') }}"
    >
        <svg class="w-4 h-4 text-[#1d1d1f]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
        </svg>
    </button>

    <!-- RESET FILTERS BUTTON -->
    @if(request()->hasAny(['min_price', 'max_price']) || (request('sort') && request('sort') !== 'relevance'))
        <a 
            href="{{ route('product-search', ['q' => $query]) }}" 
            @click="loading = true"
            class="w-8 h-8 rounded-full bg-[#f2f2f7] hover:bg-[#e5e5ea] text-[#86868b] hover:text-[#1d1d1f] flex items-center justify-center transition-all shrink-0 ml-1 mr-1"
            title="{{ __('Сбросить фильтры') }}"
        >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </a>
    @endif

</div>