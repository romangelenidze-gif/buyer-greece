@props([
    'value',
    'label' => null,
])

<div x-data="{ copied: false }" class="space-y-1">
    @if($label)
        <label class="block text-xs font-medium text-slate-500">{{ $label }}</label>
    @endif
    
    <div class="relative flex items-center">
        <input 
            type="text" 
            value="{{ $value }}" 
            readonly 
            class="block w-full pr-24 text-xs font-mono bg-slate-50 border border-slate-200 rounded-lg py-2 px-3 text-slate-900 focus:outline-none focus:ring-1 focus:ring-brand-500"
        >
        
        <button 
            type="button" 
            @click="navigator.clipboard.writeText('{{ $value }}'); copied = true; setTimeout(() => copied = false, 2000)"
            class="absolute right-1 px-2.5 py-1 text-[11px] font-semibold rounded-md transition-colors"
            :class="copied ? 'bg-emerald-100 text-emerald-800' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200 shadow-sm'"
        >
            <span x-show="!copied">Скопировать</span>
            <span x-show="copied" x-cloak class="flex items-center gap-1">
                <svg class="w-3 h-3 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Скопировано
            </span>
        </button>
    </div>
</div>