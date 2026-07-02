<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md w-full bg-white rounded-lg shadow-md p-8 text-center">
        <div class="text-6xl font-bold text-red-500 mb-4">403</div>
        <h1 class="text-2xl font-semibold text-gray-800 mb-2">Access Denied</h1>
        <p class="text-gray-600 mb-6">This link may be invalid or has been revoked.</p>
        <a href="{{ url('/') }}" class="inline-block bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
            Go to Home
        </a>
    </div>
</body>
</html>
