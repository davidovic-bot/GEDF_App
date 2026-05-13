@extends('layouts.gdf')

@section('title', 'Gestion des utilisateurs')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3">
                <i class="bi bi-people me-2"></i>Utilisateurs
            </h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.utilisateurs-create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nouvel utilisateur
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Service</th>
                        <th>Rôle</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users ?? [] as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->service->nom ?? '-' }}</td>
                        <td>
                            @foreach($user->roles ?? [] as $role)
                                <span class="badge bg-info">{{ $role->name }}</span>
                            @endforeach
                        </td>
                        <td>
                            <span class="badge bg-{{ $user->actif ? 'success' : 'secondary' }}">
                                {{ $user->actif ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.utilisateurs-edit', $user->id) }}" class="btn btn-sm btn-warning" title="Modifier">
                            <i class="bi bi-pencil"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">Aucun utilisateur</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection