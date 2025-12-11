<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ session('dark_mode', false) ? 'dark' : '' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'GDF - SuperAdmin')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Scripts & Styles avec Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js ABSOLUMENT NÉCESSAIRE -->
    <script src="//unpkg.com/alpinejs" defer></script>
    
    <style>
        /* Marges fixes POUR TOUS LES ÉCRANS */
        body {
            overflow-x: hidden;
        }
        
        aside.sidebar {
            position: fixed;
            left: 0;
            top: 64px;
            width: 256px;
            height: calc(100vh - 64px);
            background: white;
            border-right: 1px solid #e5e7eb;
            z-index: 40;
        }
        
        header.topbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 64px;
            background: white;
            border-bottom: 1px solid #e5e7eb;
            z-index: 50;
        }
        
        main.content-area {
            margin-left: 256px;
            padding-top: 64px;
            min-height: calc(100vh - 64px);
            width: calc(100% - 256px);
            background: #f9fafb;
        }
        
        .dark aside.sidebar {
            background: #1f2937;
            border-right: 1px solid #374151;
        }
        
        .dark header.topbar {
            background: #1f2937;
            border-bottom: 1px solid #374151;
        }
        
        .dark main.content-area {
            background: #111827;
        }
        
        /* Animation pour les sous-menus */
        [x-cloak] { display: none !important; }
        
        .rotate-180 {
            transform: rotate(180deg);
        }
        
        .transition-transform {
            transition: transform 0.2s ease-in-out;
        }
    </style>
    
    @stack('styles')
</head>
<body class="font-sans antialiased">
    
    <!-- Topbar -->
    @include('layouts.topbar')
    
    <!-- Sidebar -->
    @include('layouts.sidebar')
    
    <!-- Contenu principal -->
    <main class="content-area">
        <div class="p-4 md:p-6">
            @yield('content')
        </div>
    </main>
    
    <!-- Scripts additionnels -->
    <script>
        // FORCER le chargement d'Alpine.js si pas chargé
        if (typeof Alpine === 'undefined') {
            console.warn('Alpine.js non chargé, tentative de chargement...');
            var script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js';
            script.defer = true;
            document.head.appendChild(script);
            
            // Attendre qu'Alpine soit chargé
            script.onload = function() {
                console.log('Alpine.js chargé avec succès');
                Alpine.start();
            };
        }
        
        // Mode sombre/clair
        document.addEventListener('DOMContentLoaded', function() {
            // Initialiser le mode sombre
            const isDarkMode = localStorage.getItem('dark-mode') === 'true';
            if (isDarkMode) {
                document.documentElement.classList.add('dark');
                const sunIcon = document.getElementById('sun-icon');
                const moonIcon = document.getElementById('moon-icon');
                if (sunIcon) sunIcon.classList.remove('hidden');
                if (moonIcon) moonIcon.classList.add('hidden');
            }
            
            // Gestion du bouton dark mode
            const darkModeToggle = document.getElementById('dark-mode-toggle');
            if (darkModeToggle) {
                darkModeToggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    
                    const html = document.documentElement;
                    const sunIcon = document.getElementById('sun-icon');
                    const moonIcon = document.getElementById('moon-icon');
                    
                    if (html.classList.contains('dark')) {
                        html.classList.remove('dark');
                        localStorage.setItem('dark-mode', 'false');
                        if (sunIcon) sunIcon.classList.add('hidden');
                        if (moonIcon) moonIcon.classList.remove('hidden');
                    } else {
                        html.classList.add('dark');
                        localStorage.setItem('dark-mode', 'true');
                        if (sunIcon) sunIcon.classList.remove('hidden');
                        if (moonIcon) moonIcon.classList.add('hidden');
                    }
                });
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>