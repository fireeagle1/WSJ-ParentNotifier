<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Notice - {{ \App\Models\Setting::where('key', 'brand_name')->value('value') ?? 'Scouts' }} Jamboree Updates</title>
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
            <a href="{{ route('landing') }}" class="text-xl font-bold text-white hover:text-blue-200 transition">{{ $brandName }}</a>
            <a href="{{ route('login') }}" class="text-sm font-medium text-blue-200 hover:text-white transition">
                Leader Login →
            </a>
        </div>
    </header>

    <main class="flex-1 max-w-3xl mx-auto px-4 py-12">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Privacy Notice</h1>
        <p class="text-gray-500 mb-8">How we look after your information on the {{ $brandName }} Jamboree Updates portal.</p>

        <div class="space-y-8">
            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-3">What we collect</h2>
                <p class="text-gray-700 leading-relaxed mb-3">
                    We only collect what's needed to keep parents connected during the Jamboree:
                </p>
                <ul class="list-disc list-inside text-gray-700 space-y-1">
                    <li><strong>Parent/guardian email addresses</strong> — so we can send you a personal feed link and notify you when new updates are posted.</li>
                    <li><strong>Young person's name</strong> — so leaders can identify which family to send updates to.</li>
                    <li><strong>Leader names and email addresses</strong> — for authentication and to show who posted each update.</li>
                    <li><strong>Photos</strong> — uploaded by leaders as part of Jamboree updates.</li>
                    <li><strong>Location pins</strong> — optional map coordinates leaders attach to posts to show where the group is.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-3">How we use it</h2>
                <p class="text-gray-700 leading-relaxed mb-3">
                    Your data is used only to run this portal — nothing else:
                </p>
                <ul class="list-disc list-inside text-gray-700 space-y-1">
                    <li>We email you when leaders post a new update.</li>
                    <li>We generate a unique, secure link so you can view the feed without creating an account.</li>
                    <li>Photos and location pins appear in the feed to keep you informed of progress.</li>
                    <li>Leader display names are shown on posts so you know who wrote them.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-3">Keeping your data safe</h2>
                <p class="text-gray-700 leading-relaxed">
                    All data is stored on the portal's hosting server. Passwords are hashed and never stored in plain text.
                    Your personal feed link uses a randomly generated token that cannot be guessed. Photos are stored in the
                    application's local filesystem and are only accessible through authenticated or token-based access.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-3">No third-party sharing</h2>
                <p class="text-gray-700 leading-relaxed">
                    We do not sell, trade, or share your personal data with anyone outside this portal.
                    Your information is only visible to Jamboree leaders (via the dashboard) and to you (via your unique feed link).
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-3">Removing your data</h2>
                <p class="text-gray-700 leading-relaxed">
                    Leaders can remove your record at any time, which revokes feed access and stops all email notifications.
                    If you'd like your data removed, just ask one of the Jamboree leaders and they'll take care of it.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-3">Cookies</h2>
                <p class="text-gray-700 leading-relaxed">
                    This portal uses a session cookie for leader login only. It's essential for the site to work and expires
                    after 2 hours of inactivity. We don't use any tracking, analytics, or advertising cookies.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-3">Questions?</h2>
                <p class="text-gray-700 leading-relaxed">
                    If you have any questions about how your data is handled, please speak to one of the Jamboree leaders directly.
                </p>
            </section>
        </div>
    </main>

    <footer class="border-t border-gray-200">
        <div class="max-w-4xl mx-auto px-4 py-4 text-center text-sm text-gray-500">
            <a href="{{ route('landing') }}" class="hover:text-brand-600 transition">← Back to Home</a>
        </div>
    </footer>
</body>
</html>
