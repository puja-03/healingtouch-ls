<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <x-header />
    <div class="flex-1">
        <main class="py-6 sm:px-6 lg:px-8">
            {{ $slot }}
        </main>
    </div>
    <x-footer />
   <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    @livewireScripts
</body>

</html>
