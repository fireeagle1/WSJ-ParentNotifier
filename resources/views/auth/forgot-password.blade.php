<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password — {{ config('app.name', 'Scouts') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=nunito-sans:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-50">
        <div class="w-full bg-wsj-navy py-6 mb-8">
            <div class="text-center">
                <h1 class="text-2xl font-bold text-white">
                    {{ \App\Models\Setting::where('key', 'brand_name')->value('value') ?? 'Scouts' }}
                </h1>
                <p class="text-blue-200 text-sm mt-1">Password Reset</p>
            </div>
        </div>

        <div class="w-full sm:max-w-md px-6 py-6 bg-white shadow-sm overflow-hidden border border-gray-200">
            <div class="mb-4 text-sm text-gray-600">
                Enter your email address and we'll send you a link to reset your password.
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-4 p-3 bg-green-100 border border-green-300 text-green-700 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                        :value="old('email')" required autofocus maxlength="255" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="flex items-center justify-between mt-6">
                    <a href="{{ route('login') }}" class="text-sm text-brand-600 hover:text-brand-800 underline">
                        Back to login
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-wsj-navy border border-transparent font-semibold text-xs text-white uppercase tracking-widest hover:bg-wsj-navy/90 focus:outline-none focus:ring-2 focus:ring-wsj-navy focus:ring-offset-2 transition ease-in-out duration-150">
                        Send Reset Link
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
