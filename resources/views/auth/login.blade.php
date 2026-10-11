<x-layouts.public title="{{ __('Вход в кабинет | Buyer Greece') }}">
    <div class="min-h-[calc(100vh-16rem)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-[#f2f2f2]">
        <div class="max-w-md w-full space-y-6 bg-white/90 backdrop-blur-xl p-8 sm:p-10 border border-black/[0.08] rounded-[2.5rem] shadow-[0_20px_40px_rgba(0,0,0,0.06)]">
            <div class="text-center space-y-2">
                <h1 class="text-2xl font-semibold text-[#1d1d1f] tracking-tight">{{ __('Вход в личный кабинет') }}</h1>
                <p class="text-xs text-[#86868b]">{{ __('Введите ваш email и пароль для доступа к заказам') }}</p>
            </div>

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-semibold text-[#1d1d1f] mb-1.5">{{ __('Email адрес') }}</label>
                    <input type="email" name="email" id="email" required autofocus class="w-full px-4 py-2.5 rounded-xl bg-black/[0.03] border border-black/[0.08] text-sm text-[#1d1d1f] focus:bg-white focus:border-[#0071e3] focus:ring-4 focus:ring-[#0071e3]/10 transition-all duration-200 outline-none">
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-[#1d1d1f] mb-1.5">{{ __('Пароль') }}</label>
                    <input type="password" name="password" id="password" required class="w-full px-4 py-2.5 rounded-xl bg-black/[0.03] border border-black/[0.08] text-sm text-[#1d1d1f] focus:bg-white focus:border-[#0071e3] focus:ring-4 focus:ring-[#0071e3]/10 transition-all duration-200 outline-none">
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 text-[#86868b] cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-black/20 text-[#0071e3] focus:ring-[#0071e3]/20 w-4 h-4">
                        <span>{{ __('Запомнить меня') }}</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3 bg-[#0071e3] hover:bg-[#0077ed] active:scale-[0.98] text-white font-medium text-sm rounded-full transition-all duration-200 shadow-sm cursor-pointer">
                    {{ __('Войти в кабинет') }}
                </button>
            </form>

            <div class="text-center pt-4 border-t border-black/[0.06] text-xs text-[#86868b]">
                {{ __('Ещё нет аккаунта?') }} 
                <a href="{{ route('register') }}" class="font-semibold text-[#0071e3] hover:underline">{{ __('Зарегистрироваться') }}</a>
            </div>
        </div>
    </div>
</x-layouts.public>