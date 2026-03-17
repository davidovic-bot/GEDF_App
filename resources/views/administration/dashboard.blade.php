@extends('layouts.app')

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
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-1">UTILISATEURS</h6>
                    <h2 class="mb-0">{{ $stats['users']['total'] ?? 0 }}</h2>
                    <small class="text-success">{{ $stats['users']['actifs'] ?? 0 }} actifs</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-1">PARAPHEURS</h6>
                    <h2 class="mb-0">{{ $stats['parapheurs']['total'] ?? 0 }}</h2>
                    <small class="text-warning">{{ $stats['parapheurs']['en_cours'] ?? 0 }} en cours</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-1">SERVICES</h6>
                    <h2 class="mb-0">{{ $stats['services']['total'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-1">RÔLES</h6>
                    <h2 class="mb-0">{{ $stats['roles']['total'] ?? 0 }}</h2>
                </div>
            </div>
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
@endsection