<body class="flex flex-col min-h-screen">

    <main class="flex-1">
        <!-- Contenu -->
    </main>

    <!-- Footer -->
    <footer class="mt-10 border-t border-gray-200 py-4">
        <div class="max-w-3xl mx-auto px-4 text-center">
            <p class="text-sm text-gray-600">
                © 2026 Direction Générale des Impôts - République Gabonaise • Système GDF v2.1.0
            </p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
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