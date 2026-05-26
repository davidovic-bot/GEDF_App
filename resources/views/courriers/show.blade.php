@extends('layouts.gdf')

@section('title', 'Détail du courrier')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3">Détail du courrier</h1>
            <p class="text-muted">Référence : {{ $courrier->reference ?? $courrier->numero }}</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('courriers.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Informations générales</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr><th>Bénéficiaire</th><td>{{ $courrier->beneficiaire }}</td></tr>
                        <tr><th>NIF</th><td>{{ $courrier->nif ?? 'Non renseigné' }}</td></tr>
                        <tr><th>Objet</th><td>{{ $courrier->objet }}</td></tr>
                        <tr><th>Type de demande</th><td>{{ $courrier->type_demande }}</td></tr>
                        <tr><th>Service concerné</th><td>{{ $courrier->service_emetteur->nom ?? '-' }}</td></tr>
                        <tr><th>Date de réception</th><td>{{ \Carbon\Carbon::parse($courrier->date_reception)->format('d/m/Y') }}</td></tr>
                        <tr><th>Statut</th><td>{{ ucfirst($courrier->statut_general) }}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Document attaché</h5>
                </div>
                <div class="card-body">
                    @if($courrier->documents->count())
                        @foreach($courrier->documents as $doc)
                            <a href="{{ Storage::url($doc->chemin_fichier) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-download"></i> {{ $doc->nom_fichier }}
                            </a>
                        @endforeach
                    @else
                        <p class="text-muted">Aucun document attaché.</p>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <a href="{{ route('courriers.edit', $courrier->id) }}" class="btn btn-warning">Modifier</a>
                        <a href="#" class="btn btn-info">Historique</a>
                        @if(auth()->user()->hasRole('chef_service'))
                            <a href="#" class="btn btn-success">Valider</a>
                        @endif
                    </div>

                    @if(auth()->user()->hasRole('secretaire') && $courrier->statut_general == 'enregistre')
                        <form action="{{ route('courriers.transmettre', $courrier->id) }}" method="POST" class="mt-2">
                            @csrf
                            <button type="submit" class="btn btn-primary">Transmettre à l’agent</button>
                        </form>
                    @endif

                    @if(auth()->user()->hasRole('drs') && $courrier->statut_general == 'enregistre')
    <a href="{{ route('courriers.instruction.drs', $courrier->id) }}" class="btn btn-warning">
        <i class="fas fa-edit"></i> Instruire
    </a>
@endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection