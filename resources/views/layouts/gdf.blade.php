<!DOCTYPE html>
<html lang="fr" x-data="dashboardApp()" x-cloak>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'DGI - GDF')</title>
    
    <!-- Tailwind CSS CDN (pour le layout) -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Bootstrap CSS (pour le contenu) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Configuration Tailwind -->
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'topbar-blue': '#042683ea',
                        'flag-green': '#046604ff',
                        'flag-yellow': '#FCD116',
                        'flag-blue': '#3B82F6',
                    }
                }
            }
        }
    </script>
    
    <style>
        [x-cloak] { display: none !important; }
        
        .flag-bar {
            height: 4px;
            background: linear-gradient(90deg, 
                #009E60 33.33%, 
                #FCD116 33.33%, #FCD116 66.66%, 
                #3B82F6 66.66%
            );
        }
        
        .nav-item {
            padding: 1rem 1.25rem;
            color: white;
            font-weight: 500;
            border-bottom: 3px solid transparent;
            transition: all 0.2s;
        }
        
        .nav-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-bottom-color: #FCD116;
        }
        
        .nav-item.active {
            background-color: rgba(255, 255, 255, 0.15);
            border-bottom-color: #FCD116;
        }
        
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen">
    <!-- Header Institutionnel DGI -->
    <header class="bg-white">
        <div class="py-3 border-b border-gray-200">
            <div class="container mx-auto px-6">
                <div class="flex items-center justify-between">
                    <div class="w-24 h-16 flex items-center">
                        <img src="{{ asset('images/logo DGI.jpg') }}" alt="DGI" class="h-full w-auto object-contain">
                    </div>
                    
                    <div class="text-center flex-1 mx-8">
                        <h1 class="text-sm font-bold text-gray-900 leading-tight">
                            Ministère De L'Économie, Des Finances, De La Dette<br>
                            Et Des Participations, Chargé De La Lutte Contre La Vie Chère
                        </h1>
                        <h2 class="text-base font-bold text-blue-800 mt-1">
                            Direction Générale des Impôts
                        </h2>
                        <p class="text-xs text-gray-600 mt-1">L'impôt au cœur du développement</p>
                    </div>
                    
                    <div class="w-24 h-16 flex items-center">
                        <img src="{{ asset('images/sceau-gabon.jpg') }}" alt="République du Gabon" class="h-full w-auto object-contain">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="flag-bar"></div>
        
        <nav class="bg-topbar-blue">
            <div class="container mx-auto px-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="mr-6 w-12 h-12 flex items-center">
                            <div class="w-full h-full bg-white/20 border border-white/30 rounded flex items-center">
                                <img src="{{ asset('images/logo GDF.png') }}" alt="GDF" class="magic">
                            </div>
                        </div>
                        
                        <div class="flex items-center">
    <a href="{{ route('courriers.index') }}" class="nav-item {{ request()->routeIs('courriers*') ? 'active' : '' }}">Courriers</a>
    <a href="{{ route('parapheurs.index') }}" class="nav-item {{ request()->routeIs('parapheurs*') ? 'active' : '' }}">Parapheurs</a>

    {{-- Tableau de bord adapté au rôle --}}
    @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('admin'))
        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Tableau de bord</a>
    @else
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">Tableau de bord</a>
    @endif

    {{-- Administration (superadmin uniquement) --}}
    @if(auth()->user()->hasRole('superadmin'))
        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.*') ? 'active' : '' }}">Administration</a>
        <a href="{{ route('admin.statistiques.index') }}" class="nav-item {{ request()->routeIs('admin.statistiques*') ? 'active' : '' }}">Statistiques</a>
    @endif
</div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" 
                                    class="flex items-center space-x-2 text-white hover:bg-white/10 px-3 py-2 rounded-lg">
                                <div class="text-right">
                                    <p class="text-sm font-medium">{{ auth()->user()->name ?? 'Superadmin' }}</p>
                                    <p class="text-xs text-blue-200">Système GDF</p>
                                </div>
                                <div class="w-8 h-8 bg-white text-topbar-blue rounded-full flex items-center justify-center font-bold">
                                    SA
                                </div>
                            </button>
                            <div x-show="open" @click.away="open = false" 
                                 class="absolute right-0 mt-2 bg-white rounded-lg shadow-lg border py-2 min-w-48 z-50">
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil</a>
                                <hr class="my-2">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-red-600 hover:bg-red-50">Déconnexion</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <main class="container mx-auto px-6 py-8">
        @yield('content')
    </main>

    <footer class="mt-10 border-t border-gray-200 py-4">
        <div class="container mx-auto px-6 text-center">
            <p class="text-sm text-gray-600">
                © 2025 Direction Générale des Impôts - République Gabonaise • Système GDF v2.1.0
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function dashboardApp() {
            return {
                darkMode: false,
                
                toggleDarkMode() {
                    this.darkMode = !this.darkMode;
                    if (this.darkMode) {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                }
            }
        }
    </script>
    @stack('scripts')
</body>
</html>