<x-layouts.public title="{{ __('Восстановление пароля') }}">
    <div class="min-h-[calc(100vh-16rem)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-[#f5f5f7]">
        <div class="max-w-md w-full space-y-6 apple-card">
            <div class="text-center space-y-2">
                <h1 class="app-text-title">{{ __('Восстановление пароля') }}</h1>
                <p class="app-text-sub">{{ __('Введите ваш email, и мы отправим вам ссылку для сброса пароля') }}</p>
            </div>

            @if (session('status'))
                <div class="p-3 text-sm text-green-700 bg-green-100 rounded-xl">
                    {{ session('status') }}
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block app-text-sub font-semibold mb-1.5">{{ __('Email адрес') }}</label>
                    <input type="email" name="email" id="email" required autofocus class="w-full px-4 py-2.5 rounded-xl bg-black/[0.03] border border-black/[0.08] app-text-body focus:bg-white focus:border-[#0071e3] focus:ring-4 focus:ring-[#0071e3]/10 transition-all duration-200 outline-none">
                </div>

                <button type="submit" class="apple-btn-primary w-full">
                    {{ __('Отправить ссылку сброса') }}
                </button>
            </form>

            <div class="text-center pt-4 border-t border-black/[0.06] app-text-sub">
                <a href="{{ route('login') }}" class="font-semibold text-[#0071e3] hover:underline">{{ __('Вернуться к входу') }}</a>
            </div>
        </div>
    </div>
</x-layouts.public>