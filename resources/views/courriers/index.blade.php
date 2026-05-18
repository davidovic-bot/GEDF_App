@extends('layouts.gdf')

@section('title', 'Gestion des courriers')

@section('content')
<!-- Statistiques -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-primary text-uppercase mb-1">Enregistrés</div>
                        <div class="h5 mb-0 fw-bold text-gray-800">{{ $stats['enregistres'] ?? 0 }}</div>
                        <div class="text-xs text-muted">En attente de traitement</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-inbox fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-warning text-uppercase mb-1">En Analyse</div>
                        <div class="h5 mb-0 fw-bold text-gray-800">{{ $stats['en_analyse'] ?? 0 }}</div>
                        <div class="text-xs text-muted">Par les agents</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-search fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-info text-uppercase mb-1">En Validation</div>
                        <div class="h5 mb-0 fw-bold text-gray-800">{{ $stats['en_validation'] ?? 0 }}</div>
                        <div class="text-xs text-muted">Chef Service / Directeur</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start-success shadow h-100 py-2">
            <div class-card-body>
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-success text-uppercase mb-1">Signés</div>
                        <div class="h5 mb-0 fw-bold text-gray-800">{{ $stats['signes'] ?? 0 }}</div>
                        <div class="text-xs text-muted">Traitement terminé</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-file-signature fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtres -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 fw-bold text-primary"><i class="fas fa-filter me-1"></i> Filtres de recherche</h6>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label fw-bold">Type de demande</label>
                <select name="type_demande" class="form-select">
                    <option value="">Tous les types</option>
                    <option value="exoneration" {{ request('type_demande') == 'exoneration' ? 'selected' : '' }}>Exonération</option>
                    <option value="dispense" {{ request('type_demande') == 'dispense' ? 'selected' : '' }}>Dispense</option>
                    <option value="exoneration_ouverte" {{ request('type_demande') == 'exoneration_ouverte' ? 'selected' : '' }}>Exonération Ouverte</option>
                    <option value="exoneration_fermee" {{ request('type_demande') == 'exoneration_fermee' ? 'selected' : '' }}>Exonération Fermée</option>
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label fw-bold">Statut</label>
                <select name="statut" class="form-select">
                    <option value="">Tous les statuts</option>
                    <option value="enregistre" {{ request('statut') == 'enregistre' ? 'selected' : '' }}>Enregistré</option>
                    <option value="en_analyse" {{ request('statut') == 'en_analyse' ? 'selected' : '' }}>En analyse</option>
                    <option value="en_validation_chef" {{ request('statut') == 'en_validation_chef' ? 'selected' : '' }}>En validation chef</option>
                    <option value="en_validation_directeur" {{ request('statut') == 'en_validation_directeur' ? 'selected' : '' }}>En validation directeur</option>
                    <option value="en_signature_dg" {{ request('statut') == 'en_signature_dg' ? 'selected' : '' }}>En signature DG</option>
                    <option value="signe" {{ request('statut') == 'signe' ? 'selected' : '' }}>Signé</option>
                    <option value="archive" {{ request('statut') == 'archive' ? 'selected' : '' }}>Archivé</option>
                    <option value="rejete" {{ request('statut') == 'rejete' ? 'selected' : '' }}>Rejeté</option>
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label fw-bold">Période</label>
                <input type="date" name="date_debut" class="form-control" value="{{ request('date_debut') }}">
            </div>
            
            <div class="col-md-3">
                <label class="form-label fw-bold">&nbsp;</label>
                <input type="date" name="date_fin" class="form-control" value="{{ request('date_fin') }}">
            </div>
            
            <div class="col-md-6">
                <label class="form-label fw-bold">Recherche</label>
                <input type="text" name="search" class="form-control" placeholder="Référence, expéditeur, objet, NIF..." value="{{ request('search') }}">
            </div>
            
            <div class="col-md-6 d-flex align-items-end">
                <div class="btn-group w-100">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i> Rechercher</button>
                    <a href="{{ route('courriers.index') }}" class="btn btn-secondary"><i class="fas fa-times me-1"></i> Réinitialiser</a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tableau des courriers -->
<div class="card shadow">
    <div class="card-header py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-primary"><i class="fas fa-list me-1"></i> Liste des Courriers</h6>
            @if(auth()->user()->hasRole('secretaire'))
                <a href="{{ route('courriers.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus-circle me-1"></i> Enregistrer un courrier
                </a>
            @endif
        </div>
    </div>
    <div class="card-body">
        @if(count($courriers) > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="100">Référence</th>
                            <th>Expéditeur / Contribuable</th>
                            <th>Objet</th>
                            <th width="120">Type</th>
                            <th width="120">Statut</th>
                            <th width="100">Date Réception</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($courriers as $courrier)
                        <tr>
                            <td>
                                <strong class="text-primary">{{ $courrier->reference }}</strong>
                                @if($courrier->priorite == 'urgent')
                                    <br><span class="badge bg-danger small">URGENT</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold">{{ $courrier->expediteur }}</div>
                                @if($courrier->nif)<small class="text-muted">NIF: {{ $courrier->nif }}</small>@endif
                            </td>
                            <td>{{ Str::limit($courrier->objet, 80) }}</td>
                            <td>
                                @if($courrier->type_demande == 'exoneration')
                                    @if($courrier->type_exoneration == 'ouverte')
                                        <span class="badge bg-info">Exonération Ouverte</span>
                                    @elseif($courrier->type_exoneration == 'fermee')
                                        <span class="badge bg-info">Exonération Fermée</span>
                                    @else
                                        <span class="badge bg-info">Exonération</span>
                                    @endif
                                @else
                                    <span class="badge bg-warning">Dispense</span>
                                @endif
                            </td>
                            <td><span class="badge bg-{{ $courrier->statut_general == 'enregistre' ? 'secondary' : ($courrier->statut_general == 'en_analyse' ? 'warning' : ($courrier->statut_general == 'traite' ? 'success' : 'info')) }}">
    {{ ucfirst($courrier->statut_general) }}
</span></td>
                            <td>{{ \Carbon\Carbon::parse($courrier->date_reception)->format('d/m/Y') }}</td>
                                                        <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('courriers.show', $courrier->id) }}" class="btn btn-info" title="Voir détails"><i class="fas fa-eye"></i></a>

                                    @if(auth()->user()->hasRole('agent') && $courrier->statut_general == 'enregistre')
                                        <a href="{{ route('courriers.edit', $courrier->id) }}" class="btn btn-warning" title="Analyser"><i class="fas fa-edit"></i></a>
                                    @endif

                                    @if($courrier->peutEtreValidePar(auth()->user()))
                                        <a href="{{ route('courriers.valider', $courrier->id) }}" class="btn btn-success" title="Valider"><i class="fas fa-check"></i></a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted small">Affichage de {{ $courriers->firstItem() }} à {{ $courriers->lastItem() }} sur {{ $courriers->total() }} courriers</div>
                <div>{{ $courriers->links() }}</div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">Aucun courrier trouvé</h4>
                <p class="text-muted">Commencez par enregistrer un nouveau courrier</p>
                @if(auth()->user()->hasRole('secretaire'))
                <a href="{{ route('courriers.create') }}" class="btn btn-primary mt-2">
                    <i class="fas fa-plus-circle me-1"></i> Enregistrer un courrier
                </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection