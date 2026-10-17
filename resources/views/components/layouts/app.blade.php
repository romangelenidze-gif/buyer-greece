<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#f2f2f2]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- STRICT NOINDEX FOR CUSTOMER CABINET -->
    <meta name="robots" content="noindex, nofollow">

    <title>{{ $title ?? __('Личный кабинет') . ' | Buyer Greece' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex flex-col min-h-full text-[#1d1d1f] antialiased font-sans bg-[#f2f2f2]">

    <!-- Cabinet Header & Mobile Navigation -->
    <x-cabinet.cabinet-header />

    <!-- SYSTEM ALERTS -->
    @if(session('success'))
        <div class="bg-emerald-600 text-white text-xs font-medium py-2.5 px-4 text-center shadow-inner">
            {{ session('success') }}
        </div>
    @endif

    <!-- MAIN CABINET CONTENT -->
    <main class="flex-grow py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{ $slot }}
        </div>
    </main>

    <!-- Global Dark Footer -->
    <x-layouts.footer />

    <!-- Floating Messengers Widget -->
    <x-floating-messengers />

</body>
</html>