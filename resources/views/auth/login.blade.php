<x-layouts.public title="Вход в кабинет | Buyer Greece">
    <div class="min-h-[calc(100vh-16rem)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-slate-50">
        <div class="max-w-md w-full space-y-6 bg-white p-8 border border-slate-200 rounded-2xl shadow-sm">
            <div class="text-center">
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Вход в личный кабинет</h1>
                <p class="text-xs text-slate-500 mt-2">Введите ваш email и пароль для доступа к заказам</p>
            </div>

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Email адрес</label>
                    <input type="email" name="email" id="email" required autofocus class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">Пароль</label>
                    <input type="password" name="password" id="password" required class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 text-slate-600">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                        <span>Запомнить меня</span>
                    </label>
                </div>

                <x-buttons.primary type="submit" size="default" class="w-full">
                    Войти в кабинет
                </x-buttons.primary>
            </form>

            <div class="text-center pt-4 border-t border-slate-100 text-xs text-slate-600">
                Ещё нет аккаунта? 
                <a href="{{ route('register') }}" class="font-bold text-brand-600 hover:underline">Зарегистрироваться</a>
            </div>
        </div>
    </div>
</x-layouts.public>