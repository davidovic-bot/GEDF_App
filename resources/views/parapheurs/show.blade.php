@extends('layouts.app')

@section('title', 'Détail Parapheur - GDF')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <div class="row mb-3">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Tableau de bord</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('parapheurs.index') }}">Parapheurs</a></li>
                    <li class="breadcrumb-item active">{{ $parapheur->reference }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- En-tête avec actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 text-gray-800">
                        <i class="fas fa-file-alt text-primary mr-2"></i>Parapheur: {{ $parapheur->reference }}
                    </h1>
                    <p class="text-muted mb-0">
                        <i class="fas fa-calendar-alt mr-1"></i>Créé le {{ $parapheur->created_at->format('d/m/Y H:i') }}
                        @if($parapheur->date_echeance)
                        • Échéance: {{ \Carbon\Carbon::parse($parapheur->date_echeance)->format('d/m/Y') }}
                        @endif
                    </p>
                </div>
                
                <div class="btn-group">
                    <a href="{{ route('parapheurs.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Retour
                    </a>
                    
                    @if($parapheur->createur_id == auth()->id() && in_array($parapheur->statut, ['brouillon', 'en_attente']))
                    <a href="{{ route('parapheurs.edit', $parapheur) }}" class="btn btn-outline-primary ml-2">
                        <i class="fas fa-edit mr-1"></i> Modifier
                    </a>
                    @endif
                    
                    @if(auth()->user()->hasRole('superadmin'))
                    <button type="button" class="btn btn-outline-secondary dropdown-toggle ml-2" 
                            data-toggle="dropdown">
                        <i class="fas fa-cog"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#reassignModal">
                            <i class="fas fa-user-edit mr-2"></i>Réassigner
                        </a>
                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#forceModal">
                            <i class="fas fa-forward mr-2"></i>Forcer étape
                        </a>
                        @if($parapheur->statut != 'archive')
                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#archiveModal">
                            <i class="fas fa-archive mr-2"></i>Archiver
                        </a>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Alertes -->
    @if($parapheur->estEnRetard ?? false)
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>EN RETARD !</strong> La date d'échéance a été dépassée.
            </div>
        </div>
    </div>
    @endif

    <!-- Cartes indicateurs -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Statut
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @php
                                    $statutColors = [
                                        'brouillon' => 'light',
                                        'en_attente' => 'warning',
                                        'en_cours' => 'info',
                                        'valide' => 'success',
                                        'rejete' => 'danger',
                                        'en_retard' => 'danger',
                                        'archive' => 'secondary'
                                    ];
                                @endphp
                                <span class="badge badge-{{ $statutColors[$parapheur->statut] ?? 'secondary' }}">
                                    {{ $parapheur->statutLibelle ?? ucfirst(str_replace('_', ' ', $parapheur->statut)) }}
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tasks fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Priorité
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @php
                                    $prioriteColors = [
                                        'basse' => 'secondary',
                                        'normale' => 'primary',
                                        'haute' => 'warning',
                                        'urgente' => 'danger'
                                    ];
                                @endphp
                                <span class="badge badge-{{ $prioriteColors[$parapheur->priorite] ?? 'secondary' }}">
                                    {{ ucfirst($parapheur->priorite) }}
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-flag fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Progression
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                        {{ $parapheur->etape_actuelle ?? 1 }}/{{ $parapheur->etapes_total ?? 3 }}
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" role="progressbar" 
                                             style="width: {{ (($parapheur->etape_actuelle ?? 1) / ($parapheur->etapes_total ?? 3)) * 100 }}%">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Confidentialité
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @if($parapheur->confidentialite == 'standard')
                                <span class="badge badge-success">Standard</span>
                                @elseif($parapheur->confidentialite == 'confidentiel')
                                <span class="badge badge-warning">Confidentiel</span>
                                @else
                                <span class="badge badge-danger">Très confidentiel</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-lock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="row">
        <!-- Colonne gauche : Informations -->
        <div class="col-lg-8">
            <!-- Détails du parapheur -->
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle mr-2"></i>Informations détaillées
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="40%" class="text-muted">Référence:</th>
                                    <td><strong>{{ $parapheur->reference }}</strong></td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Objet:</th>
                                    <td>{{ $parapheur->objet }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Description:</th>
                                    <td>{!! nl2br(e($parapheur->description)) !!}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Créé par:</th>
                                    <td>
                                        {{ $parapheur->createur->name ?? 'Utilisateur #' . $parapheur->createur_id }}
                                        <br>
                                        <small class="text-muted">{{ $parapheur->created_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="40%" class="text-muted">Service:</th>
                                    <td>{{ $parapheur->service->nom ?? 'Non défini' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Direction:</th>
                                    <td>{{ $parapheur->direction->nom ?? 'Non définie' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Responsable actuel:</th>
                                    <td>
                                        @if($parapheur->responsableActuel)
                                        {{ $parapheur->responsableActuel->name }}
                                        @else
                                        <span class="text-muted">Aucun responsable assigné</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Date échéance:</th>
                                    <td>
                                        @if($parapheur->date_echeance)
                                        {{ \Carbon\Carbon::parse($parapheur->date_echeance)->format('d/m/Y') }}
                                        @if(\Carbon\Carbon::parse($parapheur->date_echeance)->isPast())
                                        <span class="badge badge-danger ml-2">Dépassée</span>
                                        @endif
                                        @else
                                        <span class="text-muted">Non définie</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Workflow / Étapes -->
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-project-diagram mr-2"></i>Circuit de validation
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @php
                            $etapes = [
                                1 => ['label' => 'Enregistrement', 'role' => 'Secrétariat'],
                                2 => ['label' => 'Analyse technique', 'role' => 'Agent du pool'],
                                3 => ['label' => 'Validation hiérarchique', 'role' => 'Chef de service'],
                                4 => ['label' => 'Signature', 'role' => 'Directeur DRS']
                            ];
                        @endphp
                        
                        @for($i = 1; $i <= 4; $i++)
                        <div class="timeline-step {{ $i <= $parapheur->etape_actuelle ? 'completed' : '' }}">
                            <div class="timeline-content">
                                <div class="inner-circle">
                                    @if($i < $parapheur->etape_actuelle)
                                    <i class="fas fa-check"></i>
                                    @elseif($i == $parapheur->etape_actuelle)
                                    <i class="fas fa-play"></i>
                                    @else
                                    <span>{{ $i }}</span>
                                    @endif
                                </div>
                                <p class="h6 mt-3 mb-1">{{ $etapes[$i]['label'] ?? 'Étape ' . $i }}</p>
                                <p class="text-muted mb-0">{{ $etapes[$i]['role'] ?? '' }}</p>
                            </div>
                        </div>
                        @endfor
                    </div>
                    
                    <!-- Actions workflow -->
                    @if($parapheur->responsable_actuel_id == auth()->id() && in_array($parapheur->statut, ['en_attente', 'en_cours']))
                    <div class="mt-4 pt-4 border-top">
                        <h6 class="mb-3">Actions disponibles</h6>
                        <div class="btn-group">
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#validerModal">
                                <i class="fas fa-check mr-1"></i> Valider
                            </button>
                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#rejeterModal">
                                <i class="fas fa-times mr-1"></i> Rejeter
                            </button>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#transmettreModal">
                                <i class="fas fa-forward mr-1"></i> Transmettre
                            </button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Colonne droite : Actions et fichiers -->
        <div class="col-lg-4">
            <!-- Pièces jointes -->
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-paperclip mr-2"></i>Pièces jointes
                    </h6>
                    <button class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#ajouterFichierModal">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                <div class="card-body">
                    @if(isset($parapheur->fichiers) && $parapheur->fichiers->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($parapheur->fichiers as $fichier)
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                            <div>
                                <i class="fas fa-file mr-2 text-muted"></i>
                                <span>{{ $fichier->nom_original }}</span>
                                <br>
                                <small class="text-muted">
                                    {{ $fichier->created_at->format('d/m/Y') }} • 
                                    {{ round($fichier->taille / 1024) }} KB
                                </small>
                            </div>
                            <button class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-3">
                        <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">Aucune pièce jointe</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Historique -->
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history mr-2"></i>Historique
                    </h6>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    @if(isset($parapheur->historiques) && $parapheur->historiques->count() > 0)
                    <div class="timeline-vertical">
                        @foreach($parapheur->historiques->sortByDesc('created_at') as $historique)
                        <div class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">{{ $historique->action }}</h6>
                                <p class="text-muted small mb-1">{{ $historique->details }}</p>
                                <small class="text-muted">
                                    <i class="fas fa-user mr-1"></i>{{ $historique->user->name ?? 'Système' }}
                                    • {{ $historique->created_at->format('d/m/Y H:i') }}
                                </small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-3">
                        <i class="fas fa-clock fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">Aucun historique</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals seront ajoutés ici -->
@include('parapheurs.modals.valider')
@include('parapheurs.modals.rejeter')
@include('parapheurs.modals.transmettre')
@include('parapheurs.modals.ajouter_fichier')
@include('parapheurs.modals.admin_modals')

@endsection

@push('styles')
<style>
    /* Timeline horizontale */
    .timeline {
        display: flex;
        justify-content: space-between;
        position: relative;
        margin: 40px 0;
    }
    .timeline::before {
        content: '';
        position: absolute;
        top: 30px;
        left: 0;
        right: 0;
        height: 2px;
        background: #e3e6f0;
        z-index: 1;
    }
    .timeline-step {
        position: relative;
        z-index: 2;
        text-align: center;
        flex: 1;
    }
    .timeline-step.completed .inner-circle {
        background: #28a745;
        color: white;
    }
    .timeline-step .inner-circle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: #e3e6f0;
        color: #6c757d;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        font-size: 1.2rem;
        border: 3px solid white;
    }
    
    /* Timeline verticale */
    .timeline-vertical {
        position: relative;
        padding-left: 30px;
    }
    .timeline-vertical::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e3e6f0;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }
    .timeline-item:last-child {
        margin-bottom: 0;
    }
    .timeline-marker {
        position: absolute;
        left: -30px;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #4e73df;
        border: 2px solid white;
    }
    .timeline-content {
        padding-bottom: 15px;
        border-bottom: 1px solid #f8f9fc;
    }
    .timeline-item:last-child .timeline-content {
        border-bottom: none;
    }
</style>
@endpush

<div class="modal fade" id="validerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('parapheurs.valider', $parapheur) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-check text-success mr-2"></i>Valider
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Commentaire (optionnel)</label>
                        <textarea class="form-control" name="commentaire" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Valider</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Rejeter -->
<div class="modal fade" id="rejeterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('parapheurs.rejeter', $parapheur) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-times text-danger mr-2"></i>Rejeter
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Motif du rejet *</label>
                        <textarea class="form-control" name="motif" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">Rejeter</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection