<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>    
    @livewireStyles
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex">
        @auth
            @include('partials.admin-sidebar')
        @endauth

        <!-- Main Content -->
        <div class="flex-1">
            <main class="py-6 sm:px-6 lg:px-8">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
    <script>
        // Mobile menu toggle
        document.querySelector('button').addEventListener('click', () => {
            document.querySelector('.bg-gray-800').classList.toggle('-translate-x-full');
        });
    </script>
</body>
</html>
