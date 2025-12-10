{{-- resources/views/layouts/topbar.blade.php --}}
<header class="fixed top-0 left-0 right-0 h-16 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 z-50">
    <div class="flex items-center justify-between h-full px-4 md:px-6">
        <!-- Logo et titre -->
        <div class="flex items-center space-x-3">
            <!-- Bouton menu mobile (visible seulement sur mobile) -->
            <button id="mobile-menu-button" class="md:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            
            <!-- Logo -->
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-sm">GDF</span>
                </div>
                <h1 class="text-lg md:text-xl font-semibold text-gray-800 dark:text-white hidden md:block">
                    @yield('page-title', 'Tableau de Bord')
                </h1>
            </div>
        </div>
        
        <!-- Actions droite -->
        <div class="flex items-center space-x-2 md:space-x-4">
            <!-- Notifications (Version corrigée - sans erreur de base de données) -->
<div class="relative">
    <button id="notifications-button" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 relative">
        <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>
        
        {{-- Version SÉCURISÉE sans accès à la base de données --}}
        @php
            // D'abord, vérifiez si la table notifications existe
            // Sinon, utilisez une valeur par défaut
            $unreadNotificationsCount = 0;
            
            // Optionnel : vous pouvez vérifier si la table existe
            // try {
            //     if (\Illuminate\Support\Facades\Schema::hasTable('notifications')) {
            //         $unreadNotificationsCount = auth()->user()->unreadNotifications()->count();
            //     }
            // } catch (Exception $e) {
            //     $unreadNotificationsCount = 0;
            // }
        @endphp
        
        @if($unreadNotificationsCount > 0)
            <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">
                {{ $unreadNotificationsCount }}
            </span>
        @endif
    </button>
</div>
            
            <!-- Dark/Light Mode Toggle -->
            <button id="dark-mode-toggle" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                <svg id="dark-mode-icon" class="w-5 h-5 text-gray-600 dark:text-yellow-400 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <svg id="light-mode-icon" class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                </svg>
            </button>
            
            <!-- Séparateur -->
            <div class="w-px h-6 bg-gray-300 dark:bg-gray-600 hidden md:block"></div>
            
            <!-- Profil utilisateur -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <div class="text-right hidden md:block">
                        <p class="text-sm font-medium text-gray-800 dark:text-white">
                            {{ auth()->user()->name }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Super Admin
                        </p>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                        <span class="text-blue-600 dark:text-blue-300 font-medium text-sm">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </span>
                    </div>
                </button>
                
                <!-- Dropdown menu -->
                <div x-show="open" @click.away="open = false" 
                     class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50"
                     x-transition>
                    <span class="block px-4 py-2 text-sm text-gray-500 dark:text-gray-400">
    Mon profil
</span>
<span class="block px-4 py-2 text-sm text-gray-500 dark:text-gray-400">
    Paramètres
</span>
                    <div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                            Déconnexion
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

@push('scripts')
<script>
    // Dark mode toggle
    document.getElementById('dark-mode-toggle').addEventListener('click', function() {
        const html = document.documentElement;
        const darkIcon = document.getElementById('dark-mode-icon');
        const lightIcon = document.getElementById('light-mode-icon');
        
        if (html.classList.contains('dark')) {
            html.classList.remove('dark');
            localStorage.setItem('dark-mode', 'false');
            darkIcon.classList.add('hidden');
            lightIcon.classList.remove('hidden');
        } else {
            html.classList.add('dark');
            localStorage.setItem('dark-mode', 'true');
            darkIcon.classList.remove('hidden');
            lightIcon.classList.add('hidden');
        }
    });
    
    // Menu mobile toggle
    document.getElementById('mobile-menu-button').addEventListener('click', function() {
        const sidebar = document.getElementById('sidebar');
        const main = document.querySelector('main');
        
        if (sidebar.classList.contains('-translate-x-full')) {
            sidebar.classList.remove('-translate-x-full');
            main.classList.add('ml-64');
        } else {
            sidebar.classList.add('-translate-x-full');
            main.classList.remove('ml-64');
        }
    });
</script>
@endpush