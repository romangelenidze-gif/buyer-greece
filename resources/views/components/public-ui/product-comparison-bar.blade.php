<!-- FLOATING COMPARISON BAR & MODAL WINDOW (AI-CONCIERGE) -->
<div x-data="{ 
    showModal: false,
    loading: false,
    comparisonResult: null,
    compare() {
        if (this.$store.comparison.items.length < 2) return;
        this.loading = true;
        this.showModal = true;
        
        fetch('{{ route('api.concierge.compare') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ products: this.$store.comparison.items })
        })
        .then(res => res.json())
        .then(data => {
            this.loading = false;
            if (data.success) {
                this.comparisonResult = data.data;
            } else {
                this.comparisonResult = { recommendation: data.message || 'Ошибка сравнения' };
            }
        })
        .catch(err => {
            this.loading = false;
            this.comparisonResult = { recommendation: 'Ошибка соединения с сервером.' };
        });
    }
}">
    
    <!-- FLOATING BAR -->
    <div x-show="$store.comparison && $store.comparison.items.length > 0" 
         x-cloak
         class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 w-full max-w-2xl px-4">
        
        <div class="bg-[#1d1d1f]/90 backdrop-blur-xl text-white p-4 rounded-2xl shadow-2xl border border-white/10 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3 overflow-x-auto py-1">
                <span class="text-xs font-medium text-[#86868b] uppercase tracking-wider shrink-0">
                    {{ __('Сравнение') }} (<span x-text="$store.comparison.items.length"></span>/4):
                </span>
                <template x-for="(item, index) in $store.comparison.items" :key="item.product_url">
                    <div class="flex items-center gap-1.5 bg-white/10 px-3 py-1.5 rounded-xl shrink-0">
                        <span class="text-xs font-medium truncate max-w-[120px]" x-text="item.title"></span>
                        <button @click="$store.comparison.items.splice(index, 1)" class="text-white/60 hover:text-white text-sm font-bold ml-1">×</button>
                    </div>
                </template>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <button @click="compare()" 
                        :disabled="$store.comparison.items.length < 2"
                        :class="$store.comparison.items.length < 2 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-blue-600'"
                        class="px-4 py-2 bg-[#0071e3] text-white text-xs font-medium rounded-xl transition-colors">
                    {{ __('Сравнить с ИИ') }}
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL WINDOW FOR AI CONCIERGE COMPARISON -->
    <div x-show="showModal" x-cloak class="fixed inset-0 bg-black/60 backdrop-blur-md flex items-center justify-center p-4 z-50">
        <div @click.away="showModal = false" class="bg-white text-[#1d1d1f] w-full max-w-4xl max-h-[90vh] rounded-3xl shadow-2xl overflow-hidden flex flex-col">
            
            <!-- Modal Header -->
            <div class="p-6 border-b border-black/[0.08] flex items-center justify-between bg-[#fbfbfd]">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-[#0071e3]/10 text-[#0071e3] flex items-center justify-center">
                        ✦
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold">{{ __('AI-Консьерж: Сравнение товаров') }}</h3>
                        <p class="text-xs text-[#6e6e73]">{{ __('Объективный анализ характеристик и цен из греческих магазинов') }}</p>
                    </div>
                </div>
                <button @click="showModal = false" class="w-8 h-8 rounded-full bg-black/5 hover:bg-black/10 flex items-center justify-center text-sm font-bold">✕</button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 overflow-y-auto space-y-6 flex-1">
                
                <!-- Loading State -->
                <div x-show="loading" class="py-16 text-center space-y-3">
                    <div class="inline-block w-8 h-8 border-4 border-[#0071e3] border-t-transparent rounded-full animate-spin"></div>
                    <p class="text-sm font-medium text-[#6e6e73]">{{ __('ИИ анализирует выбранные товары и цены...') }}</p>
                </div>

                <!-- Results State -->
                <div x-show="!loading && comparisonResult" class="space-y-6">
                    
                    <!-- Recommendation text -->
                    <div class="bg-blue-50/70 border border-blue-100 p-4 rounded-2xl">
                        <h4 class="text-xs font-semibold text-[#0071e3] uppercase tracking-wider mb-1">{{ __('Экспертный вывод') }}</h4>
                        <p class="text-sm text-[#1d1d1f] leading-relaxed" x-text="comparisonResult?.recommendation"></p>
                    </div>

                    <!-- Comparison Table -->
                    <template x-if="comparisonResult?.comparison_table">
                        <div class="border border-black/[0.08] rounded-2xl overflow-hidden">
                            <table class="w-full text-left border-collapse">
                                <tbody>
                                    <template x-for="row in comparisonResult.comparison_table">
                                        <tr class="border-b border-black/[0.04] last:border-none">
                                            <td class="p-3.5 text-xs font-semibold text-[#6e6e73] bg-[#fbfbfd] w-1/3" x-text="row.feature"></td>
                                            <template x-for="val in row.values">
                                                <td class="p-3.5 text-sm text-[#1d1d1f]" x-text="val"></td>
                                            </template>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </template>

                </div>

            </div>

            <!-- Modal Footer -->
            <div class="p-4 border-t border-black/[0.08] bg-[#fbfbfd] flex justify-end">
                <button @click="showModal = false" class="px-5 py-2 bg-black/5 hover:bg-black/10 text-xs font-medium rounded-xl transition-colors">
                    {{ __('Закрыть') }}
                </button>
            </div>

        </div>
    </div>

</div>