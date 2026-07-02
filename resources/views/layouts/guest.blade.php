<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-purple-50 via-white to-purple-50">
            <div>
                <a href="/" class="flex flex-col items-center gap-3">
                    @php $logoPath = \App\Models\Setting::where('key', 'logo_path')->value('value'); @endphp
                    @if($logoPath)
                        <img src="{{ asset('storage/' . $logoPath) }}" alt="Logo" class="w-20 h-20 object-contain rounded">
                    @else
                        <x-application-logo class="w-20 h-20 fill-current text-brand-600" />
                    @endif
                    <span class="text-lg font-bold text-brand-600">
                        {{ \App\Models\Setting::where('key', 'brand_name')->value('value') ?? 'Scouts' }}
                    </span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-lg overflow-hidden sm:rounded-2xl border border-gray-100">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
