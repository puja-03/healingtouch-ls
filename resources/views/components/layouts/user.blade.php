<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Dashboard - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>    
    @livewireStyles
</head>
<body class="min-h-screen bg-gray-100">
    <nav class="bg-white shadow mb-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="shrink-0 flex items-center"> 
                        <a href="/">{{ config('app.name') }}</a>
                    </div>
                </div>
                <div class="flex items-center">
                    @auth
                        <span class="mr-4">{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-sm text-red-600">Logout</button>
                        </form>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{ $slot }}
    </main>

    @livewireScripts
    @stack('scripts')
</body>
</html>