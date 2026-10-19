@props([
    'value',
    'label' => null,
])

<div x-data="{ copied: false }" class="space-y-1.5">
    @if($label)
        <label class="block text-xs font-semibold text-[#1d1d1f]">{{ $label }}</label>
    @endif
    
    <div class="relative flex items-center">
        <input 
            type="text" 
            value="{{ $value }}" 
            readonly 
            class="w-full px-4 py-2.5 pr-28 rounded-xl bg-black/[0.03] border border-black/[0.08] text-xs font-mono text-[#1d1d1f] focus:outline-none"
        >
        
        <button 
            type="button" 
            @click="navigator.clipboard.writeText('{{ $value }}'); copied = true; setTimeout(() => copied = false, 2000)"
            class="absolute right-1.5 px-3 py-1.5 text-xs font-medium rounded-lg transition-colors cursor-pointer"
            :class="copied ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-white text-[#1d1d1f] hover:bg-black/[0.04] border border-black/[0.08] shadow-xs'"
        >
            <span x-show="!copied">{{ __('Скопировать') }}</span>
            <span x-show="copied" x-cloak class="flex items-center gap-1 font-semibold text-emerald-700">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
                {{ __('Скопировано') }}
            </span>
        </button>
    </div>
</div>