<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Leader Login — {{ config('app.name', 'Scouts') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-50">
        <!-- Purple Brand Header -->
        <div class="w-full bg-[#7413dc] py-6 mb-8">
            <div class="text-center">
                <h1 class="text-2xl font-bold text-white">
                    {{ \App\Models\Setting::where('key', 'brand_name')->value('value') ?? 'Scouts' }}
                </h1>
                <p class="text-purple-200 text-sm mt-1">Leader Portal</p>
            </div>
        </div>

        <div class="w-full sm:max-w-md px-6 py-6 bg-white shadow-sm overflow-hidden border border-gray-200">
            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <div class="mb-6 text-center">
                <h2 class="text-lg font-semibold text-gray-700">Leader Login</h2>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                        :value="old('email')" required autofocus autocomplete="username" maxlength="255" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password"
                        name="password" required autocomplete="current-password" maxlength="128" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end mt-6">
                    <button type="submit" class="w-full justify-center inline-flex items-center px-4 py-2 bg-[#7413dc] border border-transparent font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-800 focus:bg-purple-800 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-[#7413dc] focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('Log in') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
