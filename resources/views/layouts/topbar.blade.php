{{-- resources/views/layouts/topbar.blade.php --}}
<header class="topbar">
    <div class="h-full px-4 md:px-6 flex items-center justify-between">
        
        <!-- Logo -->
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                <span class="text-white font-bold text-sm">GDF</span>
            </div>
            <h1 class="text-lg md:text-xl font-semibold text-gray-800 dark:text-white hidden md:block">
                Gestion des Dépenses Fiscales
                
            </h1>
        </div>
        
        <!-- Menu SuperAdmin SIMPLIFIÉ -->
        <div class="relative" x-data="{ open: false }">
            <button 
                @click="open = !open"
                class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
            >
                <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                    <span class="text-blue-600 dark:text-blue-300 font-medium text-sm">SA</span>
                </div>
                
                <div class="hidden md:block">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        SuperAdmin
                    </span>
                </div>
                
                <svg :class="{ 'rotate-180': open }" class="w-4 h-4 text-gray-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            
            <!-- Dropdown SIMPLIFIÉ -->
            <div 
                x-show="open" 
                @click.away="open = false"
                x-transition
                class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50"
                style="display: none;"
            >
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                    Mon profil
                </a>
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                    Paramètres
                </a>
                
                <button id="dark-mode-toggle" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <div class="flex items-center">
                        <svg id="sun-icon" class="w-4 h-4 mr-3 text-gray-600 dark:text-yellow-400" style="display: none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <svg id="moon-icon" class="w-4 h-4 mr-3 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                        </svg>
                        Mode sombre/clair
                    </div>
                </button>
                
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
</header>