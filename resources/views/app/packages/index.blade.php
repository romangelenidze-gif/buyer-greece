<x-layouts.app title="{{ __('Мои посылки') }} | Buyer Greece">

    <div class="space-y-6" x-data="{ tab: 'expected' }">
        
        <!-- HEADER -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="apple-section-title text-left">{{ __('Посылки на складе') }}</h1>
                <p class="apple-section-subtitle text-left">{{ __('Отслеживайте поступающие покупки и их готовность к отправке в Грузию.') }}</p>
            </div>
            
            <a href="{{ route('forwarding') }}" class="px-4 py-2 app-text-sub font-medium text-[#0071e3] bg-blue-50/70 hover:bg-blue-100/70 rounded-full transition-colors border border-blue-200/60 inline-flex items-center gap-1">
                <span>{{ __('Инструкция по адресу приема') }}</span>
                <span>→</span>
            </a>
        </div>

        <!-- TABS TOGGLE (Apple Segmented Control) -->
        <div class="flex flex-wrap gap-1.5 bg-black/[0.04] p-1.5 rounded-full max-w-md border border-black/[0.04]">
            <button 
                type="button"
                @click="tab = 'expected'" 
                :class="tab === 'expected' ? 'bg-white text-[#1d1d1f] shadow-[0_2px_8px_rgba(0,0,0,0.08)] font-medium' : 'text-[#86868b] hover:text-[#1d1d1f]'"
                class="flex-1 px-4 py-2 rounded-full app-text-sub transition-all duration-200 cursor-pointer flex items-center justify-center gap-2"
            >
                <span>{{ __('Ожидаются') }}</span>
                <span class="px-2 py-0.5 rounded-full app-badge-text bg-black/[0.05] text-[#1d1d1f]">
                    {{ $expectedPackages->count() }}
                </span>
            </button>

            <button 
                type="button"
                @click="tab = 'received'" 
                :class="tab === 'received' ? 'bg-white text-[#1d1d1f] shadow-[0_2px_8px_rgba(0,0,0,0.08)] font-medium' : 'text-[#86868b] hover:text-[#1d1d1f]'"
                class="flex-1 px-4 py-2 rounded-full app-text-sub transition-all duration-200 cursor-pointer flex items-center justify-center gap-2"
            >
                <span>{{ __('Получены в Греции') }}</span>
                <span class="px-2 py-0.5 rounded-full app-badge-text bg-black/[0.05] text-[#1d1d1f]">
                    {{ $receivedPackages->count() }}
                </span>
            </button>
        </div>

        <!-- TAB 1: EXPECTED PACKAGES -->
        <div x-show="tab === 'expected'" class="space-y-4">
            @forelse($expectedPackages as $package)
                <div class="apple-card space-y-3">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 border-b border-black/[0.06] pb-3">
                        <div>
                            <span class="app-text-sub font-mono text-[#86868b]">{{ __('Трек-номер магазина:') }}</span>
                            <p class="font-mono font-semibold text-[#1d1d1f] app-text-title">
                                {{ $package->tracking_number ?? __('Ожидает присвоения') }}
                            </p>
                        </div>
                        <x-status-badge :status="$package->status" label="{{ __('Ожидается складом') }}" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 app-text-sub">
                        <div>
                            <span class="text-[#86868b] block">{{ __('Магазин:') }}</span>
                            <span class="font-semibold text-[#1d1d1f]">{{ $package->store_name ?? '—' }}</span>
                        </div>
                        <div>
                            <span class="text-[#86868b] block">{{ __('Содержимое:') }}</span>
                            <span class="font-semibold text-[#1d1d1f]">{{ $package->description ?? __('Посылка') }}</span>
                        </div>
                        <div>
                            <span class="text-[#86868b] block">{{ __('Ожидаемая дата:') }}</span>
                            <span class="font-semibold text-[#1d1d1f]">
                                {{ $package->expected_at ? $package->expected_at->format('d.m.Y') : '—' }}
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="apple-card text-center py-12 app-text-sub text-[#86868b]">
                    {{ __('Ожидаемых поступлений на склад пока нет.') }}
                </div>
            @endforelse
        </div>

        <!-- TAB 2: RECEIVED PACKAGES -->
        <div x-show="tab === 'received'" x-cloak class="space-y-4">
            @forelse($receivedPackages as $package)
                <div class="apple-card space-y-4">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 border-b border-black/[0.06] pb-3">
                        <div>
                            <span class="app-text-sub font-mono text-[#86868b]">{{ __('Код посылки') }}</span>
                            <p class="font-mono font-semibold text-[#1d1d1f] app-text-title">
                                {{ $package->package_code ?? 'PKG-' . $package->id }}
                            </p>
                        </div>
                        <x-status-badge :status="$package->status" label="{{ __('Принято на складе GR') }}" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 app-text-sub">
                        <div>
                            <span class="text-[#86868b] block">{{ __('Дата приема:') }}</span>
                            <span class="font-semibold text-[#1d1d1f]">
                                {{ $package->received_at ? $package->received_at->format('d.m.Y') : $package->created_at->format('d.m.Y') }}
                            </span>
                        </div>
                        <div>
                            <span class="text-[#86868b] block">{{ __('Вес:') }}</span>
                            <span class="font-semibold text-[#1d1d1f] font-mono">
                                {{ $package->weight ? number_format($package->weight, 2) . ' ' . __('кг') : __('Уточняется') }}
                            </span>
                        </div>
                        <div>
                            <span class="text-[#86868b] block">{{ __('Фотофиксация:') }}</span>
                            @if($package->photo_path)
                                <a href="{{ route('packages.photo', $package->id) }}" target="_blank" class="text-[#0071e3] font-semibold hover:underline inline-flex items-center gap-1 mt-0.5">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ __('Открыть фото посылки') }}
                                </a>
                            @else
                                <span class="text-[#86868b] mt-0.5 block">{{ __('Фото отсутствует') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="p-3 bg-blue-50/70 border border-blue-200/60 rounded-2xl app-text-sub text-blue-900">
                        {{ __('Посылка проверена и взвешена. В ближайшее время она будет включена в очередную партию отправки Camex.') }}
                    </div>
                </div>
            @empty
                <div class="apple-card text-center py-12 app-text-sub text-[#86868b]">
                    {{ __('На складе пока нет полученных посылок.') }}
                </div>
            @endforelse
        </div>

    </div>

</x-layouts.app>