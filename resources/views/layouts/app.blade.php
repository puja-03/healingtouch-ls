<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Upload Application</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <nav class="bg-white shadow-lg">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between">
                <div class="flex space-x-7">
                    <div>
                        <a href="/" class="flex items-center py-4">
                            <span class="font-semibold text-gray-700 text-lg">hello </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </nav>
    <div class="flex-1">
        <main class="py-6 sm:px-6 lg:px-8">
            {{ $slot }}
        </main>
    </div>
    <main class="py-6">
        @yield('content')
    </main>

</body>

</html>
