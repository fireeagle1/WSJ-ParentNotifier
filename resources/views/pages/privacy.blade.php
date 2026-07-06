<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - Jamboree Updates Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <header class="bg-wsj-navy">
        <div class="max-w-4xl mx-auto px-4 py-5 flex items-center justify-between">
            <a href="{{ route('landing') }}" class="text-xl font-bold text-white">{{ \App\Models\Setting::where('key', 'brand_name')->value('value') ?? 'Scouts' }}</a>
            <a href="{{ route('login') }}" class="text-sm font-medium text-blue-200 hover:text-white transition">
                Leader Login →
            </a>
        </div>
    </header>

    <main class="flex-1 max-w-3xl mx-auto px-4 py-12">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Privacy Policy</h1>

        <div class="prose prose-gray max-w-none space-y-6">
            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-3">What Data We Collect</h2>
                <p class="text-gray-700 leading-relaxed">
                    We collect the minimum data necessary to operate this portal:
                </p>
                <ul class="list-disc list-inside text-gray-700 mt-2 space-y-1">
                    <li><strong>Parent email addresses</strong> — used to send notification emails when new posts are published.</li>
                    <li><strong>Young person's name</strong> — used to identify which Scout the parent is associated with.</li>
                    <li><strong>Leader email addresses and display names</strong> — used for authentication and identifying post authors.</li>
                    <li><strong>Photos</strong> — uploaded by leaders as part of Jamboree updates, stored on the server.</li>
                    <li><strong>Location data</strong> — optional map pin coordinates attached to posts by leaders.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-3">How We Use Your Data</h2>
                <p class="text-gray-700 leading-relaxed">
                    Your data is used solely for the operation of this Jamboree updates portal:
                </p>
                <ul class="list-disc list-inside text-gray-700 mt-2 space-y-1">
                    <li>Email addresses are used to send notifications when new Jamboree updates are posted.</li>
                    <li>Access tokens are generated to provide parents with unique, secure links to view the feed.</li>
                    <li>Photos and location pins are displayed in the feed to keep parents informed of Jamboree progress.</li>
                    <li>Display names are shown alongside posts to identify which leader posted an update.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-3">Data Storage and Security</h2>
                <p class="text-gray-700 leading-relaxed">
                    All data is stored on the hosting server. Photos are stored in the application's local filesystem.
                    Passwords are hashed and never stored in plain text. Access tokens are unique, randomly generated
                    strings that cannot be guessed.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-3">Data Sharing</h2>
                <p class="text-gray-700 leading-relaxed">
                    We do not sell, trade, or share your personal data with third parties. Data is only accessible
                    to Jamboree leaders (via the dashboard) and parents (via their unique token links).
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-3">Data Removal</h2>
                <p class="text-gray-700 leading-relaxed">
                    Leaders can remove parent records from the system at any time, which revokes access and stops
                    email notifications. If you would like your data removed, please contact a Jamboree leader.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-3">Cookies and Sessions</h2>
                <p class="text-gray-700 leading-relaxed">
                    This portal uses session cookies for leader authentication. These cookies are essential for
                    the portal to function and expire after 2 hours of inactivity. No tracking or analytics
                    cookies are used.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-3">Contact</h2>
                <p class="text-gray-700 leading-relaxed">
                    For any questions about this privacy policy or your data, please contact a Jamboree leader
                    directly.
                </p>
            </section>
        </div>
    </main>

    <footer class="bg-white border-t border-gray-200">
        <div class="max-w-4xl mx-auto px-4 py-4 text-center text-sm text-gray-500">
            <a href="{{ route('landing') }}" class="hover:text-gray-700">Back to Home</a>
        </div>
    </footer>
</body>
</html>
