<x-layouts.public title="{{ __('Новый пароль') }}">
    <div class="min-h-[calc(100vh-16rem)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-[#f5f5f7]">
        <div class="max-w-md w-full space-y-6 apple-card">
            <div class="text-center space-y-2">
                <h1 class="app-text-title">{{ __('Установка нового пароля') }}</h1>
            </div>

            <form action="{{ route('password.update') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label for="email" class="block app-text-sub font-semibold mb-1.5">{{ __('Email адрес') }}</label>
                    <input type="email" name="email" id="email" value="{{ $email ?? old('email') }}" required class="w-full px-4 py-2.5 rounded-xl bg-black/[0.03] border border-black/[0.08] app-text-body focus:bg-white focus:border-[#0071e3] focus:ring-4 focus:ring-[#0071e3]/10 transition-all duration-200 outline-none">
                </div>

                <div>
                    <label for="password" class="block app-text-sub font-semibold mb-1.5">{{ __('Новый пароль') }}</label>
                    <input type="password" name="password" id="password" required class="w-full px-4 py-2.5 rounded-xl bg-black/[0.03] border border-black/[0.08] app-text-body focus:bg-white focus:border-[#0071e3] focus:ring-4 focus:ring-[#0071e3]/10 transition-all duration-200 outline-none">
                </div>

                <div>
                    <label for="password_confirmation" class="block app-text-sub font-semibold mb-1.5">{{ __('Подтверждение пароля') }}</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required class="w-full px-4 py-2.5 rounded-xl bg-black/[0.03] border border-black/[0.08] app-text-body focus:bg-white focus:border-[#0071e3] focus:ring-4 focus:ring-[#0071e3]/10 transition-all duration-200 outline-none">
                </div>

                <button type="submit" class="apple-btn-primary w-full">
                    {{ __('Сбросить пароль') }}
                </button>
            </form>
        </div>
    </div>
</x-layouts.public>