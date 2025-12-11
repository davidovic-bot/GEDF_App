{{-- resources/views/layouts/sidebar.blade.php --}}
<aside class="fixed left-0 top-16 h-[calc(100vh-4rem)] w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 z-40 overflow-y-auto">
    <nav class="py-4">
        <!-- Titre de la sidebar -->
        <div class="px-4 mb-4">
            <h2 class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                Navigation Principale
            </h2>
        </div>
        
        <!-- Item Tableau de bord -->
        <a 
            href="{{ route('superadmin.dashboard') }}" 
            class="flex items-center px-4 py-3 mx-2 rounded-lg transition-colors {{ request()->routeIs('superadmin.dashboard') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
        >
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span class="font-medium">Tableau de bord</span>
        </a>
        
        <!-- Module Parapheurs -->
        <div x-data="{ open: {{ request()->is('parapheurs*') ? 'true' : 'false' }} }" class="mt-1">
            <button 
                @click="open = !open"
                class="w-full flex items-center justify-between px-4 py-3 mx-2 rounded-lg transition-colors {{ request()->is('parapheurs*') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
            >
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span class="font-medium">Parapheurs</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 text-xs font-medium px-2 py-0.5 rounded-full">
                        42
                    </span>
                    <svg :class="{ 'rotate-180': open }" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </button>
            
            <!-- Sous-menu Parapheurs -->
            <div x-show="open" x-collapse class="ml-8 mt-1 space-y-1">
                <a href="#" class="flex items-center justify-between px-4 py-2 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-3 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>En attente</span>
                    </div>
                    <span class="bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200 text-xs px-2 py-0.5 rounded-full">42</span>
                </a>
                
                <a href="#" class="flex items-center justify-between px-4 py-2 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Validés</span>
                    </div>
                    <span class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 text-xs px-2 py-0.5 rounded-full">156</span>
                </a>
                
                <a href="#" class="flex items-center justify-between px-4 py-2 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Rejetés</span>
                    </div>
                    <span class="bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 text-xs px-2 py-0.5 rounded-full">24</span>
                </a>
                
                <a href="#" class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <span>Statistiques</span>
                </a>
            </div>
        </div>
        
        <!-- Module Statistiques -->
        <div x-data="{ open: {{ request()->is('statistics*') ? 'true' : 'false' }} }" class="mt-1">
            <button 
                @click="open = !open"
                class="w-full flex items-center justify-between px-4 py-3 mx-2 rounded-lg transition-colors {{ request()->is('statistics*') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
            >
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <span class="font-medium">Statistiques</span>
                </div>
                <svg :class="{ 'rotate-180': open }" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            
            <!-- Sous-menu Statistiques -->
            <div x-show="open" x-collapse class="ml-8 mt-1 space-y-1">
                <a href="#" class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>Journalières</span>
                </a>
                
                <a href="#" class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>Mensuelles</span>
                </a>
                
                <a href="#" class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span>Par service</span>
                </a>
                
                <a href="#" class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <span>Performance</span>
                </a>
            </div>
        </div>
        
        <!-- Module Administration -->
        <div x-data="{ open: {{ request()->is('admin*') ? 'true' : 'false' }} }" class="mt-1">
            <button 
                @click="open = !open"
                class="w-full flex items-center justify-between px-4 py-3 mx-2 rounded-lg transition-colors {{ request()->is('admin*') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
            >
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    </svg>
                    <span class="font-medium">Administration</span>
                </div>
                <svg :class="{ 'rotate-180': open }" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            
            <!-- Sous-menu Administration -->
            <div x-show="open" x-collapse class="ml-8 mt-1 space-y-1">
                <a href="#" class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.67 3.623a10.953 10.953 0 01-7.67 3.123 10.953 10.953 0 01-7.67-3.123m14.34-8.334a10.946 10.946 0 00-14.34 0M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Utilisateurs</span>
                </a>
                
                <a href="#" class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    <span>Rôles & Permissions</span>
                </a>
                
                <a href="#" class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    </svg>
                    <span>Configuration</span>
                </a>
            </div>
        </div>
    </nav>
    
    <!-- Pied de la sidebar -->
    <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 mt-4">
        <p class="text-xs text-gray-500 dark:text-gray-400">
            GDF v1.0 • {{ date('d/m/Y') }}
        </p>
    </div>
</aside>