<x-layouts.app title="{{ __('Мой профиль') }} | Buyer Greece">
    <div class="max-w-4xl mx-auto space-y-6">

        <div>
            <h1 class="apple-section-title">{{ __('Настройки профиля') }}</h1>
            <p class="apple-section-subtitle">{{ __('Управляйте личными данными и персональным кодом Camex для доставки в Грузию.') }}</p>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" class="apple-card space-y-6">
            @csrf

            <!-- SECTION 1: PERSONAL DETAILS -->
            <div>
                <h2 class="text-base font-semibold text-[#1d1d1f] border-b border-black/[0.06] pb-3 mb-4">{{ __('Личная информация') }}</h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-xs font-semibold text-[#1d1d1f] mb-1.5">
                            {{ __('Имя') }} <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="first_name" 
                            id="first_name" 
                            value="{{ old('first_name', $customer->first_name ?? $user->name) }}" 
                            required 
                            class="apple-search-input !px-4 !py-3 !rounded-2xl"
                        >
                        @error('first_name')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="last_name" class="block text-xs font-semibold text-[#1d1d1f] mb-1.5">
                            {{ __('Фамилия') }} <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="last_name" 
                            id="last_name" 
                            value="{{ old('last_name', $customer->last_name ?? '') }}" 
                            required 
                            class="apple-search-input !px-4 !py-3 !rounded-2xl"
                        >
                        @error('last_name')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-medium text-[#86868b] mb-1.5">
                            {{ __('Email (не меняется)') }}
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            value="{{ $user->email }}" 
                            disabled 
                            class="apple-search-input !px-4 !py-3 !rounded-2xl !bg-black/[0.03] cursor-not-allowed text-[#86868b]"
                        >
                    </div>

                    <div>
                        <label for="phone" class="block text-xs font-semibold text-[#1d1d1f] mb-1.5">
                            {{ __('Телефон для связи') }} <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="phone" 
                            id="phone" 
                            value="{{ old('phone', $customer->phone ?? '') }}" 
                            required 
                            placeholder="+995 5xx xx xx xx" 
                            class="apple-search-input !px-4 !py-3 !rounded-2xl"
                        >
                        @error('phone')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- SECTION 2: CAMEX INTEGRATION -->
            <div>
                <h2 class="text-base font-semibold text-[#1d1d1f] border-b border-black/[0.06] pb-3 mb-4">{{ __('Доставка в Грузию (Camex)') }}</h2>
                
                <div class="space-y-4">
                    <div>
                        <label for="camex_personal_number" class="block text-xs font-semibold text-[#1d1d1f] mb-1.5">
                            {{ __('Персональный код Camex (Personal ID / Code)') }}
                        </label>
                        <input 
                            type="text" 
                            name="camex_personal_number" 
                            id="camex_personal_number" 
                            value="{{ old('camex_personal_number', $customer->camex_personal_number ?? '') }}" 
                            placeholder="{{ __('например: CAMEX-12345') }}" 
                            class="apple-search-input !px-4 !py-3 !rounded-2xl w-full sm:w-1/2"
                        >
                        <p class="text-xs text-[#86868b] mt-1.5">{{ __('Указывается при отправке посылок со склада в Греции в филиал Camex в Тбилиси/Батуми.') }}</p>
                        @error('camex_personal_number')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div>
                            <label for="city" class="block text-xs font-medium text-[#86868b] mb-1.5">{{ __('Город в Грузии') }}</label>
                            <input 
                                type="text" 
                                name="city" 
                                id="city" 
                                value="{{ old('city', $customer->city ?? 'Тбилиси') }}" 
                                placeholder="{{ __('Тбилиси, Батуми, Кутаиси...') }}" 
                                class="apple-search-input !px-4 !py-3 !rounded-2xl"
                            >
                        </div>

                        <div>
                            <label for="address" class="block text-xs font-medium text-[#86868b] mb-1.5">{{ __('Адрес получателя') }}</label>
                            <input 
                                type="text" 
                                name="address" 
                                id="address" 
                                value="{{ old('address', $customer->address ?? '') }}" 
                                placeholder="{{ __('Улица, дом, квартира') }}" 
                                class="apple-search-input !px-4 !py-3 !rounded-2xl"
                            >
                        </div>
                    </div>
                </div>
            </div>

            <!-- SUBMIT BUTTON -->
            <div class="pt-4 border-t border-black/[0.06] flex justify-end">
                <button type="submit" class="apple-search-button !relative !right-auto !top-auto !transform-none px-8 py-3 text-sm font-medium cursor-pointer">
                    {{ __('Сохранить изменения') }}
                </button>
            </div>

        </form>
    </div>
</x-layouts.app>