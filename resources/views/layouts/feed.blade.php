<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Expedition Updates')</title>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin="" />

    <style>
        /* Lightbox styles */
        .lightbox-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: rgba(0, 0, 0, 0.85);
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        .lightbox-overlay.active {
            display: flex;
        }
        .lightbox-overlay img {
            max-width: 90vw;
            max-height: 90vh;
            object-fit: contain;
            border-radius: 0.5rem;
        }
        .lightbox-close {
            position: absolute;
            top: 1rem;
            right: 1.5rem;
            color: white;
            font-size: 2rem;
            font-weight: bold;
            cursor: pointer;
            z-index: 10000;
        }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-50 font-sans antialiased min-h-screen">
    <!-- Header -->
    <header class="bg-[#7413dc] shadow-sm">
        @php
            $headerImage = \App\Models\Setting::where('key', 'header_image_path')->value('value');
            $brandName = \App\Models\Setting::where('key', 'brand_name')->value('value') ?? 'Scouts';
        @endphp

        @if($headerImage)
            <div class="w-full">
                <img src="{{ asset('storage/' . $headerImage) }}" alt="{{ $brandName }}" class="w-full h-32 object-cover">
            </div>
        @endif

        <div class="max-w-3xl mx-auto px-4 py-4 sm:px-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    @php $logoPath = \App\Models\Setting::where('key', 'logo_path')->value('value'); @endphp
                    @if($logoPath)
                        <img src="{{ asset('storage/' . $logoPath) }}" alt="{{ $brandName }}" class="w-12 h-12 object-contain rounded">
                    @else
                        <svg class="w-10 h-10 text-white" viewBox="0 0 64 64" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path d="M32 6c-.8 0-1.5.5-1.8 1.2L28 14l-6 3c-.7.4-1.2 1.1-1.2 1.9 0 .8.5 1.5 1.2 1.9l3.5 2C26.5 26 28 29.5 28 33c0 2 .5 3.8 1.3 5.3.5.9 1.5 1.5 2.7 1.5s2.2-.6 2.7-1.5c.8-1.5 1.3-3.3 1.3-5.3 0-3.5 1.5-7 2.5-10.2l3.5-2c.7-.4 1.2-1.1 1.2-1.9 0-.8-.5-1.5-1.2-1.9l-6-3-2.2-6.8C33.5 6.5 32.8 6 32 6z"/>
                            <path d="M26 44c0 0 2 4 6 4s6-4 6-4" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/>
                            <path d="M28 48l-2 6M36 48l2 6M32 49v7" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/>
                            <circle cx="32" cy="58" r="2.5"/>
                            <circle cx="26" cy="56" r="1.5"/>
                            <circle cx="38" cy="56" r="1.5"/>
                        </svg>
                    @endif
                    <div class="flex flex-col leading-tight">
                        <span class="text-white font-semibold text-lg">{{ $brandName }}</span>
                        <span class="text-purple-300 text-[11px] font-medium">Expedition Updates</span>
                    </div>
                </div>
                @if(isset($token))
                <nav class="flex items-center gap-4">
                    <a href="{{ route('feed.index', $token) }}" class="text-sm font-medium {{ request()->routeIs('feed.index') ? 'text-white' : 'text-purple-200 hover:text-white' }} transition">Feed</a>
                    <a href="{{ route('feed.map', $token) }}" class="text-sm font-medium {{ request()->routeIs('feed.map') ? 'text-white' : 'text-purple-200 hover:text-white' }} transition">Map</a>
                    <a href="{{ route('feed.leaders', $token) }}" class="text-sm font-medium {{ request()->routeIs('feed.leaders') ? 'text-white' : 'text-purple-200 hover:text-white' }} transition">Leaders</a>
                    <a href="{{ route('feed.contacts', $token) }}" class="text-sm font-medium {{ request()->routeIs('feed.contacts') ? 'text-white' : 'text-purple-200 hover:text-white' }} transition">Contact</a>
                </nav>
                @endif
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-3xl mx-auto px-4 py-6 sm:px-6">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="border-t border-gray-200 mt-8">
        <div class="max-w-3xl mx-auto px-4 py-4 sm:px-6 text-center">
            <p class="text-sm text-gray-500">&copy; {{ date('Y') }} {{ $brandName }}</p>
            <p class="text-xs text-gray-400 mt-1">Kindly provided by <a href="https://ckenterprises.co.uk" class="text-purple-500 hover:text-purple-700 transition" target="_blank" rel="noopener">CK Enterprises UK</a></p>
        </div>
    </footer>

    <!-- Lightbox -->
    <div class="lightbox-overlay" id="lightbox" onclick="closeLightbox()">
        <span class="lightbox-close" onclick="closeLightbox()">&times;</span>
        <img id="lightbox-img" src="" alt="Full size photo">
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""></script>

    <script>
        // Lightbox functionality
        function openLightbox(src) {
            document.getElementById('lightbox-img').src = src;
            document.getElementById('lightbox').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeLightbox() {
            document.getElementById('lightbox').classList.remove('active');
            document.getElementById('lightbox-img').src = '';
            document.body.style.overflow = '';
        }

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeLightbox();
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
