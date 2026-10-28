{{-- resources/views/search/partials/clarification.blade.php --}}
@php
    $options = $clarification['options'] ?? [];
    $budgets = $clarification['budgets'] ?? [];
@endphp

<div class="col-span-full max-w-2xl mx-auto w-full apple-card bg-white rounded-3xl border border-black/[0.06] p-6 sm:p-8 space-y-6"
     x-data="{
        budget: null,
        budgets: @js($budgets),
        link(q) {
            const p = new URLSearchParams({ q: q });
            if (this.budget !== null) {
                const b = this.budgets[this.budget];
                if (b.price_min) p.set('min_price', b.price_min);
                if (b.price_max) p.set('max_price', b.price_max);
            }
            return '{{ route('product-search') }}?' + p.toString();
        }
     }">

    <h4 class="app-text-title font-semibold text-center">{{ $clarification['question'] ?? '' }}</h4>

    @if(!empty($budgets))
        <div class="space-y-2">
            <p class="app-text-sub text-sm text-[#86868b] text-center">{{ __('Бюджет (необязательно)') }}</p>
            <div class="flex flex-wrap justify-center gap-2">
                @foreach($budgets as $i => $budget)
                    <button type="button"
                            @click="budget = budget === {{ $i }} ? null : {{ $i }}"
                            :class="budget === {{ $i }} ? 'bg-[#0071e3] text-white' : 'bg-[#f5f5f7] text-[#1d1d1f]'"
                            class="px-4 py-2 rounded-full text-sm font-medium transition-colors cursor-pointer">
                        {{ $budget['label'] }}
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    <div class="flex flex-wrap justify-center gap-3">
        @foreach($options as $option)
            <a :href="link(@js($option['query']))"
               @click="loading = true"
               class="px-5 py-3 rounded-2xl bg-[#f5f5f7] hover:bg-[#ececf0] text-[#1d1d1f] text-sm font-semibold transition-colors">
                {{ $option['label'] }}
            </a>
        @endforeach
    </div>
</div>
