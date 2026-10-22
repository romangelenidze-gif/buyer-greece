<template x-teleport="body">
    <div 
        x-show="sortModalOpen" 
        x-cloak 
        class="fixed inset-0 z-50 sm:hidden flex flex-col justify-end"
    >
        <!-- Тёмная подложка -->
        <div 
            x-show="sortModalOpen" 
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="sortModalOpen = false"
            class="absolute inset-0 bg-black/40 backdrop-blur-xs"
        ></div>

        <!-- Самовыплывающее меню -->
        <div 
            x-show="sortModalOpen" 
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="translate-y-full"
            x-transition:enter-end="translate-y-0"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="translate-y-0"
            x-transition:leave-end="translate-y-full"
            class="relative w-full bg-white rounded-t-3xl pt-6 px-6 pb-8 shadow-2xl border-t border-black/[0.05] space-y-5"
            style="padding-bottom: max(2rem, env(safe-area-inset-bottom));"
        >
            <div class="flex items-center justify-between border-b border-black/[0.06] pb-3">
                <h3 class="app-text-title tracking-tight">{{ __('Order by') }}</h3>
                <button @click="sortModalOpen = false" type="button" class="w-8 h-8 rounded-full bg-[#f2f2f7] active:bg-[#e5e5ea] flex items-center justify-center text-[#86868b] hover:text-[#1d1d1f] transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="space-y-3">
                <label @click="sortProducts('relevance')" class="apple-sheet-option">
                    <span class="app-text-body font-medium">{{ __('Recommended') }}</span>
                    <div class="apple-radio-circle"
                         :class="activeSort === 'relevance' ? 'border-[#0071e3] bg-[#0071e3]' : 'border-[#d1d1d6]'">
                        <div class="w-2 h-2 rounded-full bg-white" x-show="activeSort === 'relevance'"></div>
                    </div>
                </label>

                <label @click="sortProducts('price_asc')" class="apple-sheet-option">
                    <span class="app-text-body font-medium">{{ __('Lowest price') }}</span>
                    <div class="apple-radio-circle"
                         :class="activeSort === 'price_asc' ? 'border-[#0071e3] bg-[#0071e3]' : 'border-[#d1d1d6]'">
                        <div class="w-2 h-2 rounded-full bg-white" x-show="activeSort === 'price_asc'"></div>
                    </div>
                </label>

                <label @click="sortProducts('price_desc')" class="apple-sheet-option">
                    <span class="app-text-body font-medium">{{ __('Highest price') }}</span>
                    <div class="apple-radio-circle"
                         :class="activeSort === 'price_desc' ? 'border-[#0071e3] bg-[#0071e3]' : 'border-[#d1d1d6]'">
                        <div class="w-2 h-2 rounded-full bg-white" x-show="activeSort === 'price_desc'"></div>
                    </div>
                </label>
            </div>
        </div>
    </div>
</template>