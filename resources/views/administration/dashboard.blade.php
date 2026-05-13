@extends('layouts.gdf')

@section('title', 'Tableau de bord - Administration')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3">
                <i class="bi bi-speedometer2 me-2"></i>Tableau de bord administration
            </h1>
            <p class="text-muted">
                Supervision du système de gestion des dépenses fiscales
            </p>
        </div>
    </div>
    
    <!-- Cartes statistiques -->
<div class="row g-4 mb-4">
    <!-- Utilisateurs -->
    <div class="col-md-3">
        <a href="{{ route('admin.utilisateurs') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm hover-card">
                <div class="card-body">
                    <h6 class="text-muted mb-1">UTILISATEURS</h6>
                    <h2 class="mb-0">{{ $stats['users']['total'] ?? 0 }}</h2>
                    <small class="text-success">{{ $stats['users']['actifs'] ?? 0 }} actifs</small>
                </div>
            </div>
        </a>
    </div>

    <!-- Parapheurs -->
    <div class="col-md-3">
        <a href="{{ route('parapheurs.index') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm hover-card">
                <div class="card-body">
                    <h6 class="text-muted mb-1">PARAPHEURS</h6>
                    <h2 class="mb-0">{{ $stats['parapheurs']['total'] ?? 0 }}</h2>
                    <small class="text-warning">{{ $stats['parapheurs']['en_cours'] ?? 0 }} en cours</small>
                </div>
            </div>
        </a>
    </div>

    <!-- Services -->
    <div class="col-md-3">
        <a href="{{ route('admin.services.index') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm hover-card">
                <div class="card-body">
                    <h6 class="text-muted mb-1">SERVICES</h6>
                    <h2 class="mb-0">{{ $stats['services']['total'] ?? 0 }}</h2>
                </div>
            </div>
        </a>
    </div>

    <!-- Rôles -->
    <div class="col-md-3">
        <a href="{{ route('admin.roles-list') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm hover-card">
                <div class="card-body">
                    <h6 class="text-muted mb-1">RÔLES</h6>
                    <h2 class="mb-0">{{ $stats['roles']['total'] ?? 0 }}</h2>
                </div>
            </div>
        </a>
    </div>
</div>

    <!-- Derniers utilisateurs -->
    <div class="row">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="card-title mb-0">Derniers utilisateurs</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentUsers ?? [] as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->created_at->format('d/m/Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center">Aucun utilisateur</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
.hover-card {
    transition: transform 0.2s, box-shadow 0.2s;
    cursor: pointer;
}
.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
</style>
@endsection