<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dossier {{ $courrier->reference }} - GDF</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .badge-type {
            font-size: 0.9em;
            padding: 6px 12px;
        }
        .badge-exoneration { background-color: #e3f2fd; color: #1565c0; }
        .badge-dispense_tva { background-color: #e8f5e9; color: #2e7d32; }
        .badge-rejet { background-color: #ffebee; color: #c62828; }
        .badge-autre { background-color: #f5f5f5; color: #616161; }
        
        .badge-statut {
            font-size: 0.9em;
            padding: 6px 15px;
        }
        .badge-en_analyse { background-color: #e3f2fd; color: #1565c0; }
        .badge-en_validation { background-color: #fff3e0; color: #ef6c00; }
        .badge-signe { background-color: #e8f5e9; color: #2e7d32; }
        .badge-archive { background-color: #f5f5f5; color: #616161; }
        
        .info-card {
            border-left: 4px solid #0d6efd;
            height: 100%;
        }
        
        .action-card {
            border-left: 4px solid #198754;
            height: 100%;
        }
        
        .description-box {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            white-space: pre-wrap;
            font-family: inherit;
        }
        
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background-color: #dee2e6;
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }
        
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -23px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: #0d6efd;
            border: 2px solid white;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('courriers.index') }}">
                <i class="fas fa-arrow-left me-2"></i>
                Dossier {{ $courrier->reference }}
            </a>
            <div class="navbar-nav ms-auto">
                <a href="{{ route('courriers.edit', $courrier) }}" class="btn btn-light btn-sm me-2">
                    <i class="fas fa-edit me-1"></i> Modifier
                </a>
                <a href="{{ route('courriers.index') }}" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-list me-1"></i> Liste
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <!-- En-tête du dossier -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h2 class="mb-2">{{ $courrier->sujet }}</h2>
                        <div class="d-flex align-items-center gap-3">
                            <span class="badge badge-type 
                                @if($courrier->type_dossier == 'exoneration') badge-exoneration
                                @elseif($courrier->type_dossier == 'dispense_tva') badge-dispense_tva
                                @elseif($courrier->type_dossier == 'rejet') badge-rejet
                                @else badge-autre @endif">
                                <i class="fas 
                                    @if($courrier->type_dossier == 'exoneration') fa-hand-holding-usd
                                    @elseif($courrier->type_dossier == 'dispense_tva') fa-percent
                                    @elseif($courrier->type_dossier == 'rejet') fa-times-circle
                                    @else fa-file-alt @endif me-1"></i>
                                @if($courrier->type_dossier == 'exoneration')
                                    Exonération fiscale
                                @elseif($courrier->type_dossier == 'dispense_tva')
                                    Dispense de TVA
                                @elseif($courrier->type_dossier == 'rejet')
                                    Proposition de rejet
                                @else
                                    Autre dossier
                                @endif
                            </span>
                            
                            <span class="badge badge-statut badge-{{ $courrier->statut }}">
                                @if($courrier->statut == 'en_analyse')
                                    <i class="fas fa-clock me-1"></i> En analyse
                                @elseif($courrier->statut == 'en_validation')
                                    <i class="fas fa-user-check me-1"></i> En validation
                                @elseif($courrier->statut == 'signe')
                                    <i class="fas fa-check-circle me-1"></i> Signé
                                @elseif($courrier->statut == 'archive')
                                    <i class="fas fa-archive me-1"></i> Archivé
                                @endif
                            </span>
                            
                            @php
                                $estEnRetard = $courrier->date_limite && 
                                              \Carbon\Carbon::parse($courrier->date_limite)->isPast() &&
                                              in_array($courrier->statut, ['en_analyse', 'en_validation']);
                            @endphp
                            @if($estEnRetard)
                            <span class="badge bg-danger">
                                <i class="fas fa-exclamation-triangle me-1"></i> En retard
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="text-end">
                        <p class="text-muted mb-1">
                            <small>Référence : <strong>{{ $courrier->reference }}</strong></small>
                        </p>
                        <p class="text-muted mb-0">
                            <small>Créé le : {{ \Carbon\Carbon::parse($courrier->created_at)->format('d/m/Y H:i') }}</small>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Colonne de gauche : Informations -->
            <div class="col-lg-8">
                <!-- Informations du contribuable -->
                <div class="card info-card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-user-tie me-2"></i>
                            Informations du contribuable
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tbody>
                                        <tr>
                                            <th width="40%">Nom / Raison sociale</th>
                                            <td>{{ $courrier->contribuable_nom }}</td>
                                        </tr>
                                        <tr>
                                            <th>Identifiant fiscal</th>
                                            <td>{{ $courrier->contribuable_id_fiscal }}</td>
                                        </tr>
                                        <tr>
                                            <th>Secteur d'activité</th>
                                            <td>
                                                @if($courrier->secteur_activite)
                                                    @if($courrier->secteur_activite == 'industrie') Industrie
                                                    @elseif($courrier->secteur_activite == 'commerce') Commerce
                                                    @elseif($courrier->secteur_activite == 'services') Services
                                                    @elseif($courrier->secteur_activite == 'agriculture') Agriculture
                                                    @elseif($courrier->secteur_activite == 'batiment') Bâtiment & Construction
                                                    @elseif($courrier->secteur_activite == 'transport') Transport & Logistique
                                                    @elseif($courrier->secteur_activite == 'tourisme') Tourisme & Hôtellerie
                                                    @else {{ $courrier->secteur_activite }}
                                                    @endif
                                                @else
                                                    <span class="text-muted">Non spécifié</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tbody>
                                        <tr>
                                            <th width="40%">Montant concerné</th>
                                            <td>
                                                @if($courrier->montant_impact)
                                                    {{ number_format($courrier->montant_impact, 2, ',', ' ') }} €
                                                @else
                                                    <span class="text-muted">Non spécifié</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Date limite</th>
                                            <td class="{{ $estEnRetard ? 'text-danger fw-bold' : '' }}">
                                                @if($courrier->date_limite)
                                                    {{ \Carbon\Carbon::parse($courrier->date_limite)->format('d/m/Y') }}
                                                    @if($estEnRetard)
                                                        <span class="badge bg-danger ms-2">Retard</span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">Non définie</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @if($courrier->date_decision)
                                        <tr>
                                            <th>Date décision</th>
                                            <td>{{ \Carbon\Carbon::parse($courrier->date_decision)->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Description -->
                <div class="card info-card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-align-left me-2"></i>
                            Description de la demande
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="description-box">
                            {{ $courrier->description }}
                        </div>
                    </div>
                </div>
                
                <!-- Pièces jointes -->
                @if($courrier->piecesJointes && $courrier->piecesJointes->count() > 0)
                <div class="card info-card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-paperclip me-2"></i>
                            Pièces jointes ({{ $courrier->piecesJointes->count() }})
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @foreach($courrier->piecesJointes as $piece)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-file-pdf text-danger me-2"></i>
                                    {{ $piece->nom }}
                                    <small class="text-muted ms-2">
                                        ({{ round($piece->taille / 1024) }} KB)
                                    </small>
                                </div>
                                <a href="#" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>
            
            <!-- Colonne de droite : Actions et informations -->
            <div class="col-lg-4">
                <!-- Actions -->
                <div class="card action-card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-cogs me-2"></i>
                            Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('courriers.edit', $courrier) }}" class="btn btn-primary">
                                <i class="fas fa-edit me-2"></i>Modifier le dossier
                            </a>
                            
                            <a href="{{ route('courriers.historique', $courrier) }}" class="btn btn-info">
                                <i class="fas fa-history me-2"></i>Voir l'historique
                            </a>
                            
                            @if($courrier->statut == 'signe')
                            <form action="{{ route('courriers.archive', $courrier) }}" method="POST" class="d-grid">
                                @csrf
                                <button type="submit" class="btn btn-success" 
                                        onclick="return confirm('Êtes-vous sûr de vouloir archiver ce dossier ?')">
                                    <i class="fas fa-archive me-2"></i>Archiver le dossier
                                </button>
                            </form>
                            @endif
                            
                            <form action="{{ route('courriers.destroy', $courrier) }}" method="POST" class="d-grid">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" 
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce dossier ? Cette action est irréversible.')">
                                    <i class="fas fa-trash me-2"></i>Supprimer le dossier
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Créateur -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-user me-2"></i>
                            Créateur
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-user-circle fa-2x text-muted"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">{{ $courrier->createur->name ?? 'Inconnu' }}</h6>
                                <p class="text-muted small mb-0">
                                    <i class="fas fa-calendar me-1"></i>
                                    Créé le {{ \Carbon\Carbon::parse($courrier->created_at)->format('d/m/Y à H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Dernières activités -->
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-clock me-2"></i>
                            Dernière activité
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item">
                                <h6 class="mb-1">Statut modifié</h6>
                                <p class="text-muted small mb-0">
                                    @if($courrier->statut == 'en_analyse')
                                        Dossier en analyse
                                    @elseif($courrier->statut == 'en_validation')
                                        Dossier en validation
                                    @elseif($courrier->statut == 'signe')
                                        Dossier signé
                                    @elseif($courrier->statut == 'archive')
                                        Dossier archivé
                                    @endif
                                </p>
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($courrier->updated_at)->format('d/m/Y H:i') }}
                                </small>
                            </div>
                            
                            <div class="timeline-item">
                                <h6 class="mb-1">Création du dossier</h6>
                                <p class="text-muted small mb-0">
                                    Dossier créé avec la référence {{ $courrier->reference }}
                                </p>
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($courrier->created_at)->format('d/m/Y H:i') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Confirmation pour les actions sensibles
            const deleteForms = document.querySelectorAll('form[action*="destroy"]');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (!confirm('Êtes-vous sûr de vouloir supprimer ce dossier ? Cette action est irréversible.')) {
                        e.preventDefault();
                    }
                });
            });
            
            const archiveForms = document.querySelectorAll('form[action*="archive"]');
            archiveForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (!confirm('Êtes-vous sûr de vouloir archiver ce dossier ?')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
</body>
</html>