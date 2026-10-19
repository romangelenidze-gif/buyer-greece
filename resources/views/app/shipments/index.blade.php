<x-layouts.app title="{{ __('Отправки Camex') }} | Buyer Greece">

    <div class="space-y-6">
        
        <div>
            <h1 class="apple-section-title text-left">{{ __('История отправок в Грузию') }}[cite: 19]</h1>
            <p class="apple-section-subtitle text-left">{{ __('Реестр партий, переданных международному перевозчику Camex.') }}[cite: 19]</p>
        </div>

        <!-- SHIPMENT CARDS -->
        <div class="space-y-6">
            @forelse($shipments as $shipment)
                <div class="apple-card space-y-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 border-b border-black/[0.06] pb-4">
                        <div>
                            <span class="text-xs font-mono text-[#86868b]">{{ __('Партия отправки') }}[cite: 19]</span>
                            <h2 class="text-base font-semibold text-[#1d1d1f] font-mono">
                                {{ $shipment->shipment_number ?? 'SHP-' . $shipment->id }}[cite: 19]
                            </h2>
                        </div>
                        <x-status-badge :status="$shipment->status" label="{{ __('Передано в Camex') }}" />[cite: 19]
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                        <div>
                            <span class="text-[#86868b] block">{{ __('Дата передачи Camex:') }}[cite: 19]</span>
                            <span class="font-semibold text-[#1d1d1f]">
                                {{ $shipment->shipped_at ? $shipment->shipped_at->format('d.m.Y') : $shipment->created_at->format('d.m.Y') }}[cite: 19]
                            </span>
                        </div>
                        <div>
                            <span class="text-[#86868b] block">{{ __('Общий вес партии:') }}[cite: 19]</span>
                            <span class="font-semibold text-[#1d1d1f] font-mono">
                                {{ $shipment->total_weight ? number_format($shipment->total_weight, 2) . ' ' . __('кг') : '—' }}[cite: 19]
                            </span>
                        </div>
                        <div>
                            <span class="text-[#86868b] block">{{ __('Трек-номер Camex:') }}[cite: 19]</span>
                            <span class="font-semibold text-[#1d1d1f] font-mono text-sm">
                                {{ $shipment->camex_tracking_number ?? __('Присваивается') }}[cite: 19]
                            </span>
                        </div>
                    </div>

                    <!-- DISCLAIMER -->
                    <div class="p-4 bg-[#1d1d1f] text-white rounded-2xl text-xs space-y-2">
                        <div class="flex items-center gap-2 text-white font-semibold">
                            <svg class="w-4 h-4 text-[#0071e3]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ __('Передача ответственности перевозчику') }}[cite: 19]</span>
                        </div>
                        <p class="leading-relaxed text-gray-400">
                            {{ __('Ваша посылка успешно передана логистической компании Camex. Дальнейшая международная транспортировка, таможенное оформление и выдача в Грузии осуществляются службой Camex. Оплата международного тарифа производится при получении.') }}[cite: 19]
                        </p>
                    </div>
                </div>
            @empty
                <div class="apple-card text-center py-12 text-xs text-[#86868b]">
                    {{ __('У вас пока нет зарегистрированных отправок в Грузию.') }}[cite: 19]
                </div>
            @endforelse
        </div>

    </div>

</x-layouts.app>