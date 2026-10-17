<footer class="bg-[#1d1d1f] text-gray-400 text-xs border-t border-white/10 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
            <div class="space-y-3">
                <div class="font-bold text-base text-white tracking-tight flex items-center gap-1.5">
                    <span class="text-[#0071e3]">Buyer</span>Greece
                </div>
                <p class="text-gray-400 leading-relaxed">
                    {{ __('Сервис выкупа и приема покупок в греческих магазинах. Международная доставка в Грузию выполняется перевозчиком Camex.') }}
                </p>
            </div>

            <div>
                <h4 class="font-semibold text-white mb-3 text-sm">{{ __('Услуги') }}</h4>
                <ul class="space-y-2">
                    <li><a href="{{ route('buy-for-me') }}" class="hover:text-white transition-colors">{{ __('Выкупите за меня') }}</a></li>
                    <li><a href="{{ route('forwarding') }}" class="hover:text-white transition-colors">{{ __('Форвардинг') }}</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-semibold text-white mb-3 text-sm">{{ __('Информация') }}</h4>
                <ul class="space-y-2">
                    <li><a href="{{ route('how-it-works') }}" class="hover:text-white transition-colors">{{ __('Как это работает') }}</a></li>
                    <li><a href="{{ route('help') }}" class="hover:text-white transition-colors">{{ __('Помощь / FAQ') }}</a></li>
                    <li><a href="{{ route('help') }}" class="hover:text-white transition-colors">{{ __('Условия обслуживания') }}</a></li>
                    <li><a href="{{ route('help') }}" class="hover:text-white transition-colors">{{ __('Политика конфиденциальности') }}</a></li>
                </ul>
            </div>

            <div>
                <p class="text-[11px] text-gray-500 leading-relaxed">
                    {{ __('Международная транспортировка Греция → Грузия регулируется правилами Camex.') }}
                </p>
            </div>
        </div>

        <div class="pt-8 border-t border-white/10 flex flex-col sm:flex-row justify-center items-center gap-4 text-gray-500">
            <div>
                © {{ date('Y') }} Buyer Greece • {{ __('© Все права защищены.') }}
            </div>
        </div>
    </div>
</footer>