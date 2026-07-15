<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Medix eSanté' }}</title>

        <!-- Google Fonts: Outfit & Instrument Sans -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&family=Outfit:wght@100..900&display=swap" rel="stylesheet">

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-[#F8FAFC] text-[#1e293b] antialiased min-h-screen relative overflow-x-hidden font-sans">
        <!-- Reusable Background Animation -->
        <x-background-animation  />

        <!-- Main Wrapper -->
        <div class="relative z-10 min-h-screen flex flex-col justify-center items-center p-4 md:p-8">
            {{ $slot }}
        </div>
    </body>
</html>
