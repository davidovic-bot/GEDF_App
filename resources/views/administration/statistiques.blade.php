@extends('layouts.app')

@section('title', 'Statistiques - Administration')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3">
                <i class="bi bi-bar-chart-fill me-2"></i>Statistiques
            </h1>
            <p class="text-muted">
                Analyse et suivi des performances du système de gestion des dépenses fiscales
            </p>
        </div>
    </div>

    <!-- Filtres de période -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.statistiques.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Période</label>
                    <select name="periode" class="form-select" onchange="this.form.submit()">
                        <option value="7" {{ request('periode') == 7 ? 'selected' : '' }}>7 derniers jours</option>
                        <option value="30" {{ request('periode') == 30 ? 'selected' : '' }}>30 derniers jours</option>
                        <option value="90" {{ request('periode') == 90 ? 'selected' : '' }}>3 derniers mois</option>
                        <option value="365" {{ request('periode') == 365 ? 'selected' : '' }}>12 derniers mois</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Service</label>
                    <select name="service" class="form-select" onchange="this.form.submit()">
                        <option value="all">Tous les services</option>
                        @foreach($services ?? [] as $service)
                        <option value="{{ $service->id }}" {{ request('service') == $service->id ? 'selected' : '' }}>
                            {{ $service->nom }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel"></i> Appliquer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Cartes récapitulatives -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-1">TOTAL PARAPHEURS</h6>
                    <h2 class="mb-0">{{ $stats['parapheurs']['total'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-1">EN COURS</h6>
                    <h2 class="mb-0 text-warning">{{ $stats['parapheurs']['par_statut']['en_cours'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-1">VALIDÉS</h6>
                    <h2 class="mb-0 text-success">{{ $stats['parapheurs']['par_statut']['valide'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-1">REJETÉS</h6>
                    <h2 class="mb-0 text-danger">{{ $stats['parapheurs']['par_statut']['rejete'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques principaux -->
    <div class="row g-4 mb-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="card-title mb-0">Évolution mensuelle {{ date('Y') }}</h5>
                </div>
                <div class="card-body">
                    <canvas id="evolutionChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="card-title mb-0">Répartition par statut</h5>
                </div>
                <div class="card-body">
                    <canvas id="statutChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top utilisateurs et services -->
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="card-title mb-0">Top 5 utilisateurs</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Utilisateur</th>
                                <th class="text-center">Parapheurs</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topUsers ?? [] as $index => $user)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $user->name }}</td>
                                <td class="text-center">{{ $user->parapheurs_count ?? 0 }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center">Aucune donnée</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="card-title mb-0">Parapheurs par service</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Service</th>
                                <th class="text-center">Parapheurs</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($parService ?? [] as $service)
                            <tr>
                                <td>{{ $service->nom }}</td>
                                <td class="text-center">{{ $service->parapheurs_count ?? 0 }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-center">Aucune donnée</td>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Graphique d'évolution
    const ctx1 = document.getElementById('evolutionChart').getContext('2d');
    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'],
            datasets: [{
                label: 'Parapheurs',
                data: {!! json_encode($evolution->pluck('total') ?? []) !!},
                borderColor: '#0d6efd'
            }]
        }
    });

    // Graphique des statuts
    const ctx2 = document.getElementById('statutChart').getContext('2d');
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['Brouillon', 'En attente', 'En cours', 'Validé', 'Rejeté', 'Archivé'],
            datasets: [{
                data: [
                    {{ $stats['parapheurs']['par_statut']['brouillon'] ?? 0 }},
                    {{ $stats['parapheurs']['par_statut']['en_attente'] ?? 0 }},
                    {{ $stats['parapheurs']['par_statut']['en_cours'] ?? 0 }},
                    {{ $stats['parapheurs']['par_statut']['valide'] ?? 0 }},
                    {{ $stats['parapheurs']['par_statut']['rejete'] ?? 0 }},
                    {{ $stats['parapheurs']['par_statut']['archive'] ?? 0 }}
                ],
                backgroundColor: ['#6c757d', '#ffc107', '#0dcaf0', '#198754', '#dc3545', '#0d6efd']
            }]
        }
    });
});
</script>
@endpush