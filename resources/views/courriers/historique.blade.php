<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique - {{ $courrier->reference }} - GDF</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
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
            margin-bottom: 25px;
            padding: 15px;
            background-color: white;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
        
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -24px;
            top: 20px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 3px solid white;
        }
        
        .timeline-item.creation::before { background-color: #0d6efd; }
        .timeline-item.modification::before { background-color: #6c757d; }
        .timeline-item.validation::before { background-color: #198754; }
        .timeline-item.archivage::before { background-color: #6f42c1; }
        .timeline-item.suppression::before { background-color: #dc3545; }
        
        .action-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin-right: 15px;
        }
        
        .creation .action-icon { background-color: #e3f2fd; color: #0d6efd; }
        .modification .action-icon { background-color: #f8f9fa; color: #6c757d; }
        .validation .action-icon { background-color: #e8f5e9; color: #198754; }
        .archivage .action-icon { background-color: #e9ecef; color: #6f42c1; }
        .suppression .action-icon { background-color: #ffebee; color: #dc3545; }
        
        .comment-box {
            background-color: #f8f9fa;
            border-left: 3px solid #0d6efd;
            padding: 10px 15px;
            margin-top: 10px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('courriers.show', $courrier) }}">
                <i class="fas fa-arrow-left me-2"></i>
                Historique - {{ $courrier->reference }}
            </a>
        </div>
    </nav>

    <div class="container-fluid">
        <!-- En-tête -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="h4 mb-2">
                            <i class="fas fa-history text-primary me-2"></i>
                            Historique des modifications
                        </h2>
                        <p class="text-muted mb-0">
                            Dossier : <strong>{{ $courrier->reference }}</strong> - {{ $courrier->sujet }}
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('courriers.show', $courrier) }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i>Retour au dossier
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="fas fa-stream me-2"></i>
                    Journal d'activités
                </h5>
            </div>
            <div class="card-body">
                @if(isset($historiques) && $historiques->count() > 0)
                <div class="timeline">
                    @foreach($historiques as $historique)
                    @php
                        $actionClass = '';
                        $actionIcon = '';
                        $actionText = '';
                        
                        switch($historique->action) {
                            case 'creation':
                                $actionClass = 'creation';
                                $actionIcon = 'plus-circle';
                                $actionText = 'Création du dossier';
                                break;
                            case 'modification':
                                $actionClass = 'modification';
                                $actionIcon = 'edit';
                                $actionText = 'Modification';
                                break;
                            case 'validation':
                                $actionClass = 'validation';
                                $actionIcon = 'check-circle';
                                $actionText = 'Validation';
                                break;
                            case 'archivage':
                                $actionClass = 'archivage';
                                $actionIcon = 'archive';
                                $actionText = 'Archivage';
                                break;
                            case 'suppression':
                                $actionClass = 'suppression';
                                $actionIcon = 'trash';
                                $actionText = 'Suppression';
                                break;
                            default:
                                $actionClass = 'modification';
                                $actionIcon = 'info-circle';
                                $actionText = $historique->action;
                        }
                    @endphp
                    
                    <div class="timeline-item {{ $actionClass }}">
                        <div class="d-flex">
                            <div class="action-icon flex-shrink-0">
                                <i class="fas fa-{{ $actionIcon }}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ $actionText }}</h6>
                                        <p class="mb-1">{{ $historique->details }}</p>
                                        @if($historique->commentaire)
                                        <div class="comment-box">
                                            <i class="fas fa-comment me-1"></i>
                                            {{ $historique->commentaire }}
                                        </div>
                                        @endif
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($historique->created_at)->format('d/m/Y H:i') }}
                                        </small>
                                        <div class="mt-1">
                                            <small class="text-muted">
                                                <i class="fas fa-user me-1"></i>
                                                {{ $historique->user->name ?? 'Système' }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucun historique disponible</h5>
                    <p class="text-muted">Les actions sur ce dossier seront enregistrées ici.</p>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Résumé -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="fas fa-info-circle me-2"></i>
                            Informations sur l'historique
                        </h6>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="fas fa-circle text-primary me-2"></i>
                                <small>Création : Première création du dossier</small>
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-circle text-secondary me-2"></i>
                                <small>Modification : Changement d'informations</small>
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-circle text-success me-2"></i>
                                <small>Validation : Approbation du dossier</small>
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-circle text-info me-2"></i>
                                <small>Archivage : Dossier mis en archives</small>
                            </li>
                            <li>
                                <i class="fas fa-circle text-danger me-2"></i>
                                <small>Suppression : Dossier supprimé</small>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="fas fa-chart-bar me-2"></i>
                            Statistiques
                        </h6>
                        @if(isset($historiques) && $historiques->count() > 0)
                        <div class="row">
                            <div class="col-6">
                                <div class="text-center">
                                    <h4 class="text-primary">{{ $historiques->where('action', 'creation')->count() }}</h4>
                                    <small>Créations</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <h4 class="text-secondary">{{ $historiques->where('action', 'modification')->count() }}</h4>
                                    <small>Modifications</small>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                Dernière activité : 
                                {{ \Carbon\Carbon::parse($historiques->first()->created_at)->format('d/m/Y H:i') }}
                            </small>
                        </div>
                        @else
                        <p class="text-muted mb-0">Aucune statistique disponible</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>