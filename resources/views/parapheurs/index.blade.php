@extends('layouts.gdf')

@section('title', 'Gestion des parapheurs')

@section('content')
<div class="container-fluid">
    <!-- Filtres -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Statut</label>
                    <select name="statut" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="en_attente_analyse" {{ request('statut') == 'en_attente_analyse' ? 'selected' : '' }}>⏳ En attente d'analyse</option>
                        <option value="en_attente_chef_service" {{ request('statut') == 'en_attente_chef_service' ? 'selected' : '' }}>👨‍💼 En attente Chef Service</option>
                        <option value="en_attente_directeur" {{ request('statut') == 'en_attente_directeur' ? 'selected' : '' }}>👔 En attente Directeur</option>
                        <option value="valide" {{ request('statut') == 'valide' ? 'selected' : '' }}>✅ Validé</option>
                        <option value="signe" {{ request('statut') == 'signe' ? 'selected' : '' }}>📝 Signé</option>
                        <option value="rejete" {{ request('statut') == 'rejete' ? 'selected' : '' }}>❌ Rejeté</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Priorité</label>
                    <select name="priorite" class="form-select">
                        <option value="">Toutes</option>
                        <option value="normal" {{ request('priorite') == 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="urgent" {{ request('priorite') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Recherche</label>
                    <input type="text" name="search" class="form-control" placeholder="Numéro, objet, référence..." value="{{ request('search') }}">
                </div>
                
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter"></i> Filtrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des parapheurs -->
    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>N° Parapheur</th>
                            <th>Courrier</th>
                            <th>Type</th>
                            <th>Service</th>
                            <th>Statut</th>
                            <th>Priorité</th>
                            <th>Date limite</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($parapheurs as $parapheur)
                        <tr class="{{ $parapheur->estEnRetard() ? 'table-danger' : '' }}">
                            <td><strong>{{ $parapheur->numero_parapheur }}</strong>@if($parapheur->estEnRetard())<span class="badge bg-danger ms-1">RETARD</span>@endif</td>
                            <td><div>{{ Str::limit($parapheur->courrier->objet, 50) }}</div><small class="text-muted">Ref: {{ $parapheur->courrier->reference }}</small></td>
                            <td>{{ $parapheur->courrier->typeCourrier->nom ?? '-' }}</td>
                            <td>{{ $parapheur->courrier->serviceEmetteur->nom ?? '-' }}</td>
                            <td>@include('parapheurs.partials.statut-badge')</td>
                            <td>@if($parapheur->priorite == 'urgent')<span class="badge bg-danger">URGENT</span>@else<span class="badge bg-secondary">Normal</span>@endif</td>
                            <td>@if($parapheur->date_limite_traitement){{ $parapheur->date_limite_traitement->format('d/m/Y') }}@else<span class="text-muted">-</span>@endif</td>
                            <td>
                                <a href="{{ route('parapheurs.show', $parapheur) }}" class="btn btn-sm btn-primary" title="Voir"><i class="fas fa-eye"></i></a>
                                @if($parapheur->peutEtreValidePar(auth()->user()))
                                <a href="{{ route('parapheurs.valider', $parapheur) }}" class="btn btn-sm btn-success" title="Valider" onclick="return confirm('Confirmer la validation ?')"><i class="fas fa-check"></i></a>
                                @endif
                             </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-3"></i>
                                <p>Aucun parapheur trouvé</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($parapheurs->hasPages())
            <div class="d-flex justify-content-center mt-4">{{ $parapheurs->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection