@extends('layouts.gdf')

@section('title', 'Modifier un rôle')

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
                <i class="bi bi-shield-edit me-2"></i>Modifier le rôle
            </h1>
            <p class="text-muted">
                Modifier le rôle <strong>{{ $role->name }}</strong>
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
            <form action="{{ route('admin.roles-update', $role->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nom du rôle <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               name="name" value="{{ old('name', $role->name) }}" required>
                        <small class="text-muted">
                            Utiliser des lettres minuscules et des underscores (ex: superviseur)
                        </small>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="alert alert-warning mt-3">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>Attention :</strong> La modification du nom d'un rôle peut affecter les permissions associées.
                </div>

                <div class="text-end mt-3">
                    <button type="reset" class="btn btn-outline-secondary">Réinitialiser</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection