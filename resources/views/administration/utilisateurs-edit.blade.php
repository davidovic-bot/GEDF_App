@extends('layouts.gdf')

@section('title', 'Modifier utilisateur')

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

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3">
                <i class="bi bi-pencil-square me-2"></i>Modifier utilisateur
            </h1>
            <p class="text-muted">
                Modifier les informations de <strong>{{ $user->name }}</strong>
            </p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.utilisateurs') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Retour à la liste
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.utilisateurs-update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nom complet <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Matricule <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('matricule') is-invalid @enderror" 
                               name="matricule" value="{{ old('matricule', $user->matricule) }}" required>
                        @error('matricule')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Poste <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('poste') is-invalid @enderror" 
                               name="poste" value="{{ old('poste', $user->poste) }}" required>
                        @error('poste')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Rôle <span class="text-danger">*</span></label>
                        <select class="form-select @error('role_id') is-invalid @enderror" name="role_id" required>
                            <option value="">Sélectionner...</option>
                            @php
                                $currentRoleId = $user->roles->first()->id ?? null;
                            @endphp
                            <option value="3" {{ old('role_id', $currentRoleId) == 3 ? 'selected' : '' }}>Secrétaire</option>
                            <option value="4" {{ old('role_id', $currentRoleId) == 4 ? 'selected' : '' }}>Agent</option>
                            <option value="5" {{ old('role_id', $currentRoleId) == 5 ? 'selected' : '' }}>Chef de Service</option>
                            <option value="6" {{ old('role_id', $currentRoleId) == 6 ? 'selected' : '' }}>Directeur DRS</option>
                        </select>
                        @error('role_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Service</label>
                        <select class="form-select @error('service_id') is-invalid @enderror" name="service_id">
                            <option value="">Aucun</option>
                            @foreach($services ?? [] as $service)
                                <option value="{{ $service->id }}" {{ old('service_id', $user->service_id) == $service->id ? 'selected' : '' }}>
                                    {{ $service->nom }} ({{ $service->code }})
                                </option>
                            @endforeach
                        </select>
                        @error('service_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nouveau mot de passe</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               name="password" placeholder="Laisser vide pour ne pas changer">
                        <small class="text-muted">Minimum 8 caractères</small>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Confirmer le mot de passe</label>
                        <input type="password" class="form-control" name="password_confirmation">
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="actif" value="1" {{ old('actif', $user->actif) ? 'checked' : '' }}>
                        <label class="form-check-label">Compte actif</label>
                    </div>
                </div>

                <div class="text-end">
                    <button type="reset" class="btn btn-outline-secondary">Réinitialiser</button>
                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection