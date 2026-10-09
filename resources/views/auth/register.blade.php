<x-layouts.public title="Регистрация | Buyer Greece">
    <div class="min-h-[calc(100vh-16rem)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-slate-50">
        <div class="max-w-md w-full space-y-6 bg-white p-8 border border-slate-200 rounded-2xl shadow-sm">
            <div class="text-center">
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Создание аккаунта</h1>
                <p class="text-xs text-slate-500 mt-2">Зарегистрируйтесь для отправки заявок и управления заказами</p>
            </div>

            <form action="{{ route('register') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">
                        Имя и фамилия <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        value="{{ old('name') }}" 
                        required 
                        autofocus 
                        placeholder="Иван Иванов" 
                        class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500"
                    >
                    @error('name')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">
                        Email адрес <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        value="{{ old('email') }}" 
                        required 
                        placeholder="example@mail.com" 
                        class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500"
                    >
                    @error('email')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-xs font-semibold text-slate-700 mb-1">
                        Номер телефона (необязательно)
                    </label>
                    <input 
                        type="tel" 
                        name="phone" 
                        id="phone" 
                        value="{{ old('phone') }}" 
                        placeholder="+995 5xx xx xx xx" 
                        class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500"
                    >
                    @error('phone')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">
                        Пароль <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        required 
                        placeholder="Минимум 8 символов" 
                        class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500"
                    >
                    @error('password')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="text-[11px] text-slate-500 leading-relaxed pt-1">
                    Нажимая кнопку «Зарегистрироваться», вы принимаете условия
                    <a href="/terms" class="text-brand-600 hover:underline">Пользовательского соглашения</a> и 
                    <a href="/privacy" class="text-brand-600 hover:underline">Политики конфиденциальности</a>.
                </div>

                <x-buttons.primary type="submit" size="default" class="w-full justify-center">
                    Зарегистрироваться
                </x-buttons.primary>
            </form>

            <div class="text-center pt-4 border-t border-slate-100 text-xs text-slate-600">
                Уже есть аккаунт? 
                <a href="{{ route('login') }}" class="font-bold text-brand-600 hover:underline">Войти</a>
            </div>
        </div>
    </div>
</x-layouts.public>