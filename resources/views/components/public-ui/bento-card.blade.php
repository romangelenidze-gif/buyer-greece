@props([
    'step' => '01',
    'title' => '',
    'description' => '',
    'linkUrl' => null,
    'linkText' => __('Подробнее'),
])

<div class="apple-bento-card bg-white rounded-3xl p-8 shadow-[0_10px_30px_rgba(0,0,0,0.04)] border border-black/[0.06] flex flex-col justify-between transition-all duration-300 hover:shadow-[0_20px_40px_rgba(0,0,0,0.08)] hover:-translate-y-1">
    <div>
        @if($step)
            <div class="w-12 h-12 rounded-2xl bg-[#0071e3]/10 text-[#0071e3] flex items-center justify-center font-bold text-sm mb-6">
                {{ $step }}
            </div>
        @endif
        
        <h3 class="text-lg font-semibold text-[#1d1d1f] mb-3 tracking-tight">
            {{ $title }}
        </h3>
        
        <p class="text-xs sm:text-sm text-[#86868b] leading-relaxed mb-8">
            {{ $description }}
        </p>
    </div>

    @if($linkUrl)
        <div class="pt-6 border-t border-black/[0.06]">
            <a href="{{ $linkUrl }}" class="inline-flex items-center gap-2 text-xs sm:text-sm font-semibold text-[#0071e3] hover:underline group">
                <span>{{ $linkText }}</span>
                <svg class="w-4 h-4 transition-transform duration-200 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    @endif
</div>