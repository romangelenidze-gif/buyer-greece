<x-layouts.app title="Отправки Camex | Buyer Greece">

    <div class="space-y-6">
        
        <div>
            <h1 class="text-xl font-bold text-slate-900">История отправок в Грузию</h1>
            <p class="text-xs text-slate-500 mt-1">Реестр партий, переданных международному перевозчику Camex.</p>
        </div>

        <!-- SHIPMENT CARDS -->
        <div class="space-y-6">
            @forelse($shipments as $shipment)
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 border-b border-slate-100 pb-4">
                        <div>
                            <span class="text-xs font-mono text-slate-400">Партия отправки</span>
                            <h2 class="text-base font-extrabold text-slate-900 font-mono">
                                {{ $shipment->shipment_number ?? 'SHP-' . $shipment->id }}
                            </h2>
                        </div>
                        <x-status-badge :status="$shipment->status" label="Передано в Camex" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                        <div>
                            <span class="text-slate-400 block">Дата передачи Camex:</span>
                            <span class="font-bold text-slate-800">
                                {{ $shipment->shipped_at ? $shipment->shipped_at->format('d.m.Y') : $shipment->created_at->format('d.m.Y') }}
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Общий вес партии:</span>
                            <span class="font-bold text-slate-800 font-mono">
                                {{ $shipment->total_weight ? number_format($shipment->total_weight, 2) . ' кг' : '—' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Трек-номер Camex:</span>
                            <span class="font-bold text-slate-900 font-mono text-sm">
                                {{ $shipment->camex_tracking_number ?? 'Присваивается' }}
                            </span>
                        </div>
                    </div>

                    <!-- RESPONSIBILITY DISCLAIMER BANNER -->
                    <div class="p-4 bg-slate-900 text-slate-300 rounded-xl text-xs space-y-2">
                        <div class="flex items-center gap-2 text-white font-bold">
                            <svg class="w-4 h-4 text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Передача ответственности перевозчику</span>
                        </div>
                        <p class="leading-relaxed text-slate-400">
                            Ваша посылка успешно передана логистической компании Camex. Дальнейшая международная транспортировка, таможенное оформление и выдача в Грузии осуществляются службой Camex. Оплата международного тарифа производится при получении.
                        </p>
                    </div>
                </div>
            @empty
                <div class="bg-white border border-slate-200 rounded-2xl p-8 text-center text-xs text-slate-500">
                    У вас пока нет зарегистрированных отправок в Грузию.
                </div>
            @endforelse
        </div>

    </div>

</x-layouts.app>