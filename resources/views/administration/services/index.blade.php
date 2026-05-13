@extends('layouts.gdf')

@section('title', 'Gestion des services')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3">
                <i class="bi bi-building me-2"></i>Gestion des services
            </h1>
            <p class="text-muted">
                Liste des services de la Direction des Régimes Spécifiques (DRS)
            </p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nouveau service
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Nom</th>
                        <th>Sigle</th>
                        <th>Chef de service</th>
                        <th>Utilisateurs</th>
                        <th>Courriers</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($services as $service)
                    <tr>
                        <td><strong>{{ $service->code }}</strong></td>
                        <td>{{ $service->nom }}</td>
                        <td>{{ $service->sigle ?? '-' }}</td>
                        <td>{{ $service->nom_responsable ?? '-' }}</td>
                        <td>
                            <span class="badge bg-info">{{ $service->nombre_utilisateurs }}</span>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $service->nombre_courriers }}</span>
                            @if($service->courriers_en_cours > 0)
                                <span class="badge bg-warning">{{ $service->courriers_en_cours }} en cours</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $service->couleur_statut }}">
                                {{ $service->statut }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.services.show', $service) }}" class="btn btn-sm btn-info" title="Voir">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-sm btn-warning" title="Modifier">
                                <i class="bi bi-pencil"></i>
                            </a>

                            @if($service->est_actif)
                            <form action="{{ route('admin.services.desactiver', $service) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-secondary" title="Désactiver">
                                    <i class="bi bi-pause"></i>
                                </button>
                            </form>
                            @else
                            <form action="{{ route('admin.services.activer', $service) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success" title="Activer">
                                    <i class="bi bi-play"></i>
                                </button>
                            </form>
                            @endif

                            <form action="{{ route('admin.services.destroy', $service) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce service ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            Aucun service trouvé
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection