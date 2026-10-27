@props([
    'theme' => 'light',
    'direction' => 'down',
    'width' => 'w-48'
])

<div x-data="{ open: false }" class="relative inline-block text-left" @click.outside="open = false">
    <!-- Кнопка-триггер -->
    <div @click="open = !open" class="cursor-pointer select-none">
        {{ $trigger }}
    </div>

    <!-- Выпадающее меню в едином стиле Apple -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-150" 
         x-transition:enter-start="opacity-0 scale-95 {{ $direction === 'up' ? 'translate-y-1' : '-translate-y-1' }}" 
         x-transition:enter-end="opacity-100 scale-100 translate-y-0" 
         x-transition:leave="transition ease-in duration-100" 
         x-transition:leave-start="opacity-100 scale-100 translate-y-0" 
         x-transition:leave-end="opacity-0 scale-95 {{ $direction === 'up' ? 'translate-y-1' : '-translate-y-1' }}" 
         x-cloak 
         class="absolute {{ $direction === 'up' ? 'bottom-full mb-2' : 'top-full mt-2' }} right-0 {{ $width }} rounded-2xl overflow-hidden shadow-[0_10px_30px_rgba(0,0,0,0.08)] py-1.5 z-50 text-xs font-medium {{ $theme === 'dark' ? 'bg-[#1d1d1f]/95 backdrop-blur-xl border border-white/15 text-white' : 'bg-white/95 backdrop-blur-xl border border-black/[0.08] text-[#1d1d1f]' }}">
        
        {{ $slot }}
    </div>
</div>