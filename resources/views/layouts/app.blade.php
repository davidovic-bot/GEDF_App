<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DGI - Gestion des Dépenses Fiscales</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background: #2c3e50;
        }
        .nav-link {
            color: #ecf0f1;
        }
        .nav-link:hover {
            background: #34495e;
            color: #fff;
        }
        .badge-statut {
            font-size: 0.8em;
            padding: 4px 8px;
        }
        .nav-link.active {
            background: #3498db;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse bg-dark">
                <div class="position-sticky pt-3">
                    <!-- TITRE DYNAMIQUE SIDEBAR -->
                    <h5 class="text-white px-3">
                        @if(request()->is('courriers*'))
                            DGI - Module Courrier
                        @elseif(request()->is('parapheurs*'))
                            DGI - Module Parapheur
                        @elseif(request()->is('dashboard'))
                            DGI - Tableau de bord
                        @else
                            DGI - GDF
                        @endif
                    </h5>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" 
                               href="{{ route('dashboard') }}">
                                <i class="fas fa-tachometer-alt"></i> Tableau de bord
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('courriers*') ? 'active' : '' }}" 
                               href="{{ route('courriers.index') }}">
                                <i class="fas fa-envelope"></i> Courriers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('parapheurs*') ? 'active' : '' }}" 
                               href="{{ route('parapheurs.index') }}">
                                <i class="fas fa-folder"></i> Parapheurs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-chart-bar"></i> Statistiques
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-users-cog"></i> Administration
                            </a>
                        </li>
                    </ul>
                    
                    <hr class="text-white">
                    
                    <div class="px-3 text-white">
                        <small>Connecté en tant que:</small>
                        <p class="mb-1">{{ auth()->user()->name }}</p>
                        <small class="text-muted">{{ auth()->user()->email }}</small>
                        <br>
                        <small class="text-info">
                            @if(request()->is('courriers*'))
                                📨 Gestion des courriers
                            @elseif(request()->is('parapheurs*'))
                                📋 Circuit de validation
                            @endif
                        </small>
                    </div>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <!-- TITRE DYNAMIQUE PRINCIPAL -->
                    <div>
                        <h1 class="h2 mb-1">
                            @if(request()->is('courriers*'))
                                <i class="fas fa-envelope me-2"></i> 📨 Module Courrier - GDF
                            @elseif(request()->is('parapheurs*'))
                                <i class="fas fa-folder me-2"></i> 📋 Module Parapheur - GDF
                            @else
                                <i class="fas fa-home me-2"></i> @yield('title', 'Tableau de bord')
                            @endif
                        </h1>
                        <p class="text-muted mb-0">
                            @if(request()->is('courriers*'))
                                Traitement des demandes d'exonération ou de dispense de TVA
                            @elseif(request()->is('parapheurs*'))
                                Circuit de validation des demandes fiscales - Direction des Régimes Spécifiques
                            @else
                                Gestion des Dépenses Fiscales
                            @endif
                        </p>
                    </div>
                    
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <!-- BOUTONS DYNAMIQUES -->
                        @if(request()->is('courriers*'))
                            <div class="btn-group me-2">
                                <a href="{{ route('parapheurs.index') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-folder me-1"></i> Voir les parapheurs
                                </a>
                                @if(auth()->user()->hasRole(['secretaire', 'admin', 'superadmin']))
                                <a href="{{ route('courriers.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i> Nouveau courrier
                                </a>
                                @endif
                            </div>
                        @elseif(request()->is('parapheurs*'))
                            <div class="btn-group me-2">
                                <a href="{{ route('courriers.index') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-envelope me-1"></i> Retour aux courriers
                                </a>
                                @if(auth()->user()->hasRole(['secretaire', 'admin', 'superadmin']))
                                <a href="{{ route('courriers.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i> Nouveau courrier
                                </a>
                                @endif
                            </div>
                        @else
                            @yield('actions')
                        @endif
                    </div>
                </div>

                <!-- Messages flash -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Contenu principal -->
                <div class="container-fluid">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>