<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervision Parapheurs - GEDF</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                📁 Module Parapheur - Supervision
            </a>
            <span class="text-white">
                Superadmin
            </span>
        </div>
    </nav>

    <div class="container">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">✅ ACCÈS RÉUSSI AU MODULE PARAPHEUR</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h5>Félicitations !</h5>
                    <p class="mb-0">Vous avez accédé avec succès au module Parapheur en tant que <strong>Superadmin</strong>.</p>
                </div>
                
                <h5 class="mt-4">📊 Données disponibles :</h5>
                
                @php
                    use Illuminate\Support\Facades\DB;
                    $total = DB::table('parapheurs')->count();
                    $parStatut = DB::table('parapheurs')
                        ->join('parapheur_statuts', 'parapheurs.statut_id', '=', 'parapheur_statuts.id')
                        ->select('parapheur_statuts.nom', 'parapheur_statuts.couleur', DB::raw('count(*) as total'))
                        ->groupBy('parapheur_statuts.nom', 'parapheur_statuts.couleur')
                        ->get();
                @endphp
                
                <div class="row mt-3">
                    <div class="col-md-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h1 class="display-3">{{ $total }}</h1>
                                <p class="mb-0">Parapheurs totaux</p>
                            </div>
                        </div>
                    </div>
                    
                    @foreach($parStatut as $stat)
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h2>{{ $stat->total }}</h2>
                                <p class="mb-0" style="color: {{ $stat->couleur }}">
                                    {{ $stat->nom }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <div class="mt-4">
                    <a href="{{ route('dashboard.superadmin') }}" class="btn btn-primary">
                        ← Retour au tableau de bord
                    </a>
                    <button onclick="window.location.reload()" class="btn btn-secondary">
                        Actualiser
                    </button>
                </div>
            </div>
            <div class="card-footer text-muted">
                Module Parapheur GEDF - Version de test
            </div>
        </div>
    </div>
</body>
</html>