@extends('layouts.gdf')

@section('title', 'Analyse du courrier')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3">Analyse du dossier</h1>
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
                    <h5 class="card-title">Informations du courrier</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr><th>Bénéficiaire</th><td>{{ $courrier->beneficiaire }}</tr>
                        <tr><th>NIF</th><td>{{ $courrier->nif ?? '-' }}</tr>
                        <tr><th>Objet</th><td>{{ $courrier->objet }}</tr>
                        <tr><th>Type</th><td>{{ $courrier->type_demande }}</tr>
                        <tr><th>Service</th><td>{{ $courrier->service_emetteur->nom ?? '-' }}</tr>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Décision de l’agent</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('courriers.transmettre.chef', $courrier->id) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Type de projet *</label>
                            <select name="type_projet" class="form-select" required>
                                <option value="">Sélectionner</option>
                                <option value="attestation">Projet d’attestation</option>
                                <option value="rejet">Projet de rejet</option>
                                <option value="complement">Demande de complément</option>
                                <option value="visite">Demande de visite sur site</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Motif / commentaire</label>
                            <textarea name="motif" class="form-control" rows="3"></textarea>
                        </div>

                        @if($courrier->type_demande == 'exoneration_tva')
    <div class="mb-3">
        <label class="form-label">Type d’exonération</label>
        <select name="type_exoneration" class="form-select">
            <option value="">Sélectionner</option>
            <option value="fermee">Exonération fermée</option>
            <option value="ouverte">Exonération ouverte</option>
        </select>
    </div>
@endif
                        <button type="submit" class="btn btn-primary">Transmettre au chef de service</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection