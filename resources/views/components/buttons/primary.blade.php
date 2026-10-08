@props([
    'href' => null,
    'type' => 'submit',
    'size' => 'default',
    'disabled' => false,
])

@php
    $sizeClasses = [
        'small' => 'px-3 py-1.5 text-xs font-medium',
        'default' => 'px-4 py-2 text-sm font-semibold',
        'large' => 'px-6 py-3 text-base font-semibold',
    ][$size] ?? 'px-4 py-2 text-sm font-semibold';

    $baseClasses = 'inline-flex items-center justify-center rounded-lg bg-brand-600 text-white shadow-sm hover:bg-brand-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600 transition-all disabled:opacity-50 disabled:cursor-not-allowed';
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => "$baseClasses $sizeClasses"]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => "$baseClasses $sizeClasses"]) }}>
        {{ $slot }}
    </button>
@endif