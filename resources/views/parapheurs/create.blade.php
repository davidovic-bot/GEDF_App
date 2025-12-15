@extends('layouts.app')

@section('title', 'Nouveau Parapheur - GDF')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <div class="row mb-3">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Tableau de bord</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('parapheurs.index') }}">Parapheurs</a></li>
                    <li class="breadcrumb-item active">Nouveau courrier</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 text-gray-800">
                    <i class="fas fa-plus-circle text-primary mr-2"></i>Nouveau Parapheur
                </h1>
                <a href="{{ route('parapheurs.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Retour
                </a>
            </div>
            <p class="text-muted">Enregistrement d'un nouveau courrier administratif</p>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-file-alt mr-2"></i>Informations du courrier
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('parapheurs.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Section 1: Informations de base -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-info-circle text-primary mr-2"></i>Description
                                </h5>
                                
                                <!-- Objet -->
                                <div class="form-group">
                                    <label for="objet" class="font-weight-bold">
                                        Objet du courrier <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('objet') is-invalid @enderror" 
                                           id="objet" 
                                           name="objet" 
                                           value="{{ old('objet') }}"
                                           placeholder="Ex: Demande d'exonération fiscale pour..."
                                           required>
                                    @error('objet')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Maximum 500 caractères</small>
                                </div>

                                <!-- Description -->
                                <div class="form-group">
                                    <label for="description" class="font-weight-bold">
                                        Description détaillée <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" 
                                              name="description" 
                                              rows="5"
                                              placeholder="Décrivez le contenu du courrier..."
                                              required>{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-cogs text-primary mr-2"></i>Paramètres
                                </h5>

                                <!-- Priorité -->
                                <div class="form-group">
                                    <label class="font-weight-bold">Priorité <span class="text-danger">*</span></label>
                                    <div class="row">
                                        @foreach(['basse' => 'Basse', 'normale' => 'Normale', 'haute' => 'Haute', 'urgente' => 'Urgente'] as $value => $label)
                                        <div class="col-6 mb-2">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" 
                                                       id="priorite_{{ $value }}" 
                                                       name="priorite" 
                                                       value="{{ $value }}"
                                                       class="custom-control-input @error('priorite') is-invalid @enderror"
                                                       {{ old('priorite', 'normale') == $value ? 'checked' : '' }}
                                                       required>
                                                <label class="custom-control-label" for="priorite_{{ $value }}">
                                                    @if($value == 'urgente')
                                                    <span class="text-danger font-weight-bold">{{ $label }}</span>
                                                    @elseif($value == 'haute')
                                                    <span class="text-warning font-weight-bold">{{ $label }}</span>
                                                    @else
                                                    {{ $label }}
                                                    @endif
                                                </label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    @error('priorite')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Confidentialité -->
                                <div class="form-group">
                                    <label for="confidentialite" class="font-weight-bold">Confidentialité</label>
                                    <select class="form-control @error('confidentialite') is-invalid @enderror" 
                                            id="confidentialite" 
                                            name="confidentialite">
                                        <option value="standard" {{ old('confidentialite', 'standard') == 'standard' ? 'selected' : '' }}>
                                            Standard
                                        </option>
                                        <option value="confidentiel" {{ old('confidentialite') == 'confidentiel' ? 'selected' : '' }}>
                                            Confidentiel
                                        </option>
                                        <option value="tres_confidentiel" {{ old('confidentialite') == 'tres_confidentiel' ? 'selected' : '' }}>
                                            Très confidentiel
                                        </option>
                                    </select>
                                    @error('confidentialite')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Date d'échéance -->
                                <div class="form-group">
                                    <label for="date_echeance" class="font-weight-bold">
                                        Date d'échéance <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" 
                                           class="form-control @error('date_echeance') is-invalid @enderror" 
                                           id="date_echeance" 
                                           name="date_echeance" 
                                           value="{{ old('date_echeance', \Carbon\Carbon::now()->addDays(7)->format('Y-m-d')) }}"
                                           min="{{ \Carbon\Carbon::now()->addDay()->format('Y-m-d') }}"
                                           required>
                                    @error('date_echeance')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">La date limite de traitement</small>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Affectation -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-building text-primary mr-2"></i>Affectation administrative
                                </h5>
                            </div>
                            
                            <!-- Service -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="service_id" class="font-weight-bold">
                                        Service concerné <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control @error('service_id') is-invalid @enderror" 
                                            id="service_id" 
                                            name="service_id" 
                                            required>
                                        <option value="">Sélectionnez un service</option>
                                        @foreach($services as $service)
                                        <option value="{{ $service->id }}" 
                                                {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                            {{ $service->nom }}
                                            @if($service->chef)
                                            (Chef: {{ $service->chef->name }})
                                            @endif
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('service_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Direction -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="direction_id" class="font-weight-bold">
                                        Direction <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control @error('direction_id') is-invalid @enderror" 
                                            id="direction_id" 
                                            name="direction_id" 
                                            required>
                                        <option value="">Sélectionnez une direction</option>
                                        @foreach($directions as $direction)
                                        <option value="{{ $direction->id }}" 
                                                {{ old('direction_id') == $direction->id ? 'selected' : '' }}>
                                            {{ $direction->nom }}
                                            @if($direction->directeur)
                                            (Directeur: {{ $direction->directeur->name }})
                                            @endif
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('direction_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: Pièces jointes -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-paperclip text-primary mr-2"></i>Pièces jointes
                                </h5>
                                
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Formats acceptés: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG
                                    <br>
                                    Taille maximale: 10 MB par fichier
                                </div>

                                <div class="form-group">
                                    <label for="fichiers" class="font-weight-bold">Documents à joindre</label>
                                    <div class="custom-file">
                                        <input type="file" 
                                               class="custom-file-input @error('fichiers.*') is-invalid @enderror" 
                                               id="fichiers" 
                                               name="fichiers[]" 
                                               multiple
                                               accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.png">
                                        <label class="custom-file-label" for="fichiers" id="fichiers-label">
                                            Choisissez les fichiers...
                                        </label>
                                        @error('fichiers.*')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <!-- Liste des fichiers sélectionnés -->
                                    <div id="file-list" class="mt-2"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('parapheurs.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times mr-1"></i> Annuler
                                    </a>
                                    
                                    <div>
                                        <button type="submit" name="action" value="save" class="btn btn-outline-primary mr-2">
                                            <i class="fas fa-save mr-1"></i> Enregistrer comme brouillon
                                        </button>
                                        
                                        <button type="submit" name="action" value="submit" class="btn btn-primary">
                                            <i class="fas fa-paper-plane mr-1"></i> Soumettre le courrier
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Informations -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="alert alert-light border">
                <h6><i class="fas fa-lightbulb mr-2"></i>Bon à savoir</h6>
                <ul class="mb-0">
                    <li>Le courrier sera automatiquement assigné au chef du service sélectionné</li>
                    <li>Une référence unique sera générée automatiquement</li>
                    <li>Vous pourrez suivre l'avancement du traitement dans la liste des parapheurs</li>
                    <li>Les notifications seront envoyées aux responsables à chaque étape</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .custom-file-label::after {
        content: "Parcourir";
    }
    .file-item {
        padding: 5px 10px;
        background: #f8f9fa;
        border-radius: 4px;
        margin-bottom: 5px;
        font-size: 0.9em;
    }
    .file-item .file-size {
        color: #6c757d;
        font-size: 0.85em;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestion de l'affichage des noms de fichiers
        const fileInput = document.getElementById('fichiers');
        const fileLabel = document.getElementById('fichiers-label');
        const fileList = document.getElementById('file-list');
        
        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                fileLabel.textContent = this.files.length + ' fichier(s) sélectionné(s)';
                
                // Afficher la liste des fichiers
                fileList.innerHTML = '';
                Array.from(this.files).forEach(file => {
                    const fileSize = (file.size / 1024 / 1024).toFixed(2);
                    const fileItem = document.createElement('div');
                    fileItem.className = 'file-item d-flex justify-content-between align-items-center';
                    fileItem.innerHTML = `
                        <span>${file.name}</span>
                        <span class="file-size">${fileSize} MB</span>
                    `;
                    fileList.appendChild(fileItem);
                });
            } else {
                fileLabel.textContent = 'Choisissez les fichiers...';
                fileList.innerHTML = '';
            }
        });

        // Validation de la date d'échéance
        const dateEcheance = document.getElementById('date_echeance');
        const today = new Date().toISOString().split('T')[0];
        
        dateEcheance.addEventListener('change', function() {
            const selectedDate = new Date(this.value);
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            
            if (selectedDate < tomorrow) {
                alert('La date d\'échéance doit être au moins demain.');
                this.value = tomorrow.toISOString().split('T')[0];
            }
        });

        // Afficher un aperçu des sélections
        const serviceSelect = document.getElementById('service_id');
        const directionSelect = document.getElementById('direction_id');
        
        function updatePreview() {
            console.log('Service:', serviceSelect.value);
            console.log('Direction:', directionSelect.value);
        }
        
        serviceSelect.addEventListener('change', updatePreview);
        directionSelect.addEventListener('change', updatePreview);
    });
</script>
@endpush