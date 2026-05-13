@extends('layouts.gdf')

@section('title', 'Créer un rôle')

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

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3">
                <i class="bi bi-shield-plus me-2"></i>Créer un rôle
            </h1>
            <p class="text-muted">
                Ajouter un nouveau rôle dans le système
            </p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.roles-list') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.roles-store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nom du rôle <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               name="name" value="{{ old('name') }}" required 
                               placeholder="ex: controleur, auditeur, superviseur">
                        <small class="text-muted">
                            Utiliser des lettres minuscules et des underscores (ex: controleur)
                        </small>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="alert alert-info mt-3">
                    <i class="bi bi-info-circle"></i>
                    Le rôle sera créé avec le guard_name "web" par défaut.
                </div>

                <div class="text-end mt-3">
                    <button type="reset" class="btn btn-outline-secondary">Réinitialiser</button>
                    <button type="submit" class="btn btn-primary">Créer le rôle</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection