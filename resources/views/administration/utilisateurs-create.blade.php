@extends('layouts.gdf')

@section('title', 'Nouvel utilisateur')

@section('content')

@if($errors->any())
    <div class="container-fluid mt-3">
        <div class="alert alert-danger">
            <strong>Erreurs de validation :</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

@if(session('success'))
    <div class="container-fluid mt-3">
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    </div>
@endif

@if(session('error'))
    <div class="container-fluid mt-3">
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    </div>
@endif

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3">
                <i class="bi bi-person-plus me-2"></i>Nouvel utilisateur
            </h1>
            <p class="text-muted">
                Créer un nouvel utilisateur pour la Direction des Régimes Spécifiques
            </p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.utilisateurs') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.utilisateurs-store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nom complet <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Matricule <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="matricule" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Poste <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="poste" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Rôle <span class="text-danger">*</span></label>
                        <select class="form-select" name="role_id" required>
                            <option value="">Sélectionner...</option>
                            <option value="3">Secrétaire</option>
                            <option value="4">Agent</option>
                            <option value="5">Chef de Service</option>
                            <option value="6">Directeur DRS</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Service</label>
                        <select class="form-select" name="service_id">
                            <option value="">Aucun</option>
                            @foreach($services ?? [] as $service)
                                <option value="{{ $service->id }}">{{ $service->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mot de passe <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Confirmer <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="password_confirmation" required>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="actif" value="1" checked>
                        <label class="form-check-label">Compte actif</label>
                    </div>
                </div>

                <div class="text-end">
                    <button type="reset" class="btn btn-outline-secondary">Réinitialiser</button>
                    <button type="submit" class="btn btn-primary">Créer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection