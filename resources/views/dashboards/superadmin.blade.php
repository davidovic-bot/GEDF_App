<!DOCTYPE html>
<html lang="fr" x-data="dashboardApp()" x-cloak>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GDF - Tableau de bord GDF </title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome pour les icônes -->
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
                        // Topbar bleu GDF (différent du bleu drapeau)
                        'topbar-blue': '#042683ea',
                        // Couleurs drapeau Gabon
                        'flag-green': '#046604ff',
                        'flag-yellow': '#FCD116',
                        'flag-blue': '#3B82F6', // Bleu drapeau (différent)
                    }
                }
            }
        }
    </script>
    
    <style>
        [x-cloak] { display: none !important; }
        
        /* Barre tricolore Gabon */
        .flag-bar {
            height: 4px;
            background: linear-gradient(90deg, 
                #009E60 33.33%, 
                #FCD116 33.33%, #FCD116 66.66%, 
                #3B82F6 66.66% /* Bleu drapeau différent */
            );
        }
        
        /* Topbar navigation GDF */
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
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen">
    <!-- Header Institutionnel DGI -->
    <header class="bg-white">
        <!-- Barre blanche avec logo, titre et sceau -->
        <div class="py-3 border-b border-gray-200">
            <div class="container mx-auto px-6">
                <div class="flex items-center justify-between">
                    <!-- LOGO DGI À GAUCHE -->
<div class="w-24 h-16 flex items-center">
    <img src="{{ asset('images/logo DGI.jpg') }}" alt="DGI" class="h-full w-auto object-contain">
</div>
    
                    <!-- Titre centré -->
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
                    
                    <!-- SCEAU RÉPUBLIQUE À DROITE -->
                    <div class="w-24 h-16 flex items-center">
                            <img src="{{ asset('images/sceau-gabon.jpg') }}" alt="République du Gabon" class="h-full w-auto object-contain"> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Barre drapeau Gabon -->
        <div class="flag-bar"></div>
        
        <!-- Topbar Navigation GDF -->
        <nav class="bg-topbar-blue">
            <div class="container mx-auto px-6">
                <div class="flex items-center justify-between">
                    <!-- Logo GDF + Navigation -->
                    <div class="flex items-center">
                        <!-- LOGO GDF À GAUCHE -->
                        <div class="mr-6 w-12 h-12 flex items-center">
                            <div class="w-full h-full bg-white/20 border border-white/ 30 rounded flex items-center">         
                                <img src="{{ asset('images/logo GDF.png') }}" alt="GDF" class="magic">
                            </div>
                        </div>
                        
                        <!-- Navigation -->
                        <div class="flex items-center">
                            <a href="/tableau de bord" class="nav-item active">Tableau de bord</a>
                            <a href="/parapheurs" class="nav-item">Parapheurs</a>
                            <a href="/statistiques" class="nav-item">Statistiques</a>
                            <a href="/Adlinistration" class="nav-item">Administration</a>
                        </div>
                    </div>
                    
                    <!-- Actions & User -->
                    <div class="flex items-center space-x-4">
                        <!-- Superadmin -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" 
                                    class="flex items-center space-x-2 text-white hover:bg-white/10 px-3 py-2 rounded-lg">
                                <div class="text-right">
                                    <p class="text-sm font-medium">Superadmin</p>
                                    <p class="text-xs text-blue-200">Système GDF</p>
                                </div>
                                <div class="w-8 h-8 bg-white text-topbar-blue rounded-full flex items-center justify-center font-bold">
                                    SA
                                </div>
                            </button>
                            <div x-show="open" @click.away="open = false" 
                                 class="absolute right-0 mt-2 bg-white rounded-lg shadow-lg border py-2 min-w-48 z-50">
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil</a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Paramètres</a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Mode sombre/clair</a>
                                <hr class="my-2">
                                <a href="#" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">Déconnexion</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Contenu principal -->
    <main class="container mx-auto px-6 py-8">
        <!-- Titre Dashboard -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Tableau de bord GDF</h1>
            <p class="text-gray-600 mt-1">Supervision complète du système de gestion électronique des parapheurs</p>
            
            <!-- Indicateurs rapides -->
            <div class="mt-4 flex flex-wrap gap-3">
                <div class="flex items-center text-sm text-green-700 bg-green-50 px-3 py-1.5 rounded-lg">
                    <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                    Système opérationnel
                </div>
                <div class="flex items-center text-sm text-blue-700 bg-blue-50 px-3 py-1.5 rounded-lg">
                    <div class="w-2 h-2 bg-blue-500 rounded-full mr-2"></div>
                    10 utilisateurs connectés
                </div>
                <div class="flex items-center text-sm text-yellow-700 bg-yellow-50 px-3 py-1.5 rounded-lg">
                    <div class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></div>
                    3 alertes en attente
                </div>
            </div>
        </div>

        <!-- Stats Cards Essentielles -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Parapheurs en attente -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <p class="text-sm text-gray-500 mb-1">PARAPHEURS EN ATTENTE</p>
                <div class="flex items-baseline">
                    <span class="text-3xl font-bold text-gray-900">18</span>
                    <span class="ml-2 text-sm text-red-600">dont 3 en retard</span>
                </div>
                <p class="text-xs text-green-600 mt-2">+2 depuis hier</p>
            </div>

            <!-- Parapheurs validés -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <p class="text-sm text-gray-500 mb-1">PARAPHEURS VALIDÉS</p>
                <div class="flex items-baseline">
                    <span class="text-3xl font-bold text-gray-900">47</span>
                    <span class="ml-2 text-sm text-gray-500">ce mois</span>
                </div>
                <p class="text-xs text-green-600 mt-2">+8% vs mois dernier</p>
            </div>

            <!-- Utilisateurs actifs -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <p class="text-sm text-gray-500 mb-1">UTILISATEURS ACTIFS</p>
                <div class="flex items-baseline">
                    <span class="text-3xl font-bold text-gray-900">10</span>
                    <span class="ml-2 text-sm text-gray-500">sur 6 rôles</span>
                </div>
                <p class="text-xs text-gray-500 mt-2">stable cette semaine</p>
            </div>

            <!-- Délai moyen -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <p class="text-sm text-gray-500 mb-1">DÉLAI MOYEN</p>
                <div class="flex items-baseline">
                    <span class="text-3xl font-bold text-gray-900">2,8</span>
                    <span class="ml-2 text-sm text-gray-500">jours</span>
                </div>
                <p class="text-xs text-green-600 mt-2">-0,3 jour</p>
            </div>
        </div>
        

        <!-- Section informations essentielles -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Prochaines échéances -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Prochaines échéances</h3>
                <div class="space-y-3">
                    <div>
                        <p class="font-medium text-gray-900">Rapport mensuel d'activité</p>
                        <p class="text-sm text-gray-600">Rapport de performance du système</p>
                        <p class="text-xs text-blue-600 mt-1">Dans 3 jours</p>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Audit de sécurité</p>
                        <p class="text-sm text-gray-600">Audit semestriel du système</p>
                        <p class="text-xs text-blue-600 mt-1">15/04/2024</p>
                    </div>
                </div>
            </div>

            <!-- Performances système -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Performances système</h3>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm text-gray-600">Temps de réponse</span>
                            <span class="font-bold text-green-600">98,7%</span>
                        </div>
                        <p class="text-xs text-gray-500">Objectif : 95%</p>
                    </div>
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm text-gray-600">Disponibilité</span>
                            <span class="font-bold text-green-600">99,9%</span>
                        </div>
                        <p class="text-xs text-gray-500">Objectif : 99,5%</p>
                    </div>
                </div>
            </div>

            <!-- Support technique -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Support technique</h3>
                <p class="text-sm text-gray-600 mb-4">Pour toute assistance technique :</p>
                <div class="space-y-2">
                    <p class="text-blue-600 font-medium">support-drs@dgi.gov.ga</p>
                    <p class="text-blue-600 font-medium">+241 01 44 08 08</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer simple -->
    <footer class="mt-10 border-t border-gray-200 py-4">
        <div class="container mx-auto px-6 text-center">
            <p class="text-sm text-gray-600">
                © 2024 Direction Générale des Impôts - République du Gabon • Système GDF v2.1.0
            </p>
        </div>
    </footer>

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
</body>
</html>