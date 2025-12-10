{{-- resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ session('dark_mode', false) ? 'dark' : '' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', config('app.name', 'GDF')) - SuperAdmin</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Scripts & Styles avec Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Styles additionnels -->
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900">
    <!-- Topbar -->
    @include('layouts.topbar')
    
    <!-- Sidebar -->
    @include('layouts.sidebar')
    
    <!-- Contenu principal -->
    <main class="ml-0 md:ml-64 pt-16 transition-all duration-300 min-h-screen">
        <div class="p-4 md:p-6">
            @yield('content')
        </div>
    </main>
    
    <!-- Scripts -->
    @stack('scripts')
</body>
</html>