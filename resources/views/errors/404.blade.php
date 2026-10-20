<x-layouts.public title="{{ __('Страница не найдена') }} | Buyer Greece">
    <div class="min-h-[calc(100vh-16rem)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="apple-card max-w-md w-full text-center space-y-5 bg-white">
            
            <!-- Круглый индикатор ошибки -->
            <div class="w-16 h-16 bg-[#0071e3]/10 text-[#0071e3] font-mono font-bold text-xl rounded-full flex items-center justify-center mx-auto shadow-2xs">
                404
            </div>

            <div class="space-y-2">
                <h1 class="app-text-title text-2xl font-semibold text-[#1d1d1f]">
                    {{ __('Страница не найдена') }}
                </h1>
                <p class="app-text-sub text-[#515154] leading-relaxed">
                    {{ __('Запрашиваемая страница не существует или была перемещена. Проверьте правильность введенного адреса.') }}
                </p>
            </div>

            <!-- Кнопки действий в стиле Apple -->
            <div class="pt-2 flex flex-col sm:flex-row gap-3 justify-center items-center">
                <a href="{{ route('home') }}" class="apple-btn-primary w-full sm:w-auto">
                    {{ __('На главную') }}
                </a>
                
                @auth
                    <a href="{{ route('dashboard') }}" class="w-full sm:w-auto px-6 py-3 bg-black/[0.04] hover:bg-black/[0.08] text-[#1d1d1f] font-medium app-text-sub rounded-full transition-all duration-300 text-center">
                        {{ __('В личный кабинет') }}
                    </a>
                @endauth
            </div>

        </div>
    </div>
</x-layouts.public>