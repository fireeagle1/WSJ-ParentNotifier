<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=nunito-sans:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @stack('styles')
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                @hasSection('content')
                    @yield('content')
                @else
                    {{ $slot }}
                @endif
            </main>

            <!-- Footer -->
            <footer class="border-t border-gray-100 mt-12">
                <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8 text-center">
                    <p class="text-xs text-gray-400">Kindly provided by <a href="https://ckenterprises.co.uk" class="text-brand-500 hover:text-brand-700 transition" target="_blank" rel="noopener">CK Enterprises UK</a></p>
                </div>
            </footer>
        </div>

        @stack('scripts')
    </body>
</html>
