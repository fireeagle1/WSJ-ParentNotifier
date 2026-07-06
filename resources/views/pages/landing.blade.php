<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ \App\Models\Setting::where('key', 'brand_name')->value('value') ?? 'Scouts' }} - Jamboree Updates</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=nunito-sans:400,500,600,700&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Nunito Sans', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eef8f8',
                            100: '#d5eeee',
                            200: '#aedddd',
                            300: '#7ac5c5',
                            400: '#4da8a8',
                            500: '#088486',
                            600: '#076e70',
                            700: '#065a5c',
                            800: '#054849',
                            900: '#043a3b',
                        },
                        wsj: {
                            purple: '#7413dc',
                            orange: '#ff912a',
                            green: '#205b41',
                            teal: '#088486',
                            navy: '#003982',
                            blue: '#006ddf',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col font-sans">
    @php
        $brandName = \App\Models\Setting::where('key', 'brand_name')->value('value') ?? 'Scouts';
    @endphp

    <header class="bg-wsj-navy">
        <div class="max-w-4xl mx-auto px-4 py-5 flex items-center justify-between">
            <h1 class="text-xl font-bold text-white">{{ $brandName }}</h1>
            <a href="{{ route('login') }}" class="text-sm font-medium text-blue-200 hover:text-white transition">
                Leader Login →
            </a>
        </div>
    </header>

    <main class="flex-1 flex items-center justify-center px-4 py-16">
        <div class="max-w-lg text-center">
            <div class="mb-8">
                <div class="w-20 h-20 mx-auto bg-brand-100 flex items-center justify-center rounded-full">
                    <svg class="h-10 w-10 text-brand-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />
                    </svg>
                </div>
            </div>

            <h2 class="text-3xl font-bold text-gray-900 mb-4">Stay Connected During the Jamboree</h2>

            <p class="text-lg text-gray-600 mb-6">
                This portal keeps parents informed with updates, photos, and location pins from Scout Jamboree leaders. If you're a parent, check your email for your personal access link.
            </p>

            <p class="text-sm text-gray-500 mb-8">
                Parents receive a unique link via email to view Jamboree updates. No account creation needed.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium text-white bg-wsj-navy hover:bg-wsj-navy/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-wsj-navy shadow-sm transition">
                    Leader Login
                </a>
                <a href="{{ route('privacy') }}" class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition">
                    Privacy Policy
                </a>
            </div>
        </div>
    </main>

    <footer class="border-t border-gray-200">
        <div class="max-w-4xl mx-auto px-4 py-4 text-center text-sm text-gray-500">
            <a href="{{ route('privacy') }}" class="hover:text-brand-600 transition">Privacy Policy</a>
        </div>
    </footer>
</body>
</html>
