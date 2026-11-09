<x-layouts.public title="{{ __('Подтверждение email') }}">
    <div class="min-h-[calc(100vh-16rem)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-[#f5f5f7]">
        <div class="max-w-md w-full space-y-6 apple-card text-center">
            <div class="space-y-2">
                <h1 class="app-text-title">{{ __('Подтвердите ваш email') }}</h1>
                <p class="app-text-sub">
                    {{ __('Спасибо за регистрацию! Перед началом работы, пожалуйста, подтвердите ваш адрес электронной почты, перейдя по ссылке, которую мы только что отправили вам. Если вы не получили письмо, мы с радостью отправим его повторно.') }}
                </p>
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="p-3 text-sm text-green-700 bg-green-100 rounded-xl">
                    {{ __('Новая ссылка для подтверждения была отправлена на ваш email адрес.') }}
                </div>
            @endif

            <div class="space-y-4 pt-2">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="apple-btn-primary w-full">
                        {{ __('Отправить письмо повторно') }}
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-[#86868b] hover:text-black transition-colors">
                        {{ __('Выйти из системы') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.public>