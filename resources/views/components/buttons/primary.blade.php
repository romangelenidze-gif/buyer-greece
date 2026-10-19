@props([
    'href' => null,
    'type' => 'submit',
    'size' => 'default',
    'disabled' => false,
])

@php
    $sizeClasses = match ($size) {
        'small' => 'px-3.5 py-1.5 text-xs font-semibold',
        'large' => 'px-6 py-3.5 text-base font-semibold',
        default => 'px-5 py-2.5 text-sm font-semibold',
    };

    $baseClasses = 'inline-flex items-center justify-center rounded-full bg-[#0071e3] text-white hover:bg-[#0077ed] active:scale-98 shadow-xs transition-all duration-200 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed disabled:active:scale-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#0071e3]';
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