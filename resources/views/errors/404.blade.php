<x-layouts.public title="Страница не найдена | Buyer Greece">
    <div class="min-h-[calc(100vh-16rem)] flex items-center justify-center py-12 px-4">
        <div class="max-w-md w-full bg-white border border-slate-200 rounded-2xl p-8 text-center space-y-4 shadow-sm">
            <div class="w-16 h-16 bg-slate-100 text-slate-400 font-mono font-black text-xl rounded-full flex items-center justify-center mx-auto">
                404
            </div>
            <h1 class="text-xl font-extrabold text-slate-900">Страница не найдена</h1>
            <p class="text-xs text-slate-500 leading-relaxed">
                Запрашиваемая страница не существует или была перемещена. Проверьте правильность адреса.
            </p>
            <div class="pt-2 flex justify-center gap-3">
                <x-buttons.primary href="{{ route('home') }}" size="small">
                    На главную
                </x-buttons.primary>
                @auth
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                        В личный кабинет
                    </a>
                @endauth
            </div>
        </div>
    </div>
</x-layouts.public>