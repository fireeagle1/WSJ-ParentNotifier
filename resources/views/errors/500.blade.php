<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Error</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md w-full bg-white shadow-sm border border-gray-200 p-8 text-center">
        <div class="text-6xl font-bold text-gray-500 mb-4">500</div>
        <h1 class="text-2xl font-semibold text-gray-800 mb-2">Something Went Wrong</h1>
        <p class="text-gray-600 mb-6">Please try again later.</p>
        <a href="{{ url('/') }}" class="inline-block bg-wsj-navy text-white px-6 py-2 hover:bg-wsj-navy/90 transition">
            Go to Home
        </a>
    </div>
</body>
</html>
