<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archives - GDF</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .badge-type {
            font-size: 0.75em;
            padding: 4px 8px;
        }
        .badge-exoneration { background-color: #e3f2fd; color: #1565c0; }
        .badge-dispense_tva { background-color: #e8f5e9; color: #2e7d32; }
        .badge-rejet { background-color: #ffebee; color: #c62828; }
        .badge-autre { background-color: #f5f5f5; color: #616161; }
        
        .badge-archive {
            font-size: 0.8em;
            padding: 4px 10px;
            background-color: #f5f5f5;
            color: #616161;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.05);
        }
        
        .action-buttons .btn {
            padding: 3px 8px;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('courriers.index') }}">
                <i class="fas fa-landmark me-2"></i>
                GDF - Archives
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('courriers.index') }}">
                            <i class="fas fa-folder-open me-1"></i> Dossiers actifs
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('courriers.archives') }}">
                            <i class="fas fa-archive me-1"></i> Archives
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-user me-1"></i> 
                            @auth
                                {{ Auth::user()->name }}
                            @else
                                Utilisateur
                            @endauth
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <!-- En-tête -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-2">
                    <i class="fas fa-archive text-secondary me-2"></i>
                    Archives des dossiers fiscaux
                </h1>
                <p class="text-muted mb-0">
                    Consultation des dossiers fiscaux archivés
                </p>
            </div>
            <div>
                <a href="{{ route('courriers.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Retour aux dossiers actifs
                </a>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-archive fa-3x text-secondary"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="card-title">{{ $archives->total() ?? 0 }} dossiers archivés</h5>
                                <p class="card-text text-muted">
                                    Ces dossiers ont été traités et archivés. Ils sont en consultation seule.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('courriers.archives') }}" class="row g-3">
                    <div class="col-md-8">
                        <label for="search" class="form-label">Recherche</label>
                        <input type="text" name="search" id="search" 
                               class="form-control" 
                               placeholder="Référence, contribuable, sujet..."
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="d-flex gap-2 w-100">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Rechercher
                            </button>
                            @if(request('search'))
                            <a href="{{ route('courriers.archives') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-redo me-2"></i>Effacer
                            </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tableau des archives -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    Liste des archives
                    @if(isset($archives) && $archives->total())
                    <span class="badge bg-secondary ms-2">{{ $archives->total() }}</span>
                    @endif
                </h5>
                
                @if(isset($archives) && $archives->total() > 0)
                <div class="d-flex align-items-center">
                    <span class="me-3 text-muted small">
                        Affichage {{ $archives->firstItem() }}-{{ $archives->lastItem() }} sur {{ $archives->total() }}
                    </span>
                </div>
                @endif
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="120">Référence</th>
                                <th width="150">Type</th>
                                <th>Contribuable / Sujet</th>
                                <th width="120">Archivé le</th>
                                <th width="150">Décision le</th>
                                <th width="100">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($archives) && $archives->count() > 0)
                                @foreach($archives as $archive)
                                <tr>
                                    <td>
                                        <span class="fw-bold">{{ $archive->reference }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $typeColors = [
                                                'exoneration' => 'exoneration',
                                                'dispense_tva' => 'dispense_tva',
                                                'rejet' => 'rejet',
                                                'autre' => 'autre'
                                            ];
                                            $typeIcons = [
                                                'exoneration' => 'hand-holding-usd',
                                                'dispense_tva' => 'percent',
                                                'rejet' => 'times-circle',
                                                'autre' => 'file-alt'
                                            ];
                                        @endphp
                                        <span class="badge badge-type badge-{{ $typeColors[$archive->type_dossier] ?? 'autre' }}">
                                            <i class="fas fa-{{ $typeIcons[$archive->type_dossier] ?? 'file-alt' }} me-1"></i>
                                            @if($archive->type_dossier == 'exoneration')
                                                Exonération
                                            @elseif($archive->type_dossier == 'dispense_tva')
                                                Dispense TVA
                                            @elseif($archive->type_dossier == 'rejet')
                                                Rejet
                                            @else
                                                Autre
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $archive->contribuable_nom }}</div>
                                        <small class="text-muted">
                                            @if(strlen($archive->sujet) > 60)
                                                {{ substr($archive->sujet, 0, 60) }}...
                                            @else
                                                {{ $archive->sujet }}
                                            @endif
                                        </small>
                                    </td>
                                    <td>
                                        @if($archive->date_archive)
                                        <span title="{{ \Carbon\Carbon::parse($archive->date_archive)->format('d/m/Y H:i') }}">
                                            {{ \Carbon\Carbon::parse($archive->date_archive)->format('d/m/Y') }}
                                        </span>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($archive->date_decision)
                                        <span>
                                            {{ \Carbon\Carbon::parse($archive->date_decision)->format('d/m/Y') }}
                                        </span>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-buttons d-flex gap-1">
                                            <a href="{{ route('courriers.show', $archive) }}" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Consulter">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="py-5">
                                        <i class="fas fa-archive fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">Aucun dossier archivé</h5>
                                        <p class="text-muted">Les dossiers signés apparaîtront ici une fois archivés.</p>
                                        <a href="{{ route('courriers.index') }}" class="btn btn-primary">
                                            <i class="fas fa-folder-open me-2"></i>Voir les dossiers actifs
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Pagination -->
            @if(isset($archives) && $archives->hasPages())
            <div class="card-footer bg-white">
                <div class="d-flex justify-content-center">
                    {{ $archives->withQueryString()->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>