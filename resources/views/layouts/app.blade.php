<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    
<script src="https://cdn.tailwindcss.com"></script>


    @livewireStyles
    <title>Product Configurator</title>
</head>
<body class="bg-gray-100 text-gray-800 min-h-screen flex flex-col">

    <!-- Navbar -->
    <nav class="bg-blue-600 text-white px-6 py-4 shadow">
        <div class="container mx-auto flex justify-between items-center">
            <!-- Website Name -->
            <div class="text-2xl font-bold">
                {{ config('app.name', 'ToyKart') }}
            </div>

            <!-- Right side placeholder -->
            <div class="space-x-4 hidden md:flex">
                <a href="/" class="hover:underline">Home</a>
                <a href="/shop" class="hover:underline">Products</a>
                <a href="#" class="hover:underline">Contact</a>
            </div>
        </div>
    </nav>

    <main class="flex-grow py-6 px-4">
        @yield('content')
    </main>

    @livewireScripts

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-4">
        <div class="container mx-auto text-center text-sm">
            &copy; {{ date('Y') }} {{ config('app.name', 'ToyKart') }}. All rights reserved.
        </div>
    </footer>

</body>
</html>
