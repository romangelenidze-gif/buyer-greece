<x-layouts.app title="Мои посылки | Buyer Greece">

    <div class="space-y-6" x-data="{ tab: 'expected' }">
        
        <!-- HEADER -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-900">Посылки на складе</h1>
                <p class="text-xs text-slate-500 mt-1">Отслеживайте поступающие покупки и их готовность к отправке в Грузию.</p>
            </div>
            
            <a href="{{ route('forwarding') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700 bg-brand-50 px-3 py-2 rounded-lg border border-brand-100">
                Инструкция по адресу приема →
            </a>
        </div>

        <!-- TABS TOGGLE -->
        <div class="flex border-b border-slate-200 gap-6 text-sm font-semibold">
            <button 
                @click="tab = 'expected'" 
                :class="tab === 'expected' ? 'border-brand-600 text-brand-600 border-b-2 pb-3' : 'text-slate-500 hover:text-slate-800 pb-3'"
                class="transition-colors flex items-center gap-2"
            >
                <span>Ожидаются</span>
                <span class="px-2 py-0.5 rounded-full text-xs bg-slate-100 text-slate-600" x-text="'1'"></span>
            </button>

            <button 
                @click="tab = 'received'" 
                :class="tab === 'received' ? 'border-brand-600 text-brand-600 border-b-2 pb-3' : 'text-slate-500 hover:text-slate-800 pb-3'"
                class="transition-colors flex items-center gap-2"
            >
                <span>Получены в Греции</span>
                <span class="px-2 py-0.5 rounded-full text-xs bg-slate-100 text-slate-600" x-text="'1'"></span>
            </button>
        </div>

        <!-- TAB 1: EXPECTED PACKAGES -->
        <div x-show="tab === 'expected'" class="space-y-4">
            <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-6 shadow-sm space-y-3">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 border-b border-slate-100 pb-3">
                    <div>
                        <span class="text-xs font-mono text-slate-400">Трек-номер магазина:</span>
                        <p class="font-mono font-bold text-slate-900 text-sm">GR-TRACK-998811</p>
                    </div>
                    <x-status-badge status="pending" label="Ожидается складом" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 block">Магазин:</span>
                        <span class="font-semibold text-slate-800">Zara Greece</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Содержимое:</span>
                        <span class="font-semibold text-slate-800">Куртка и шарф</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Ожидаемая дата:</span>
                        <span class="font-semibold text-slate-800">12.10.2026</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: RECEIVED PACKAGES (WITH PHOTO ACCESS) -->
        <div x-show="tab === 'received'" x-cloak class="space-y-4">
            <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-6 shadow-sm space-y-4">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 border-b border-slate-100 pb-3">
                    <div>
                        <span class="text-xs font-mono text-slate-400">Код посылки</span>
                        <p class="font-mono font-bold text-slate-900 text-sm">PKG-2026-0042</p>
                    </div>
                    <x-status-badge status="received" label="Принято на складе GR" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 block">Дата приема:</span>
                        <span class="font-semibold text-slate-800">08.10.2026</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Вес:</span>
                        <span class="font-semibold text-slate-800 font-mono">1.45 кг</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Фотофиксация:</span>
                        <a href="{{ route('packages.photo', 1) }}" target="_blank" class="text-brand-600 font-semibold hover:underline inline-flex items-center gap-1 mt-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Открыть фото посылки
                        </a>
                    </div>
                </div>

                <div class="p-3 bg-brand-50 border border-brand-100 rounded-xl text-xs text-brand-900">
                    Посылка проверена и взвешена. В ближайшее время она будет включена в очередную партию отправки Camex.
                </div>
            </div>
        </div>

    </div>

</x-layouts.app>