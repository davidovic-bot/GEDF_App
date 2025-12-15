@extends('layouts.app')

@section('title', 'Supervision des Parapheurs')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">
            <i class="fas fa-tachometer-alt text-primary"></i> Supervision Parapheurs
            @if(auth()->user()->hasRole('superadmin'))
            <span class="badge bg-danger ms-2">SUPERADMIN</span>
            @endif
        </h1>
        <div>
            @if(auth()->user()->hasRole('superadmin'))
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-danger me-2">
                <i class="fas fa-cog"></i> Administration
            </a>
            @endif
            <a href="{{ route('parapheurs.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouveau Parapheur
            </a>
        </div>
    </div>

    <!-- STATISTIQUES RAPIDES SUPERADMIN -->
    @if(auth()->user()->hasRole('superadmin'))
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ App\Models\Parapheur::count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-contract fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                En attente</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ App\Models\Parapheur::where('statut', 'en_attente')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                En cours</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ App\Models\Parapheur::where('statut', 'en_cours')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-spinner fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                En retard</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ App\Models\Parapheur::where('statut', 'en_retard')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Validés</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ App\Models\Parapheur::where('statut', 'valide')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Archivés</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ App\Models\Parapheur::where('statut', 'archive')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-archive fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- FILTRES AVANCÉS (Superadmin seulement) -->
    @if(auth()->user()->hasRole('superadmin') && isset($services) && isset($directions) && isset($utilisateurs))
    <div class="card shadow mb-4">
        <div class="card-header bg-dark text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-filter"></i> Filtres avancés de supervision
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <!-- Filtre Statut -->
                <div class="col-md-2">
                    <label class="form-label">Statut</label>
                    <select name="statut" class="form-select">
                        <option value="">Tous statuts</option>
                        @foreach(['en_attente', 'en_cours', 'valide', 'rejete', 'en_retard', 'archive'] as $statut)
                        <option value="{{ $statut }}" {{ request('statut') == $statut ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $statut)) }}
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Filtre Direction -->
                <div class="col-md-2">
                    <label class="form-label">Direction</label>
                    <select name="direction_id" class="form-select">
                        <option value="">Toutes</option>
                        @foreach($directions as $direction)
                        <option value="{{ $direction->id }}" {{ request('direction_id') == $direction->id ? 'selected' : '' }}>
                            {{ $direction->sigle }}
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Filtre Service -->
                <div class="col-md-2">
                    <label class="form-label">Service</label>
                    <select name="service_id" class="form-select">
                        <option value="">Tous</option>
                        @foreach($services as $service)
                        <option value="{{ $service->id }}" {{ request('service_id') == $service->id ? 'selected' : '' }}>
                            {{ $service->code }}
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Filtre Créateur -->
                <div class="col-md-2">
                    <label class="form-label">Créateur</label>
                    <select name="createur_id" class="form-select">
                        <option value="">Tous</option>
                        @foreach($utilisateurs as $user)
                        <option value="{{ $user->id }}" {{ request('createur_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Filtre Responsable -->
                <div class="col-md-2">
                    <label class="form-label">Responsable</label>
                    <select name="responsable_actuel_id" class="form-select">
                        <option value="">Tous</option>
                        @foreach($utilisateurs as $user)
                        <option value="{{ $user->id }}" {{ request('responsable_actuel_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Filtre Priorité -->
                <div class="col-md-2">
                    <label class="form-label">Priorité</label>
                    <select name="priorite" class="form-select">
                        <option value="">Toutes</option>
                        @foreach(['basse', 'normale', 'haute', 'urgente'] as $priorite)
                        <option value="{{ $priorite }}" {{ request('priorite') == $priorite ? 'selected' : '' }}>
                            {{ ucfirst($priorite) }}
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Dates -->
                <div class="col-md-3">
                    <label class="form-label">Date début</label>
                    <input type="date" name="date_debut" class="form-control" 
                           value="{{ request('date_debut') }}">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Date fin</label>
                    <input type="date" name="date_fin" class="form-control" 
                           value="{{ request('date_fin') }}">
                </div>
                
                <!-- Boutons -->
                <div class="col-md-6 d-flex align-items-end">
                    <button type="submit" class="btn btn-dark me-2">
                        <i class="fas fa-search"></i> Filtrer
                    </button>
                    <a href="{{ route('parapheurs.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Réinitialiser
                    </a>
                    @if(auth()->user()->hasRole('superadmin'))
                    <div class="ms-auto">
                        <button type="button" class="btn btn-outline-primary dropdown-toggle" 
                                data-bs-toggle="dropdown">
                            <i class="fas fa-download"></i> Exporter
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-file-excel"></i> Excel</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-file-pdf"></i> PDF</a></li>
                        </ul>
                    </div>
                    @endif
                </div>
            </form>
        </div>
    </div>
    @else
    <!-- Filtres basiques pour non-superadmin -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Statut</label>
                    <select name="statut" class="form-select">
                        <option value="">Tous</option>
                        <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                        <option value="en_cours" {{ request('statut') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                        <option value="valide" {{ request('statut') == 'valide' ? 'selected' : '' }}>Validé</option>
                        <option value="rejete" {{ request('statut') == 'rejete' ? 'selected' : '' }}>Rejeté</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter"></i> Filtrer
                    </button>
                    <a href="{{ route('parapheurs.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- TABLEAU DES PARAPHEURS -->
    <div class="card shadow">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-list"></i> Liste des parapheurs
                    <span class="badge bg-primary ms-2">{{ $parapheurs->total() }}</span>
                </h6>
                <div>
                    <span class="text-muted me-3">
                        Page {{ $parapheurs->currentPage() }} / {{ $parapheurs->lastPage() }}
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 100px;">Réf.</th>
                            <th>Objet</th>
                            @if(auth()->user()->hasRole('superadmin'))
                            <th style="width: 80px;">Direction</th>
                            <th style="width: 80px;">Service</th>
                            <th style="width: 120px;">Créateur</th>
                            <th style="width: 120px;">Responsable</th>
                            @endif
                            <th style="width: 100px;">Statut</th>
                            <th style="width: 80px;">Priorité</th>
                            <th style="width: 100px;">Échéance</th>
                            <th style="width: 120px;">Créé le</th>
                            <th style="width: 100px;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($parapheurs as $parapheur)
                        <tr class="{{ $parapheur->statut == 'en_retard' ? 'table-danger' : '' }}">
                            <!-- Référence -->
                            <td>
                                <strong class="text-primary">{{ $parapheur->reference }}</strong>
                                @if($parapheur->confidentialite == 'tres_confidentiel')
                                <br><span class="badge bg-dark"><i class="fas fa-lock"></i> Secret</span>
                                @endif
                            </td>
                            
                            <!-- Objet -->
                            <td>
                                <div class="fw-bold" title="{{ $parapheur->objet }}">
                                    {{ Str::limit($parapheur->objet, 50) }}
                                </div>
                                <small class="text-muted" title="{{ $parapheur->description }}">
                                    {{ Str::limit($parapheur->description, 40) }}
                                </small>
                            </td>
                            
                            <!-- Colonnes Superadmin -->
                            @if(auth()->user()->hasRole('superadmin'))
                            <td>
                                <span class="badge bg-info">
                                    {{ $parapheur->direction->sigle ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <small>{{ $parapheur->service->code ?? 'N/A' }}</small>
                            </td>
                            <td>
                                <div class="small">
                                    {{ Str::limit($parapheur->createur->name ?? 'N/A', 15) }}
                                </div>
                                <span class="badge bg-secondary">
                                    {{ $parapheur->createur->roles->first()->name ?? '' }}
                                </span>
                            </td>
                            <td>
                                @if($parapheur->responsableActuel)
                                <div class="small">
                                    {{ Str::limit($parapheur->responsableActuel->name, 15) }}
                                </div>
                                <span class="badge bg-warning">
                                    {{ $parapheur->responsableActuel->roles->first()->name ?? '' }}
                                </span>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            @endif
                            
                            <!-- Statut -->
                            <td>
                                @if($parapheur->statut == 'en_attente')
                                <span class="badge bg-warning"><i class="fas fa-clock"></i> En attente</span>
                                @elseif($parapheur->statut == 'en_cours')
                                <span class="badge bg-info"><i class="fas fa-spinner"></i> En cours</span>
                                @elseif($parapheur->statut == 'valide')
                                <span class="badge bg-success"><i class="fas fa-check"></i> Validé</span>
                                @elseif($parapheur->statut == 'rejete')
                                <span class="badge bg-danger"><i class="fas fa-times"></i> Rejeté</span>
                                @elseif($parapheur->statut == 'en_retard')
                                <span class="badge bg-dark"><i class="fas fa-exclamation"></i> En retard</span>
                                @elseif($parapheur->statut == 'archive')
                                <span class="badge bg-secondary"><i class="fas fa-archive"></i> Archivé</span>
                                @else
                                <span class="badge bg-light text-dark">{{ $parapheur->statut }}</span>
                                @endif
                            </td>
                            
                            <!-- Priorité -->
                            <td>
                                @if($parapheur->priorite == 'urgente')
                                <span class="badge bg-danger"><i class="fas fa-exclamation"></i> Urgente</span>
                                @elseif($parapheur->priorite == 'haute')
                                <span class="badge bg-warning"><i class="fas fa-arrow-up"></i> Haute</span>
                                @elseif($parapheur->priorite == 'normale')
                                <span class="badge bg-primary"><i class="fas fa-equals"></i> Normale</span>
                                @else
                                <span class="badge bg-success"><i class="fas fa-arrow-down"></i> Basse</span>
                                @endif
                            </td>
                            
                            <!-- Échéance -->
                            <td>
                                <div>{{ $parapheur->date_echeance->format('d/m/Y') }}</div>
                                @if($parapheur->statut == 'en_retard')
                                <small class="text-danger">
                                    <i class="fas fa-clock"></i> Retard
                                </small>
                                @endif
                            </td>
                            
                            <!-- Date création -->
                            <td>
                                <div class="small">
                                    {{ $parapheur->created_at->format('d/m/Y') }}
                                </div>
                                <div class="text-muted">
                                    {{ $parapheur->created_at->format('H:i') }}
                                </div>
                            </td>
                            
                            <!-- Actions -->
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <!-- Voir -->
                                    <a href="{{ route('parapheurs.show', $parapheur) }}" 
                                       class="btn btn-outline-primary" title="Voir détail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <!-- Actions Superadmin -->
                                    @if(auth()->user()->hasRole('superadmin'))
                                    <button type="button" class="btn btn-outline-dark dropdown-toggle dropdown-toggle-split" 
                                            data-bs-toggle="dropdown" title="Actions admin">
                                        <span class="visually-hidden">Actions</span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item" href="#" data-bs-toggle="modal" 
                                               data-bs-target="#reassignModal{{ $parapheur->id }}">
                                                <i class="fas fa-user-edit text-warning"></i> Réassigner
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="#" data-bs-toggle="modal" 
                                               data-bs-target="#forceModal{{ $parapheur->id }}">
                                                <i class="fas fa-forward text-info"></i> Forcer l'étape
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item text-success" href="#" 
                                               onclick="return confirm('Marquer comme validé ?')">
                                                <i class="fas fa-check"></i> Valider
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item text-danger" href="#" 
                                               onclick="return confirm('Archiver ce parapheur ?')">
                                                <i class="fas fa-archive"></i> Archiver
                                            </a>
                                        </li>
                                    </ul>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ auth()->user()->hasRole('superadmin') ? 11 : 6 }}" 
                                class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted h5">Aucun parapheur trouvé</p>
                                <p class="text-muted">Aucun parapheur ne correspond à vos critères</p>
                                <a href="{{ route('parapheurs.create') }}" class="btn btn-primary mt-2">
                                    <i class="fas fa-plus"></i> Créer le premier parapheur
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination -->
        @if($parapheurs->hasPages())
        <div class="card-footer bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    Affichage de {{ $parapheurs->firstItem() }} à {{ $parapheurs->lastItem() }} 
                    sur {{ $parapheurs->total() }} parapheurs
                </div>
                <div>
                    {{ $parapheurs->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- MODALS POUR ACTIONS SUPERADMIN -->
@if(auth()->user()->hasRole('superadmin') && isset($utilisateurs))
@foreach($parapheurs as $parapheur)
<!-- Modal réassignation -->
<div class="modal fade" id="reassignModal{{ $parapheur->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.parapheurs.reassign', $parapheur) }}" method="POST">
                @csrf
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-user-edit"></i> Réassigner le parapheur
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Parapheur :</strong> {{ $parapheur->reference }}</p>
                    <p><strong>Objet :</strong> {{ $parapheur->objet }}</p>
                    
                    <div class="mb-3">
                        <label class="form-label">Nouveau responsable *</label>
                        <select name="nouveau_responsable_id" class="form-select" required>
                            <option value="">Sélectionner un utilisateur</option>
                            @foreach($utilisateurs as $user)
                            <option value="{{ $user->id }}">
                                {{ $user->name }} ({{ $user->roles->first()->name ?? 'N/A' }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Commentaire administratif *</label>
                        <textarea name="commentaire" class="form-control" rows="3" 
                                  placeholder="Raison de la réassignation..." required></textarea>
                        <small class="form-text text-muted">Ce commentaire sera enregistré dans l'historique</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning">Réassigner</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal forcer étape -->
<div class="modal fade" id="forceModal{{ $parapheur->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.parapheurs.force', $parapheur) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-forward"></i> Forcer le passage d'étape
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> 
                        <strong>Action administrative exceptionnelle</strong>
                        <p class="mb-0 small">Cette action contourne le workflow normal</p>
                    </div>
                    
                    <p><strong>Parapheur :</strong> {{ $parapheur->reference }}</p>
                    <p><strong>Étape actuelle :</strong> {{ $parapheur->etape_actuelle }}/{{ $parapheur->etapes_total }}</p>
                    
                    <div class="mb-3">
                        <label class="form-label">Passer directement à l'étape *</label>
                        <select name="nouvelle_etape" class="form-select" required>
                            <option value="">Sélectionner une étape</option>
                            @for($i = $parapheur->etape_actuelle + 1; $i <= $parapheur->etapes_total; $i++)
                            <option value="{{ $i }}">Étape {{ $i }} - Validation</option>
                            @endfor
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Justification administrative *</label>
                        <textarea name="justification" class="form-control" rows="3" 
                                  placeholder="Raison du contournement du workflow..." required></textarea>
                        <small class="form-text text-muted">Cette justification sera enregistrée dans l'historique</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">Forcer le passage</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endif

@endsection

@push('styles')
<style>
.table th {
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    border-top: none;
}
.table td {
    vertical-align: middle;
}
.badge {
    font-size: 0.75em;
    font-weight: 500;
}
.card-header {
    border-bottom: 1px solid #e3e6f0;
}
</style>
@endpush