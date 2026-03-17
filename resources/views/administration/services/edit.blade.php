@extends('layouts.app')

@section('title', 'Modifier un service')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3">
                <i class="bi bi-building-gear me-2"></i>Modifier le service
            </h1>
            <p class="text-muted">
                {{ $service->nom }}
            </p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.services.update', $service) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="code" class="form-label">Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror" 
                               id="code" name="code" value="{{ old('code', $service->code) }}" required>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="sigle" class="form-label">Sigle</label>
                        <input type="text" class="form-control @error('sigle') is-invalid @enderror" 
                               id="sigle" name="sigle" value="{{ old('sigle', $service->sigle) }}">
                        @error('sigle')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="nom" class="form-label">Nom complet <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nom') is-invalid @enderror" 
                           id="nom" name="nom" value="{{ old('nom', $service->nom) }}" required>
                    @error('nom')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="3">{{ old('description', $service->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email', $service->email) }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="telephone" class="form-label">Téléphone</label>
                        <input type="text" class="form-control @error('telephone') is-invalid @enderror" 
                               id="telephone" name="telephone" value="{{ old('telephone', $service->telephone) }}">
                        @error('telephone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="ordre_affichage" class="form-label">Ordre d'affichage</label>
                        <input type="number" class="form-control @error('ordre_affichage') is-invalid @enderror" 
                               id="ordre_affichage" name="ordre_affichage" value="{{ old('ordre_affichage', $service->ordre_affichage) }}">
                        @error('ordre_affichage')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="est_actif" class="form-label">Statut</label>
                        <select class="form-select" id="est_actif" name="est_actif">
                            <option value="1" {{ (old('est_actif', $service->est_actif) == 1) ? 'selected' : '' }}>Actif</option>
                            <option value="0" {{ (old('est_actif', $service->est_actif) == 0) ? 'selected' : '' }}>Inactif</option>
                        </select>
                    </div>
                </div>

                <h5 class="mt-4 mb-3">Responsable du service</h5>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="responsable_nom" class="form-label">Nom du responsable</label>
                        <input type="text" class="form-control" id="responsable_nom" 
                               name="responsable_nom" value="{{ old('responsable_nom', $service->responsable_nom) }}">
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="responsable_email" class="form-label">Email du responsable</label>
                        <input type="email" class="form-control" id="responsable_email" 
                               name="responsable_email" value="{{ old('responsable_email', $service->responsable_email) }}">
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="responsable_telephone" class="form-label">Téléphone du responsable</label>
                        <input type="text" class="form-control" id="responsable_telephone" 
                               name="responsable_telephone" value="{{ old('responsable_telephone', $service->responsable_telephone) }}">
                    </div>
                </div>

                <div class="text-end mt-3">
                    <button type="reset" class="btn btn-outline-secondary">Réinitialiser</button>
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection