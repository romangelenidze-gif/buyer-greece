@props([
    'value',
    'label' => null,
])

<div x-data="{ copied: false }" class="w-full">
    @if($label)
        <label class="block text-xs font-medium text-slate-500 mb-1">{{ $label }}</label>
    @endif
    <div class="flex items-center rounded-md bg-slate-100 border border-slate-200 p-2 text-sm font-mono text-slate-800 justify-between">
        <span class="truncate select-all">{{ $value }}</span>
        <button 
            type="button" 
            @click="navigator.clipboard.writeText('{{ $value }}'); copied = true; setTimeout(() => copied = false, 2000)"
            class="ml-2 text-xs font-sans bg-white px-2.5 py-1 rounded border border-slate-300 hover:bg-slate-50 text-slate-700 transition-all focus:outline-none flex-shrink-0"
        >
            <span x-show="!copied">Скопировать</span>
            <span x-show="copied" x-cloak class="text-emerald-600 font-semibold">Скопировано!</span>
        </button>
    </div>
</div>