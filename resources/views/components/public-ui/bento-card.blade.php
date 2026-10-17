@props([
    'step' => '01',
    'title' => '',
    'description' => '',
    'linkUrl' => null,
    'linkText' => __('Подробнее'),
])

<div class="apple-bento-card flex flex-col justify-between">
    <div>
        <div class="w-12 h-12 rounded-2xl bg-white text-[#1d1d1f] shadow-sm flex items-center justify-center font-semibold text-lg mb-6 border border-black/[0.04]">
            {{ $step }}
        </div>
        <h3 class="text-xl font-semibold text-[#1d1d1f] mb-3 tracking-tight">
            {{ $title }}
        </h3>
        <p class="text-sm text-[#86868b] leading-relaxed mb-8">
            {{ $description }}
        </p>
    </div>

    @if($linkUrl)
        <a href="{{ $linkUrl }}" class="inline-flex items-center text-sm font-semibold text-[#0071e3] hover:text-[#0077ed] group">
            <span>{{ $linkText }}</span>
            <span class="ml-1 transition-transform duration-200 group-hover:translate-x-1">→</span>
        </a>
    @endif
</div>