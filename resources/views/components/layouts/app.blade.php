<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Upload Application</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
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
