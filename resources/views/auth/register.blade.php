<x-layouts.public title="{{ __('Регистрация | Buyer Greece') }}">
    <div class="min-h-[calc(100vh-16rem)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-[#f2f2f2]">
        <div class="max-w-md w-full space-y-6 apple-card">
            <div class="text-center space-y-2">
                <h1 class="app-text-title">{{ __('Создание аккаунта') }}</h1>
                <p class="app-text-sub">{{ __('Зарегистрируйтесь для отправки заявок и управления заказами') }}</p>
            </div>

            <form action="{{ route('register') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="name" class="block app-text-sub font-semibold mb-1.5">
                        {{ __('Имя и фамилия') }} <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        value="{{ old('name') }}" 
                        required 
                        autofocus 
                        placeholder="Иван Иванов" 
                        class="w-full px-4 py-2.5 rounded-xl bg-black/[0.03] border border-black/[0.08] app-text-body focus:bg-white focus:border-[#0071e3] focus:ring-4 focus:ring-[#0071e3]/10 transition-all duration-200 outline-none"
                    >
                    @error('name')
                        <p class="app-text-sub text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block app-text-sub font-semibold mb-1.5">
                        {{ __('Email адрес') }} <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        value="{{ old('email') }}" 
                        required 
                        placeholder="example@mail.com" 
                        class="w-full px-4 py-2.5 rounded-xl bg-black/[0.03] border border-black/[0.08] app-text-body focus:bg-white focus:border-[#0071e3] focus:ring-4 focus:ring-[#0071e3]/10 transition-all duration-200 outline-none"
                    >
                    @error('email')
                        <p class="app-text-sub text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block app-text-sub font-semibold mb-1.5">
                        {{ __('Номер телефона (необязательно)') }}
                    </label>
                    <input 
                        type="tel" 
                        name="phone" 
                        id="phone" 
                        value="{{ old('phone') }}" 
                        placeholder="+995 5xx xx xx xx" 
                        class="w-full px-4 py-2.5 rounded-xl bg-black/[0.03] border border-black/[0.08] app-text-body focus:bg-white focus:border-[#0071e3] focus:ring-4 focus:ring-[#0071e3]/10 transition-all duration-200 outline-none"
                    >
                    @error('phone')
                        <p class="app-text-sub text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block app-text-sub font-semibold mb-1.5">
                        {{ __('Пароль') }} <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        required 
                        placeholder="Минимум 8 символов" 
                        class="w-full px-4 py-2.5 rounded-xl bg-black/[0.03] border border-black/[0.08] app-text-body focus:bg-white focus:border-[#0071e3] focus:ring-4 focus:ring-[#0071e3]/10 transition-all duration-200 outline-none"
                    >
                    @error('password')
                        <p class="app-text-sub text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="text-[11px] text-[#86868b] leading-relaxed pt-1">
                    {{ __('Нажимая кнопку «Зарегистрироваться», вы принимаете условия') }}
                    <a href="/terms" class="text-[#0071e3] hover:underline">{{ __('Пользовательского соглашения') }}</a> {{ __('и') }} 
                    <a href="/privacy" class="text-[#0071e3] hover:underline">{{ __('Политики конфиденциальности') }}</a>.
                </div>

                <button type="submit" class="apple-btn-primary w-full">
                    {{ __('Зарегистрироваться') }}
                </button>
            </form>

            <div class="text-center pt-4 border-t border-black/[0.06] app-text-sub">
                {{ __('Уже есть аккаунт?') }} 
                <a href="{{ route('login') }}" class="font-semibold text-[#0071e3] hover:underline">{{ __('Войти') }}</a>
            </div>
        </div>
    </div>
</x-layouts.public>