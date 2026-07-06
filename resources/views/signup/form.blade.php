<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Sign Up - {{ config('app.name', 'Jamboree Updates') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=nunito-sans:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Nunito Sans', 'sans-serif'] },
                    colors: {
                        brand: {
                            50: '#eef8f8', 100: '#d5eeee', 200: '#aedddd', 300: '#7ac5c5',
                            400: '#4da8a8', 500: '#088486', 600: '#076e70', 700: '#065a5c',
                            800: '#054849', 900: '#043a3b', 950: '#022526',
                        },
                        wsj: {
                            purple: '#7413dc', orange: '#ff912a', green: '#205b41',
                            teal: '#088486', navy: '#003982', blue: '#006ddf',
                            red: '#ed3f23', yellow: '#ffe627',
                        },
                    },
                },
            },
        }
    </script>
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex flex-col items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            <!-- Header -->
            <div class="text-center mb-8">
                @php
                    $logoPath = \App\Models\Setting::where('key', 'logo_path')->value('value');
                    $brandName = \App\Models\Setting::where('key', 'brand_name')->value('value') ?? config('app.name', 'Jamboree Updates');
                @endphp
                @if($logoPath)
                    <img src="{{ asset('storage/' . $logoPath) }}" alt="{{ $brandName }}" class="w-16 h-16 object-contain mx-auto mb-3">
                @else
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-brand-100 mb-3 rounded-full">
                        <svg class="w-9 h-9 text-brand-600" viewBox="0 0 64 64" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path d="M32 6c-.8 0-1.5.5-1.8 1.2L28 14l-6 3c-.7.4-1.2 1.1-1.2 1.9 0 .8.5 1.5 1.2 1.9l3.5 2C26.5 26 28 29.5 28 33c0 2 .5 3.8 1.3 5.3.5.9 1.5 1.5 2.7 1.5s2.2-.6 2.7-1.5c.8-1.5 1.3-3.3 1.3-5.3 0-3.5 1.5-7 2.5-10.2l3.5-2c.7-.4 1.2-1.1 1.2-1.9 0-.8-.5-1.5-1.2-1.9l-6-3-2.2-6.8C33.5 6.5 32.8 6 32 6z"/>
                            <path d="M26 44c0 0 2 4 6 4s6-4 6-4" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/>
                            <path d="M28 48l-2 6M36 48l2 6M32 49v7" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/>
                            <circle cx="32" cy="58" r="2.5"/>
                            <circle cx="26" cy="56" r="1.5"/>
                            <circle cx="38" cy="56" r="1.5"/>
                        </svg>
                    </div>
                @endif
                <h1 class="text-2xl font-bold text-gray-800">{{ $brandName }}</h1>
                <p class="mt-2 text-gray-600">Parent Sign-Up</p>
            </div>

            <div class="bg-white shadow-sm border border-gray-200 p-8">
                @if ($closed)
                    <!-- Sign-ups closed message -->
                    <div class="text-center">
                        <div class="mb-4">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-semibold text-gray-700 mb-2">Sign-ups are currently closed</h2>
                        <p class="text-gray-500">Please contact the Jamboree leaders if you need access to updates.</p>
                    </div>
                @else
                    <!-- Success message -->
                    @if (session('success'))
                        <div class="mb-6 p-4 bg-green-100 border border-green-300 text-green-800">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Validation errors -->
                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-red-100 border border-red-300 text-red-800">
                            <ul class="list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('signup.store', $signupToken ?? null) }}">
                        @csrf

                        <!-- Young Person's Name -->
                        <div class="mb-4">
                            <label for="young_person_name" class="block text-sm font-medium text-gray-700 mb-1">
                                Young Person's Name
                            </label>
                            <input type="text"
                                   id="young_person_name"
                                   name="young_person_name"
                                   value="{{ old('young_person_name') }}"
                                   required
                                   maxlength="255"
                                   class="w-full px-3 py-2 border-2 border-gray-900 shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                   placeholder="Enter your child's name">
                        </div>

                        <!-- Parent Email -->
                        <div class="mb-6">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                Your Email Address
                            </label>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   required
                                   maxlength="255"
                                   class="w-full px-3 py-2 border-2 border-gray-900 shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                   placeholder="you@example.com">
                        </div>

                        <!-- Submit Button -->
                        <button type="submit"
                                class="w-full px-4 py-2 bg-wsj-navy text-white font-semibold hover:bg-[#002a61] focus:outline-none focus:ring-2 focus:ring-wsj-navy focus:ring-offset-2 transition ease-in-out duration-150">
                            Sign Up for Updates
                        </button>
                    </form>
                @endif
            </div>

            <!-- Privacy Notice -->
            <div class="mt-6 p-4 bg-gray-50 border border-gray-200">
                <p class="text-xs text-gray-600 leading-relaxed">
                    By signing up, you agree to receive email notifications about Jamboree updates.
                    Your email address will only be used to send you a personal feed link and post notifications.
                    We will not share your data with third parties. You can unsubscribe at any time by contacting the Jamboree leaders.
                </p>
            </div>

            <!-- Footer -->
            <p class="mt-4 text-center text-sm text-gray-500">
                You will receive a personal link to view World Scout Jamboree updates.
            </p>
            <p class="mt-2 text-center text-xs text-gray-400">
                Kindly provided by <a href="https://ckenterprises.co.uk" class="text-brand-500 hover:text-brand-700 transition" target="_blank" rel="noopener">CK Enterprises UK</a>
            </p>
        </div>
    </div>
</body>
</html>
