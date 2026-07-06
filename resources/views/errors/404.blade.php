<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md w-full bg-white shadow-sm border border-gray-200 p-8 text-center">
        <div class="text-6xl font-bold text-yellow-500 mb-4">404</div>
        <h1 class="text-2xl font-semibold text-gray-800 mb-2">Page Not Found</h1>
        <p class="text-gray-600 mb-6">Please check the URL.</p>
        <a href="{{ url('/') }}" class="inline-block bg-[#7413dc] text-white px-6 py-2 hover:bg-purple-800 transition">
            Go to Home
        </a>
    </div>
</body>
</html>
