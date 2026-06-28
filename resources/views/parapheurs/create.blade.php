@extends('layouts.gdf')

@section('title', 'Enregistrement d\'un Parapheur - GDF')

@section('content')
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
<div class="container-fluid">
    <!-- Breadcrumb -->
    <div class="row mb-3">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb" style="background: rgba(255,255,255,0.1); border-radius: 8px; padding: 10px 15px;">
                    
                </ol>
            </nav>
        </div>
    </div>

    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 style="color: #0b0b0b !important; font-size: 1.8rem;">
                    <i class="fas fa-plus-circle text-primary mr-2"></i>Enregistrement d'un parapheur
                </h1>
                <a href="{{ route('parapheurs.index') }}" class="btn btn-light">
                    <i class="fas fa-arrow-left mr-1"></i> Retour
                </a>
            </div>
            <p style="color: rgba(87, 82, 82, 0.7) !important;"></p>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="row">
        <div class="col-12">
            <div style="background: #ffffff; border-radius: 12px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
                <h5 style="color: #1a1a1a !important; border-bottom: 2px solid #3B82F6; padding-bottom: 10px; margin-bottom: 25px;">
                    <i class="fas fa-file-alt text-primary mr-2"></i>Informations du parapheur
                </h5>
                <form action="{{ route('parapheurs.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    

                    <!-- Ligne : Type d'attestation + Service concerné (côte à côte) -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="type_attestation" class="form-label" style="color: #1a1a1a !important; font-weight: 600;">
                                    <i class="fas fa-certificate me-1"></i>Type d'attestation <span class="text-danger">*</span>
                                </label>
                                <select name="type_attestation" id="type_attestation" 
                                        class="form-control @error('type_attestation') is-invalid @enderror" 
                                        required
                                        style="border: 1px solid #ced4da; border-radius: 6px; padding: 10px 12px; color: #1a1a1a;">
                                    <option value="">Sélectionner</option>
                                    <option value="exoneration_tva" {{ old('type_attestation') == 'exoneration_tva' ? 'selected' : '' }}>
                                        Exonération ouverte TVA
                                    </option>
                                    <option value="dispense_tva" {{ old('type_attestation') == 'dispense_tva' ? 'selected' : '' }}>
                                        Dispense ouverte TVA
                                    </option>
                                    <option value="exoneration_css" {{ old('type_attestation') == 'exoneration_css' ? 'selected' : '' }}>
                                        Exonération ouverte CSS
                                    </option>
                                    <option value="dispense_css" {{ old('type_attestation') == 'dispense_css' ? 'selected' : '' }}>
                                        Dispense ouverte CSS
                                    </option>
                                </select>
                                @error('type_attestation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small style="color: #6c757d !important;">Type d'attestation délivrée au contribuable</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="service_expediteur" class="form-label" style="color: #1a1a1a !important; font-weight: 600;">
                                    <i class="fas fa-building me-1"></i>Service concerné <span class="text-danger">*</span>
                                </label>
                                <select name="service_expediteur" 
                                        id="service_expediteur" 
                                        class="form-control @error('service_expediteur') is-invalid @enderror" 
                                        required
                                        style="border: 1px solid #ced4da; border-radius: 6px; padding: 10px 12px; color: #1a1a1a;">
                                    <option value="">Sélectionnez un service</option>
                                    <option value="IFD" {{ old('service_expediteur') == 'IFD' ? 'selected' : '' }}>IFD - Incitations Fiscales pour le Développement</option>
                                    <option value="GFMPF" {{ old('service_expediteur') == 'GFMPF' ? 'selected' : '' }}>GFMPF - Gestion Fiscales Mines, Pétrole et Forêts</option>
                                    <option value="ZES" {{ old('service_expediteur') == 'ZES' ? 'selected' : '' }}>ZES - Zones Économiques Spéciales</option>
                                </select>
                                @error('service_expediteur')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small style="color: #6c757d !important;">Service qui a délivré l'attestation</small>
                            </div>
                        </div>
                    </div>

                    
                    <div class="row">
                        <!-- Date de réception -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date_reception" class="form-label" style="color: #1a1a1a !important; font-weight: 600;">
                                    <i class="fas fa-calendar me-1"></i>Date de réception <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control @error('date_reception') is-invalid @enderror" 
                                       id="date_reception" 
                                       name="date_reception" 
                                       value="{{ old('date_reception', date('Y-m-d')) }}"
                                       required
                                       style="border: 1px solid #ced4da; border-radius: 6px; padding: 10px 12px; color: #1a1a1a;">
                                @error('date_reception')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Date limite -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date_limite" class="form-label" style="color: #1a1a1a !important; font-weight: 600;">
                                    <i class="fas fa-clock me-1"></i>Date limite <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control @error('date_limite') is-invalid @enderror" 
                                       id="date_limite" 
                                       name="date_limite" 
                                       value="{{ old('date_limite', date('Y-m-d', strtotime('+15 days'))) }}"
                                       required
                                       style="border: 1px solid #ced4da; border-radius: 6px; padding: 10px 12px; color: #1a1a1a;">
                                @error('date_limite')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small style="color: #6c757d !important;">Délai de traitement recommandé : 15 jours</small>
                            </div>
                        </div>
                    </div>

                    

                    <!-- ============================================ -->
                    <!-- UPLOAD DE FICHIERS (PIÈCES JOINTES) -->
                    <!-- ============================================ -->
                    <div class="mb-3">
                        <label class="form-label" style="color: #1a1a1a !important; font-weight: 600;">
                            <i class="fas fa-paperclip me-1"></i>Pièces jointes
                        </label>
                        <input type="file" 
                               class="form-control @error('fichiers.*') is-invalid @enderror" 
                               id="fichiers" 
                               name="fichiers[]" 
                               multiple
                               accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.png"
                               style="border: 1px solid #ced4da; border-radius: 6px; padding: 10px 12px; color: #1a1a1a;">
                        @error('fichiers.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small style="color: #6c757d !important;">
                            Formats acceptés: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG (max 10 Mo par fichier)
                        </small>
                        <div id="file-list" class="mt-2"></div>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="mt-4" style="border-top: 1px solid #e9ecef; padding-top: 20px;">
                        <button type="submit" class="btn btn-primary px-4" style="padding: 10px 25px;">
                            <i class="fas fa-save me-1"></i> Enregistrer
                        </button>
                        <a href="{{ route('parapheurs.index') }}" class="btn btn-secondary px-4" style="padding: 10px 25px;">
                            <i class="fas fa-times me-1"></i> Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Styles pour les champs de formulaire */
    .form-control {
        border: 1px solid #ced4da !important;
        border-radius: 6px !important;
        padding: 10px 12px !important;
        color: #1a1a1a !important;
        background: #ffffff !important;
    }
    .form-control:focus {
        border-color: #3B82F6 !important;
        box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25) !important;
    }
    .form-control::placeholder {
        color: #6c757d !important;
    }
    .form-check-input {
        background-color: #ffffff !important;
        border: 1px solid #ced4da !important;
    }
    .form-check-input:checked {
        background-color: #3B82F6 !important;
        border-color: #3B82F6 !important;
    }
    .form-label {
        color: #1a1a1a !important;
        font-weight: 600 !important;
    }
    .invalid-feedback {
        color: #dc3545 !important;
    }
    .breadcrumb-item a {
        color: #3B82F6 !important;
        text-decoration: none;
    }
    .breadcrumb-item a:hover {
        text-decoration: underline;
    }
    .breadcrumb-item.active {
        color: #ffffff !important;
    }
    /* Liste des fichiers */
    #file-list .file-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 12px;
        background: #f8f9fa;
        border-radius: 4px;
        margin-bottom: 5px;
        font-size: 0.9em;
        color: #1a1a1a;
    }
    #file-list .file-item .file-size {
        color: #6c757d;
        font-size: 0.85em;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('fichiers');
        const fileList = document.getElementById('file-list');
        
        fileInput.addEventListener('change', function() {
            fileList.innerHTML = '';
            if (this.files.length > 0) {
                Array.from(this.files).forEach(file => {
                    const fileSize = (file.size / 1024 / 1024).toFixed(2);
                    const fileItem = document.createElement('div');
                    fileItem.className = 'file-item';
                    fileItem.innerHTML = `
                        <span><i class="fas fa-file me-2"></i>${file.name}</span>
                        <span class="file-size">${fileSize} MB</span>
                    `;
                    fileList.appendChild(fileItem);
                });
            }
        });
    });
</script>
@endpush