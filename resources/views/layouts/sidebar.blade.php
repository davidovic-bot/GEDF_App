{{-- resources/views/layouts/topbar.blade.php --}}
<header class="fixed top-0 left-0 right-0 h-16 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 z-50">
    <div class="flex items-center justify-between h-full px-4 md:px-6">
        <!-- Logo et titre -->
        <div class="flex items-center space-x-3">
            <!-- Bouton menu mobile -->
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
            <!-- Dark/Light Mode Toggle -->
            <button id="dark-mode-toggle" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                <svg id="dark-mode-icon" class="w-5 h-5 text-gray-600 dark:text-yellow-400 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <svg id="light-mode-icon" class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-