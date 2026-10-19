<x-layouts.public title="{{ __('Вход в кабинет | Buyer Greece') }}">
    <div class="min-h-[calc(100vh-16rem)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-[#f2f2f2]">
        <div class="max-w-md w-full space-y-6 apple-card">
            <div class="text-center space-y-2">
                <h1 class="app-text-title">{{ __('Вход в личный кабинет') }}</h1>
                <p class="app-text-sub">{{ __('Введите ваш email и пароль для доступа к заказам') }}</p>
            </div>

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block app-text-sub font-semibold mb-1.5">{{ __('Email адрес') }}</label>
                    <input type="email" name="email" id="email" required autofocus class="w-full px-4 py-2.5 rounded-xl bg-black/[0.03] border border-black/[0.08] app-text-body focus:bg-white focus:border-[#0071e3] focus:ring-4 focus:ring-[#0071e3]/10 transition-all duration-200 outline-none">
                </div>

                <div>
                    <label for="password" class="block app-text-sub font-semibold mb-1.5">{{ __('Пароль') }}</label>
                    <input type="password" name="password" id="password" required class="w-full px-4 py-2.5 rounded-xl bg-black/[0.03] border border-black/[0.08] app-text-body focus:bg-white focus:border-[#0071e3] focus:ring-4 focus:ring-[#0071e3]/10 transition-all duration-200 outline-none">
                </div>

                <div class="flex items-center justify-between app-text-sub">
                    <label class="flex items-center gap-2 text-[#86868b] cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-black/20 text-[#0071e3] focus:ring-[#0071e3]/20 w-4 h-4">
                        <span>{{ __('Запомнить меня') }}</span>
                    </label>
                </div>

                <button type="submit" class="apple-btn-primary w-full">
                    {{ __('Войти в кабинет') }}
                </button>
            </form>

            <div class="text-center pt-4 border-t border-black/[0.06] app-text-sub">
                {{ __('Ещё нет аккаунта?') }} 
                <a href="{{ route('register') }}" class="font-semibold text-[#0071e3] hover:underline">{{ __('Зарегистрироваться') }}</a>
            </div>
        </div>
    </div>
</x-layouts.public>