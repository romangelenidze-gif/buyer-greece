@props(['index', 'question'])

<div class="py-1">
    <button 
        type="button"
        @click="openFaq = (openFaq === {{ $index }} ? null : {{ $index }})"
        :aria-expanded="openFaq === {{ $index }}"
        aria-controls="faq-answer-{{ $index }}"
        id="faq-question-{{ $index }}" 
        class="w-full text-left flex justify-between items-center gap-5 py-5 group cursor-pointer rounded-2xl focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#0071e3]/40"
    >
        <span class="app-text-title min-w-0 pr-2 transition-colors duration-300 group-hover:text-[#0071e3]" :class="openFaq === {{ $index }} ? 'text-[#0071e3]' : ''">
            {{ $question }}
        </span>
        <span class="w-8 h-8 rounded-full bg-black/[0.035] group-hover:bg-[#0071e3]/[0.08] flex items-center justify-center text-[#6e6e73] transition-colors duration-300 shrink-0 ml-1" :class="openFaq === {{ $index }} ? '!bg-[#0071e3]/[0.08] !text-[#0071e3]' : ''">
            <svg class="w-4 h-4 transition-transform duration-500 ease-[cubic-bezier(0.22,1,0.36,1)] motion-reduce:transition-none" :class="openFaq === {{ $index }} ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <path d="M5.5 7.5L10 12l4.5-4.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </span>
    </button>
    <div
        id="faq-answer-{{ $index }}"
        role="region"
        aria-labelledby="faq-question-{{ $index }}"
        :aria-hidden="openFaq !== {{ $index }}"
        class="grid transition-[grid-template-rows,opacity] duration-500 ease-[cubic-bezier(0.22,1,0.36,1)] motion-reduce:transition-none will-change-[grid-template-rows,opacity]"
        :style="openFaq === {{ $index }} ? 'grid-template-rows: 1fr; opacity: 1;' : 'grid-template-rows: 0fr; opacity: 0;'"
    >
        <div class="min-h-0 overflow-hidden">
            <div class="app-text-body pt-2 pb-5 text-[#424245] leading-relaxed">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>