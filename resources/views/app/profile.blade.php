<x-layouts.app title="Мой профиль | Buyer Greece">
    <div class="max-w-4xl mx-auto space-y-6">

        <!-- HEADER -->
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Настройки профиля</h1>
            <p class="text-sm text-slate-500 mt-1">Управляйте личными данными и персональным кодом Camex для доставки в Грузию.</p>
        </div>

        <!-- SUCCESS ALERT -->
        @if (session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-medium flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST" class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-sm space-y-6">
            @csrf

            <!-- SECTION 1: PERSONAL DETAILS -->
            <div>
                <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 mb-4">Личная информация</h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-xs font-semibold text-slate-700 mb-1">
                            Имя <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="first_name" 
                            id="first_name" 
                            value="{{ old('first_name', $customer->first_name ?? $user->name) }}" 
                            required 
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500"
                        >
                        @error('first_name')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="last_name" class="block text-xs font-semibold text-slate-700 mb-1">
                            Фамилия <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="last_name" 
                            id="last_name" 
                            value="{{ old('last_name', $customer->last_name ?? '') }}" 
                            required 
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500"
                        >
                        @error('last_name')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-500 mb-1">
                            Email (не меняется)
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            value="{{ $user->email }}" 
                            disabled 
                            class="w-full rounded-lg border-slate-200 bg-slate-50 text-slate-500 text-sm cursor-not-allowed"
                        >
                    </div>

                    <div>
                        <label for="phone" class="block text-xs font-semibold text-slate-700 mb-1">
                            Телефон для связи <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="phone" 
                            id="phone" 
                            value="{{ old('phone', $customer->phone ?? '') }}" 
                            required 
                            placeholder="+995 5xx xx xx xx" 
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500"
                        >
                        @error('phone')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- SECTION 2: CAMEX INTEGRATION & DELIVERY ADDRESS -->
            <div>
                <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 mb-4">Доставка в Грузию (Camex)</h2>
                
                <div class="space-y-4">
                    <div>
                        <label for="camex_personal_number" class="block text-xs font-semibold text-slate-700 mb-1">
                            Персональный код Camex (Personal ID / Code)
                        </label>
                        <input 
                            type="text" 
                            name="camex_personal_number" 
                            id="camex_personal_number" 
                            value="{{ old('camex_personal_number', $customer->camex_personal_number ?? '') }}" 
                            placeholder="например: CAMEX-12345" 
                            class="w-full sm:w-1/2 rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500"
                        >
                        <p class="text-[11px] text-slate-500 mt-1">Указывается при отправке посылок со склада в Греции в филиал Camex в Тбилиси/Батуми.</p>
                        @error('camex_personal_number')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div>
                            <label for="city" class="block text-xs font-medium text-slate-700 mb-1">Город в Грузии</label>
                            <input 
                                type="text" 
                                name="city" 
                                id="city" 
                                value="{{ old('city', $customer->city ?? 'Тбилиси') }}" 
                                placeholder="Тбилиси, Батуми, Кутаиси..." 
                                class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500"
                            >
                        </div>

                        <div>
                            <label for="address" class="block text-xs font-medium text-slate-700 mb-1">Адрес получателя</label>
                            <input 
                                type="text" 
                                name="address" 
                                id="address" 
                                value="{{ old('address', $customer->address ?? '') }}" 
                                placeholder="Улица, дом, квартира" 
                                class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500"
                            >
                        </div>
                    </div>
                </div>
            </div>

            <!-- SUBMIT BUTTON -->
            <div class="pt-4 border-t border-slate-100 flex justify-end">
                <x-buttons.primary type="submit">
                    Сохранить изменения
                </x-buttons.primary>
            </div>

        </form>
    </div>
</x-layouts.app>