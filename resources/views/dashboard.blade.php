@extends('layouts.gdf')

@section('title', 'Tableau de bord')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h3">Tableau de bord</h1>
            <p class="text-muted">Bienvenue sur votre tableau de bord, {{ auth()->user()->name }}.</p>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Dossiers en cours</h5>
                    <p class="card-text display-4">{{ $stats['dossiers_en_cours'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">En attente de validation</h5>
                    <p class="card-text display-4">{{ $stats['en_attente_validation'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Validés ce mois</h5>
                    <p class="card-text display-4">{{ $stats['valides_mois'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection