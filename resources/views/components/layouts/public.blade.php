<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#f2f2f2] scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? __('Buyer Greece → Georgia | Выкуп и доставка товаров из Греции') }}</title>
    @if(isset($description))
        <meta name="description" content="{{ $description }}">
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex flex-col min-h-full text-[#1d1d1f] antialiased font-sans bg-[#f2f2f2]" x-data="{ mobileNavOpen: false, searchOpen: false }">

    <!-- Header + Drawer -->
    <x-layouts.header />

    <!-- Main Content -->
    <main class="flex-grow">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <x-layouts.footer />

</body>
</html>