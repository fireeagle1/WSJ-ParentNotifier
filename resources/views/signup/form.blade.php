<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Sign Up - {{ config('app.name', 'Jamboree Updates') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex flex-col items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-14 h-14 bg-purple-100 mb-3">
                    <svg class="w-7 h-7 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-800">{{ config('app.name', 'Jamboree Updates') }}</h1>
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
                                class="w-full px-4 py-2 bg-purple-600 text-white font-semibold hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Sign Up for Updates
                        </button>
                    </form>
                @endif
            </div>

            <!-- Footer -->
            <p class="mt-6 text-center text-sm text-gray-500">
                You will receive a personal link to view World Scout Jamboree updates.
            </p>
        </div>
    </div>
</body>
</html>
