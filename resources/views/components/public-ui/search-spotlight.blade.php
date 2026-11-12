<section class="pt-0 sm:pt-4 lg:pt-3 pb-10 px-4 sm:px-8 lg:px-8 text-center max-w-4xl mx-auto space-y-8 sm:space-y-12">
    <div class="space-y-3">
        <h1 class="text-6xl sm:text-9xl font-bold tracking-tight text-[#1d1d1f] truncate px-2">
            {{ __('exereto') }}
        </h1>
        <p class="app-text-body text-[#515154] max-w-2xl mx-auto font-normal px-2">
            {{ __('Быстрый поиск по официальным каталогам брендов в Греции.') }}
        </p>
    </div>

    <div class="max-w-3xl mx-auto relative" 
         x-data="{ 
            query: '', 
            suggestions: [], 
            open: false,
            loadingSuggestions: false,
            abortCtrl: null,
            fetchSuggestions() {
                if (this.query.trim().length < 2) {
                    this.suggestions = [];
                    this.open = false;
                    return;
                }
                if (this.abortCtrl) this.abortCtrl.abort();
                this.abortCtrl = new AbortController();
                this.loadingSuggestions = true;

                fetch(`{{ route('api.autocomplete') }}?q=${encodeURIComponent(this.query.trim())}`, {
                    signal: this.abortCtrl.signal,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(res => res.json())
                    .then(data => {
                        this.suggestions = data.suggestions || [];
                        this.open = this.suggestions.length > 0;
                        this.loadingSuggestions = false;
                    })
                    .catch(err => {
                        if (err.name !== 'AbortError') {
                            this.suggestions = [];
                            this.open = false;
                            this.loadingSuggestions = false;
                        }
                    });
            },
            selectSuggestion(item) {
                this.query = item.label || item.value;
                this.open = false;
                this.$dispatch('search-loading');
                if (item.url) {
                    window.location.href = item.url;
                    return;
                }
                window.location.href = `{{ route('product-search') }}?q=${encodeURIComponent(item.value)}`;
            }
         }"
         @click.outside="open = false"
         @scroll.window="open = false"
    >
        <form action="{{ route('product-search') }}" method="GET" class="relative">
            <div class="apple-search-bar flex items-center justify-between">
                <input 
                    type="text" 
                    name="q" 
                    x-model="query"
                    @focus="if(suggestions.length > 0) open = true"
                    @input.debounce.180ms="fetchSuggestions()"
                    placeholder="{{ __('Поиск...') }}" 
                    required
                    maxlength="200"
                    autocomplete="off"
                    spellcheck="false"
                    class="apple-search-input flex-1 bg-transparent border-none focus:outline-none text-base sm:text-lg"
                >
                <button type="submit" aria-label="{{ __('Найти') }}" class="p-2 text-[#0071e3] hover:opacity-85 transition-opacity cursor-pointer shrink-0">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </div>
        </form>

        <div x-show="open" 
             x-cloak
             x-transition:enter="transition cubic-bezier-apple duration-300"
             x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition cubic-bezier-apple duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
             class="absolute left-0 right-0 mt-2 bg-white/95 backdrop-blur-xl rounded-2xl shadow-2xl border border-black/[0.08] overflow-hidden z-50 text-left"
        >
            <template x-for="item in suggestions" :key="item.url || item.value">
                <div @click="selectSuggestion(item)" class="px-5 py-3 hover:bg-black/[0.04] flex items-center justify-between gap-3 cursor-pointer transition-colors border-b border-black/[0.03] last:border-b-0">
                    <div class="flex items-center gap-3 min-w-0">
                        <img x-show="item.image" :src="item.image" alt="" class="w-9 h-9 rounded-lg object-cover bg-[#f5f5f7] shrink-0">
                        <span class="app-text-body font-medium text-[#1d1d1f] truncate" x-text="item.label"></span>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <span x-show="item.price" class="text-xs font-semibold text-[#1d1d1f]" x-text="item.price"></span>
                        <span class="text-xs px-2.5 py-1 bg-[#0071e3]/10 text-[#0071e3] rounded-full font-semibold" x-text="item.badge"></span>
                    </div>
                </div>
            </template>
        </div>
    </div>
</section>