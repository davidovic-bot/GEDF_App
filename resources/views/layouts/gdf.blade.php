<!DOCTYPE html>
<html lang="fr" x-data="dashboardApp()" x-cloak>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'DGI - GDF')</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- AlpineJS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Tailwind Config -->
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'topbar-blue': '#042683ea',
                        'flag-green': '#009E60',
                        'flag-yellow': '#FCD116',
                        'flag-blue': '#3B82F6',
                    }
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }

        /* Glass background */
        body {
            background: radial-gradient(circle at 20% 20%, rgba(59,130,246,0.25), transparent 40%),
                        radial-gradient(circle at 80% 0%, rgba(16,185,129,0.20), transparent 40%),
                        radial-gradient(circle at 50% 100%, rgba(250,204,21,0.20), transparent 40%),
                        #0f172a;
        }

        /* Glass card principal */
        .glass {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.25);
        }

        /* Navigation glass */
        .nav-item {
            padding: 1rem 1.25rem;
            color: white;
            font-weight: 500;
            border-bottom: 3px solid transparent;
            transition: all 0.25s ease;
            text-decoration: none;
        }

        .nav-item:hover {
            background-color: rgba(255, 255, 255, 0.08);
            border-bottom-color: #FCD116;
        }

        .nav-item.active {
            background-color: rgba(255, 255, 255, 0.12);
            border-bottom-color: #FCD116;
        }

        /* Flag bar */
        .flag-bar {
            height: 4px;
            background: linear-gradient(
                90deg,
                #009E60 33.33%,
                #FCD116 33.33%,
                #FCD116 66.66%,
                #3B82F6 66.66%
            );
        }

        /* Cards internes */
        .card-box {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(12px);
            border-radius: 14px;
            transition: all 0.3s ease;
        }

        .card-box:hover {
            transform: translateY(-4px);
            background: rgba(255, 255, 255, 0.10);
        }
    </style>

    @stack('styles')
</head>

<!-- BODY GLASSMORPHISM -->
<body class="min-h-screen flex flex-col text-white antialiased selection:bg-blue-500 selection:text-white">

    {{-- HEADER --}}
    @include('layouts.header')

    {{-- CONTENU --}}
    <main class="flex-1 flex justify-center px-4 sm:px-6 lg:px-8 py-10">

        <!-- Glass container principal -->
        <div class="glass w-full max-w-6xl rounded-3xl p-6 sm:p-10">

            @yield('content')

        </div>

    </main><br><br><br><br><br><br>

    {{-- FOOTER --}}
    @include('layouts.footer')

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function dashboardApp() {
            return {
                darkMode: true,

                toggleDarkMode() {Tableau de bord
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
</htm