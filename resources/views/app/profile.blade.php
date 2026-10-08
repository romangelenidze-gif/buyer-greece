<x-layouts.app title="Мой профиль | Buyer Greece">

    <div class="max-w-3xl mx-auto space-y-6">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Профиль пользователя</h1>
            <p class="text-xs text-slate-500 mt-1">Укажите ваш персональный номер Camex для активации греческого адреса форвардинга.</p>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-6">
            @csrf

            <!-- CAMEX PERSONAL NUMBER FIELD -->
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 space-y-3">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    <h2 class="text-sm font-bold text-amber-900">Персональный код Camex</h2>
                </div>
                
                <div>
                    <label for="camex_personal_number" class="block text-xs font-semibold text-slate-700 mb-1">
                        Camex Personal Number (например, CX-98765) <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="camex_personal_number" 
                        id="camex_personal_number" 
                        value="{{ old('camex_personal_number', Auth::user()->camex_personal_number ?? '') }}"
                        placeholder="CX-XXXXX" 
                        required
                        class="w-full sm:w-1/2 rounded-lg border-slate-300 text-sm font-mono focus:border-brand-500 focus:ring-brand-500"
                    >
                    @error('camex_personal_number')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <p class="text-[11px] text-amber-800">
                    Номер необходим для маркировки ваших посылок при передаче международному перевозчику Camex.
                </p>
            </div>

            <!-- PERSONAL CONTACT DETAILS -->
            <div class="space-y-4">
                <h2 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2">Личные данные</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-xs font-medium text-slate-700 mb-1">Имя и Фамилия</label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            value="{{ old('name', Auth::user()->name ?? '') }}" 
                            required 
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500"
                        >
                    </div>

                    <div>
                        <label for="phone" class="block text-xs font-medium text-slate-700 mb-1">Номер телефона</label>
                        <input 
                            type="text" 
                            name="phone" 
                            id="phone" 
                            value="{{ old('phone', Auth::user()->phone ?? '') }}" 
                            placeholder="+995 5XX XX XX XX" 
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500"
                        >
                    </div>

                    <div class="sm:col-span-2">
                        <label for="email" class="block text-xs font-medium text-slate-700 mb-1">Email адрес</label>
                        <input 
                            type="email" 
                            id="email" 
                            value="{{ Auth::user()->email ?? '' }}" 
                            disabled 
                            class="w-full rounded-lg border-slate-200 bg-slate-50 text-slate-500 text-sm cursor-not-allowed"
                        >
                    </div>
                </div>
            </div>

            <div class="pt-2 border-t border-slate-100 flex justify-end">
                <x-buttons.primary type="submit" size="default">
                    Сохранить изменения
                </x-buttons.primary>
            </div>
        </form>
    </div>

</x-layouts.app>