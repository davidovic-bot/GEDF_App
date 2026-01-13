{{-- resources/views/courriers/index.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dossiers Fiscaux - GDF</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .stat-card {
            border-left: 4px solid;
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-2px);
        }
        .stat-en-cours { border-left-color: #0d6efd; }
        .stat-validation { border-left-color: #ffc107; }
        .stat-retard { border-left-color: #dc3545; }
        .stat-signe { border-left-color: #198754; }
        
        .badge-type {
            font-size: 0.75em;
            padding: 4px 8px;
        }
        .badge-exoneration { background-color: #e3f2fd; color: #1565c0; }
        .badge-dispense_tva { background-color: #e8f5e9; color: #2e7d32; }
        .badge-rejet { background-color: #ffebee; color: #c62828; }
        
        .badge-statut {
            font-size: 0.8em;
            padding: 4px 10px;
        }
        .badge-en_analyse { background-color: #e3f2fd; color: #1565c0; }
        .badge-en_validation { background-color: #fff3e0; color: #ef6c00; }
        .badge-signe { background-color: #e8f5e9; color: #2e7d32; }
        .badge-archive { background-color: #f5f5f5; color: #616161; }
        
        .retard-indicator {
            animation: pulse 1.5s infinite;
            color: #dc3545;
        }
        
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
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
            <a class="navbar-brand" href="#">
                <i class="fas fa-landmark me-2"></i>
                GDF - Gestion des Dossiers Fiscaux
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('courriers.index') }}">
                            <i class="fas fa-folder-open me-1"></i> Dossiers
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('courriers.create') }}">
                            <i class="fas fa-plus-circle me-1"></i> Nouveau
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('courriers.archives') }}">
                            <i class="fas fa-archive me-1"></i> Archives
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-chart-bar me-1"></i> Statistiques
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-user me-1"></i> {{ Auth::user()->name ?? 'Utilisateur' }}
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
                    <i class="fas fa-folder-open text-primary me-2"></i>
                    Dossiers Fiscaux
                </h1>
                <p class="text-muted mb-0">
                    Gestion des demandes d'exonération, dispense TVA et propositions de rejet
                </p>
            </div>
            <div>
                <a href="{{ route('courriers.create') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus-circle me-2"></i>
                    Nouveau dossier
                </a>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stat-card stat-en-cours">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-muted mb-1">En cours</h6>
                                <h4 class="mb-0">{{ $stats['en_cours'] ?? 0 }}</h4>
                            </div>
                            <div class="icon">
                                <i class="fas fa-clock fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card stat-card stat-validation">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-muted mb-1">En validation</h6>
                                <h4 class="mb-0">{{ $stats['en_validation'] ?? 0 }}</h4>
                            </div>
                            <div class="icon">
                                <i class="fas fa-user-check fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card stat-card stat-retard">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-muted mb-1">En retard</h6>
                                <h4 class="mb-0">{{ $stats['en_retard'] ?? 0 }}</h4>
                            </div>
                            <div class="icon">
                                <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card stat-card stat-signe">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-muted mb-1">Signés</h6>
                                <h4 class="mb-0">{{ $stats['signes'] ?? 0 }}</h4>
                            </div>
                            <div class="icon">
                                <i class="fas fa-check-circle fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('courriers.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label for="type" class="form-label">Type de dossier</label>
                        <select name="type" id="type" class="form-select">
                            <option value="">Tous les types</option>
                            @foreach($types ?? [] as $key => $label)
                            <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="statut" class="form-label">Statut</label>
                        <select name="statut" id="statut" class="form-select">
                            <option value="">Tous les statuts</option>
                            @foreach($statuts ?? [] as $key => $label)
                            <option value="{{ $key }}" {{ request('statut') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="date_debut" class="form-label">Date début</label>
                        <input type="date" name="date_debut" id="date_debut" 
                               class="form-control" value="{{ request('date_debut') }}">
                    </div>

                    <div class="col-md-2">
                        <label for="date_fin" class="form-label">Date fin</label>
                        <input type="date" name="date_fin" id="date_fin" 
                               class="form-control" value="{{ request('date_fin') }}">
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-2"></i>Filtrer
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tableau des dossiers -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    Liste des dossiers
                    <span class="badge bg-primary ms-2">{{ $courriers->total() ?? 0 }}</span>
                </h5>
                
                <div class="d-flex align-items-center">
                    @if(isset($courriers) && $courriers->total() > 0)
                    <span class="me-3 text-muted small">
                        Affichage {{ $courriers->firstItem() }}-{{ $courriers->lastItem() }} sur {{ $courriers->total() }}
                    </span>
                    @endif
                </div>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="120">Référence</th>
                                <th width="150">Type</th>
                                <th>Contribuable / Sujet</th>
                                <th width="120">Statut</th>
                                <th width="120">Date limite</th>
                                <th width="150">Créé le</th>
                                <th width="100">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($courriers) && $courriers->count() > 0)
                                @foreach($courriers as $courrier)
                                <tr class="{{ $courrier->est_en_retard ? 'table-warning' : '' }}">
                                    <td>
                                        <span class="fw-bold">{{ $courrier->reference }}</span>
                                        @if($courrier->est_en_retard)
                                        <span class="retard-indicator ms-1" title="En retard">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $typeColors = [
                                                'exoneration' => 'exoneration',
                                                'dispense_tva' => 'dispense_tva',
                                                'rejet' => 'rejet'
                                            ];
                                        @endphp
                                        <span class="badge badge-type badge-{{ $typeColors[$courrier->type_dossier] ?? 'secondary' }}">
                                            @if($courrier->type_dossier == 'exoneration')
                                                <i class="fas fa-hand-holding-usd me-1"></i>
                                            @elseif($courrier->type_dossier == 'dispense_tva')
                                                <i class="fas fa-percent me-1"></i>
                                            @else
                                                <i class="fas fa-file-alt me-1"></i>
                                            @endif
                                            {{ $courrier->libelle_type ?? ucfirst($courrier->type_dossier) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $courrier->contribuable_nom }}</div>
                                        <small class="text-muted">{{ Str::limit($courrier->sujet, 60) }}</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-statut badge-{{ $courrier->statut }}">
                                            {{ $courrier->libelle_statut ?? ucfirst($courrier->statut) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($courrier->date_limite)
                                        <span class="{{ $courrier->est_en_retard ? 'text-danger fw-bold' : '' }}">
                                            {{ \Carbon\Carbon::parse($courrier->date_limite)->format('d/m/Y') }}
                                        </span>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span title="{{ \Carbon\Carbon::parse($courrier->created_at)->format('d/m/Y H:i') }}">
                                            {{ \Carbon\Carbon::parse($courrier->created_at)->format('d/m/Y') }}
                                        </span>
                                        <div class="small text-muted">
                                            par {{ $courrier->createur->name ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('courriers.show', $courrier) }}" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Voir le dossier">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('courriers.edit', $courrier) }}" 
                                               class="btn btn-sm btn-outline-secondary" 
                                               title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="py-5">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">Aucun dossier trouvé</h5>
                                        <p class="text-muted">Commencez par créer un nouveau dossier.</p>
                                        <a href="{{ route('courriers.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus-circle me-2"></i>Créer un dossier
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
            @if(isset($courriers) && $courriers->hasPages())
            <div class="card-footer bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Page {{ $courriers->currentPage() }} sur {{ $courriers->lastPage() }}
                    </div>
                    <div>
                        {{ $courriers->withQueryString()->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-submit des filtres quand on change le type ou statut
            document.getElementById('type')?.addEventListener('change', function() {
                this.form.submit();
            });
            
            document.getElementById('statut')?.addEventListener('change', function() {
                this.form.submit();
            });
        });
    </script>
</body>
</html>