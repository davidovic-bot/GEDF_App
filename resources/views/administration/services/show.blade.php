@extends('layouts.app')

@section('title', 'Détails du service')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3">
                <i class="bi bi-building me-2"></i>{{ $service->nom_complet }}
            </h1>
            <p class="text-muted">
                Code: {{ $service->code }} | Statut: 
                <span class="badge bg-{{ $service->couleur_statut }}">{{ $service->statut }}</span>
            </p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Modifier
            </a>
            <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informations générales</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th style="width: 150px">Code</th>
                            <td>{{ $service->code }}</td>
                        </tr>
                        <tr>
                            <th>Nom</th>
                            <td>{{ $service->nom }}</td>
                        </tr>
                        <tr>
                            <th>Sigle</th>
                            <td>{{ $service->sigle ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <td>{{ $service->description ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $service->email ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Téléphone</th>
                            <td>{{ $service->telephone ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Ordre d'affichage</th>
                            <td>{{ $service->ordre_affichage }}</td>
                        </tr>
                        <tr>
                            <th>Date création</th>
                            <td>{{ $service->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Responsable</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th style="width: 150px">Nom</th>
                            <td>{{ $service->responsable_nom ?? 'Non défini' }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $service->responsable_email ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Téléphone</th>
                            <td>{{ $service->responsable_telephone ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Statistiques</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="p-3">
                                <h4>{{ $service->nombre_utilisateurs }}</h4>
                                <small class="text-muted">Utilisateurs</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-3">
                                <h4>{{ $service->nombre_courriers }}</h4>
                                <small class="text-muted">Courriers</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-3">
                                <h4>{{ $service->courriers_en_cours }}</h4>
                                <small class="text-muted">En cours</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection