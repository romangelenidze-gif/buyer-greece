@props(['colour' => '', 'label' => null])

@php
    if (empty($colour)) {
        return;
    }

    // Словарь соответствия названий цветов HEX-кодам (работает по исходному английскому цвету)
    $colorMap = [
        'white' => '#FFFFFF',
        'black' => '#000000',
        'grey' => '#808080',
        'gray' => '#808080',
        'light grey' => '#D3D3D3',
        'dark grey' => '#A9A9A9',
        'red' => '#EF4444',
        'blue' => '#3B82F6',
        'navy' => '#1E3A8A',
        'green' => '#10B981',
        'yellow' => '#FBBF24',
        'pink' => '#EC4899',
        'purple' => '#8B5CF6',
        'orange' => '#F97316',
        'brown' => '#92400E',
        'beige' => '#F5F5DC',
        'cream' => '#FFFDD0',
        'olive' => '#556B2F',
        'maroon' => '#800000',
        'silver' => '#C0C0C0',
        'gold' => '#FFD700',
    ];

    // Разбиваем цвета по слешу или запятой (напр. "White / Black" -> ['White', 'Black'])
    $rawColors = preg_split('/[\/,]+/', $colour);
    $dots = [];

    foreach ($rawColors as $cName) {
        $cleanName = strtolower(trim($cName));
        $hex = '#CCCCCC';
        foreach ($colorMap as $key => $h) {
            if (str_contains($cleanName, $key)) {
                $hex = $h;
                break;
            }
        }
        $dots[] = [
            'name' => trim($cName),
            'hex' => $hex,
        ];
    }

    $displayLabel = $label ?: $colour;
@endphp

<div class="inline-flex items-center gap-1.5" title="{{ $displayLabel }}">
    <div class="flex -space-x-1 overflow-hidden">
        @foreach($dots as $dot)
            <span class="inline-block w-3.5 h-3.5 rounded-full ring-2 ring-white shadow-sm"
                  style="background-color: {{ $dot['hex'] }};"
                  aria-label="{{ $dot['name'] }}">
            </span>
        @endforeach
    </div>
    <span class="text-xs text-zinc-600 font-medium ml-1">{{ $displayLabel }}</span>
</div>