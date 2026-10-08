@props([
    'status' => 'pending',
    'label' => null,
])

@php
    $badgeStyle = match ($status) {
        'draft', 'pending', 'received' => 'bg-slate-100 text-slate-700 border-slate-200',
        'action_required', 'quote_ready', 'awaiting_payment' => 'bg-amber-50 text-amber-800 border-amber-200 font-semibold',
        'paid', 'purchased', 'completed', 'transferred_to_camex' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'expired', 'cancelled', 'rejected', 'failed' => 'bg-rose-50 text-rose-700 border-rose-200',
        default => 'bg-slate-100 text-slate-700 border-slate-200',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs border $badgeStyle"]) }}>
    {{ $label ?? $status }}
</span>