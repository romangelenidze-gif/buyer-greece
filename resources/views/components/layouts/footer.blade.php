<footer class="apple-footer py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
            <div class="space-y-3">
                <div class="font-semibold text-xl text-[#1d1d1f] tracking-tight flex items-center gap-1.5">
                    exereto
                </div>
                <p class="text-[#6e6e73] leading-relaxed text-sm">
                    {{ __('Интеллектуальная платформа поиска и заказа товаров из греческих каталогов и интернет-магазинов.') }}
                </p>
            </div>

            <div>
                <h4 class="apple-footer-title">{{ __('Услуги') }}</h4>
                <ul class="space-y-2">
                    <li><a href="{{ route('buy-for-me') }}" class="apple-footer-link">{{ __('Выкупите за меня') }}</a></li>
                    <li><a href="{{ route('forwarding') }}" class="apple-footer-link">{{ __('Форвардинг') }}</a></li>
                </ul>
            </div>

            <div>
                <h4 class="apple-footer-title">{{ __('Информация') }}</h4>
                <ul class="space-y-2">
                    <li><a href="{{ route('how-it-works') }}" class="apple-footer-link">{{ __('Как это работает') }}</a></li>
                    <li><a href="{{ route('help') }}" class="apple-footer-link">{{ __('Помощь / FAQ') }}</a></li>
                    <li><a href="{{ route('help') }}" class="apple-footer-link">{{ __('Условия обслуживания') }}</a></li>
                    <li><a href="{{ route('help') }}" class="apple-footer-link">{{ __('Политика конфиденциальности') }}</a></li>
                </ul>
            </div>

            <div>
                <p class="text-sm text-[#6e6e73] leading-relaxed">
                    {{ __('Международная транспортировка Греция → Грузия регулируется правилами Camex.') }}
                </p>
            </div>
        </div>

        <div class="pt-8 border-t border-black/[0.08] flex flex-col sm:flex-row justify-between items-center gap-4 text-[#86868b]">
            <div class="text-sm">
                © {{ date('Y') }} exereto • {{ __('© Все права защищены.') }}
            </div>
        </div>
    </div>
</footer>