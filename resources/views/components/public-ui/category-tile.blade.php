@props(['tile'])

<a href="{{ $tile['url'] }}" class="apple-category-chip">
    <span>{{ $tile['label'] }}</span>
    <span class="apple-category-chip-count">{{ $tile['count'] }}</span>
</a>
