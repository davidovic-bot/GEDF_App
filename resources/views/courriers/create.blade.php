<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau Dossier Fiscal - GDF</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .type-card-wrapper {
            position: relative;
            height: 100%;
        }
        
        .type-radio {
            position: absolute;
            opacity: 0;
        }
        
        .type-label {
            display: block;
            height: 100%;
            cursor: pointer;
        }
        
        .type-card {
            transition: all 0.3s ease;
            border: 2px solid #e9ecef;
            height: 100%;
        }
        
        .type-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-color: #dee2e6;
        }
        
        .type-radio:checked + .type-label .type-card {
            border-color: #0d6efd;
            box-shadow: 0 5px 20px rgba(13, 110, 253, 0.2);
            background-color: #f8f9fa;
        }
        
        .type-radio:checked + .type-label .type-card .card-footer .badge {
            background-color: #0d6efd !important;
        }
        
        .icon-wrapper {
            width: 80px;
            height: 80px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background-color: #f8f9fa;
        }
        
        .form-section {
            border-left: 4px solid #0d6efd;
            padding-left: 15px;
            margin-bottom: 25px;
        }
        
        .form-section h5 {
            color: #0d6efd;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('courriers.index') }}">
                <i class="fas fa-arrow-left me-2"></i>
                Nouveau Dossier Fiscal
            </a>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h2 class="h4 mb-0">
                            <i class="fas fa-file-contract text-primary me-2"></i>
                            Création d'un nouveau dossier fiscal
                        </h2>
                        <p class="text-muted mb-0 mt-2">
                            Remplissez le formulaire ci-dessous pour créer un nouveau dossier fiscal.
                        </p>
                    </div>
                    
                    <div class="card-body">
                        <form method="POST" action="{{ route('courriers.store') }}" enctype="multipart/form-data">
                            @csrf
                            
                            <!-- Section 1 : Type de demande -->
                            <div class="form-section mb-5">
                                <h5 class="mb-4">
                                    <i class="fas fa-tag me-2"></i>
                                    1. Type de demande fiscale
                                </h5>
                                
                                <div class="row g-4">
                                    <div class="col-md-3 col-sm-6">
                                        <div class="type-card-wrapper">
                                            <input type="radio" 
                                                   name="type_dossier" 
                                                   id="type_exoneration" 
                                                   value="exoneration"
                                                   class="type-radio"
                                                   {{ old('type_dossier', 'exoneration') == 'exoneration' ? 'checked' : '' }}
                                                   required>
                                            <label for="type_exoneration" class="type-label">
                                                <div class="card type-card">
                                                    <div class="card-body text-center p-4">
                                                        <div class="icon-wrapper mb-3">
                                                            <i class="fas fa-hand-holding-usd fa-2x text-primary"></i>
                                                        </div>
                                                        <h6 class="card-title fw-bold">Exonération fiscale</h6>
                                                        <p class="card-text small text-muted">
                                                            Demande de dispense totale ou partielle d'impôt
                                                        </p>
                                                    </div>
                                                    <div class="card-footer text-center bg-transparent">
                                                        <span class="badge bg-primary">
                                                            Sélectionner
                                                        </span>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3 col-sm-6">
                                        <div class="type-card-wrapper">
                                            <input type="radio" 
                                                   name="type_dossier" 
                                                   id="type_dispense_tva" 
                                                   value="dispense_tva"
                                                   class="type-radio"
                                                   {{ old('type_dossier') == 'dispense_tva' ? 'checked' : '' }}>
                                            <label for="type_dispense_tva" class="type-label">
                                                <div class="card type-card">
                                                    <div class="card-body text-center p-4">
                                                        <div class="icon-wrapper mb-3">
                                                            <i class="fas fa-percent fa-2x text-success"></i>
                                                        </div>
                                                        <h6 class="card-title fw-bold">Dispense de TVA</h6>
                                                        <p class="card-text small text-muted">
                                                            Demande d'exonération de Taxe sur la Valeur Ajoutée
                                                        </p>
                                                    </div>
                                                    <div class="card-footer text-center bg-transparent">
                                                        <span class="badge bg-success">
                                                            Sélectionner
                                                        </span>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3 col-sm-6">
                                        <div class="type-card-wrapper">
                                            <input type="radio" 
                                                   name="type_dossier" 
                                                   id="type_rejet" 
                                                   value="rejet"
                                                   class="type-radio"
                                                   {{ old('type_dossier') == 'rejet' ? 'checked' : '' }}>
                                            <label for="type_rejet" class="type-label">
                                                <div class="card type-card">
                                                    <div class="card-body text-center p-4">
                                                        <div class="icon-wrapper mb-3">
                                                            <i class="fas fa-times-circle fa-2x text-danger"></i>
                                                        </div>
                                                        <h6 class="card-title fw-bold">Proposition de rejet</h6>
                                                        <p class="card-text small text-muted">
                                                            Dossier de rejet d'une demande précédente
                                                        </p>
                                                    </div>
                                                    <div class="card-footer text-center bg-transparent">
                                                        <span class="badge bg-danger">
                                                            Sélectionner
                                                        </span>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3 col-sm-6">
                                        <div class="type-card-wrapper">
                                            <input type="radio" 
                                                   name="type_dossier" 
                                                   id="type_autre" 
                                                   value="autre"
                                                   class="type-radio"
                                                   {{ old('type_dossier') == 'autre' ? 'checked' : '' }}>
                                            <label for="type_autre" class="type-label">
                                                <div class="card type-card">
                                                    <div class="card-body text-center p-4">
                                                        <div class="icon-wrapper mb-3">
                                                            <i class="fas fa-file-alt fa-2x text-secondary"></i>
                                                        </div>
                                                        <h6 class="card-title fw-bold">Autre dossier</h6>
                                                        <p class="card-text small text-muted">
                                                            Autre type de demande fiscale
                                                        </p>
                                                    </div>
                                                    <div class="card-footer text-center bg-transparent">
                                                        <span class="badge bg-secondary">
                                                            Sélectionner
                                                        </span>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                @error('type_dossier')
                                <div class="alert alert-danger mt-3">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            
                            <!-- Section 2 : Informations du contribuable -->
                            <div class="form-section mb-5">
                                <h5 class="mb-4">
                                    <i class="fas fa-user-tie me-2"></i>
                                    2. Informations du contribuable
                                </h5>
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="contribuable_nom" class="form-label fw-bold">
                                            <i class="fas fa-building me-1"></i>
                                            Nom / Raison sociale *
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('contribuable_nom') is-invalid @enderror" 
                                               id="contribuable_nom" 
                                               name="contribuable_nom" 
                                               value="{{ old('contribuable_nom') }}" 
                                               placeholder="Entrez le nom ou la raison sociale"
                                               required>
                                        @error('contribuable_nom')
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-circle me-1"></i>
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="contribuable_id_fiscal" class="form-label fw-bold">
                                            <i class="fas fa-id-card me-1"></i>
                                            Identifiant fiscal *
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('contribuable_id_fiscal') is-invalid @enderror" 
                                               id="contribuable_id_fiscal" 
                                               name="contribuable_id_fiscal" 
                                               value="{{ old('contribuable_id_fiscal') }}"
                                               placeholder="Ex: IF123456789"
                                               required>
                                        @error('contribuable_id_fiscal')
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-circle me-1"></i>
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="secteur_activite" class="form-label fw-bold">
                                            <i class="fas fa-industry me-1"></i>
                                            Secteur d'activité
                                        </label>
                                        <select class="form-select @error('secteur_activite') is-invalid @enderror" 
                                                id="secteur_activite" 
                                                name="secteur_activite">
                                            <option value="">Sélectionnez un secteur...</option>
                                            <option value="industrie" {{ old('secteur_activite') == 'industrie' ? 'selected' : '' }}>Industrie</option>
                                            <option value="commerce" {{ old('secteur_activite') == 'commerce' ? 'selected' : '' }}>Commerce</option>
                                            <option value="services" {{ old('secteur_activite') == 'services' ? 'selected' : '' }}>Services</option>
                                            <option value="agriculture" {{ old('secteur_activite') == 'agriculture' ? 'selected' : '' }}>Agriculture</option>
                                            <option value="batiment" {{ old('secteur_activite') == 'batiment' ? 'selected' : '' }}>Bâtiment & Construction</option>
                                            <option value="transport" {{ old('secteur_activite') == 'transport' ? 'selected' : '' }}>Transport & Logistique</option>
                                            <option value="tourisme" {{ old('secteur_activite') == 'tourisme' ? 'selected' : '' }}>Tourisme & Hôtellerie</option>
                                            <option value="autre" {{ old('secteur_activite') == 'autre' ? 'selected' : '' }}>Autre</option>
                                        </select>
                                        @error('secteur_activite')
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-circle me-1"></i>
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="montant_impact" class="form-label fw-bold">
                                            <i class="fas fa-euro-sign me-1"></i>
                                            Montant concerné (€)
                                        </label>
                                        <div class="input-group">
                                            <input type="number" 
                                                   class="form-control @error('montant_impact') is-invalid @enderror" 
                                                   id="montant_impact" 
                                                   name="montant_impact" 
                                                   value="{{ old('montant_impact') }}" 
                                                   step="0.01" 
                                                   min="0"
                                                   placeholder="0.00">
                                            <span class="input-group-text bg-light">€</span>
                                            @error('montant_impact')
                                            <div class="invalid-feedback">
                                                <i class="fas fa-exclamation-circle me-1"></i>
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                        <small class="text-muted mt-1 d-block">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Montant estimé de l'impact fiscal
                                        </small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Section 3 : Détails de la demande -->
                            <div class="form-section mb-5">
                                <h5 class="mb-4">
                                    <i class="fas fa-edit me-2"></i>
                                    3. Détails de la demande
                                </h5>
                                
                                <div class="mb-4">
                                    <label for="sujet" class="form-label fw-bold">
                                        <i class="fas fa-heading me-1"></i>
                                        Objet de la demande *
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('sujet') is-invalid @enderror" 
                                           id="sujet" 
                                           name="sujet" 
                                           value="{{ old('sujet') }}" 
                                           placeholder="Ex: Demande d'exonération pour investissement en R&D"
                                           required>
                                    @error('sujet')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                
                                <div class="mb-4">
                                    <label for="description" class="form-label fw-bold">
                                        <i class="fas fa-align-left me-1"></i>
                                        Justification détaillée *
                                    </label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" 
                                              name="description" 
                                              rows="8"
                                              placeholder="Décrivez précisément la demande, les motifs, les références légales..."
                                              required>{{ old('description') }}</textarea>
                                    @error('description')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        {{ $message }}
                                    </div>
                                    @enderror
                                    <div class="d-flex justify-content-between mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-lightbulb me-1"></i>
                                            Soyez précis et citez les articles de loi applicables
                                        </small>
                                        <small class="text-muted" id="char-count">0 caractères</small>
                                    </div>
                                </div>
                                
                                <!-- Paramètres avancés (pour les administrateurs) -->
                                <div class="border-top pt-4 mt-4">
                                    <h6 class="fw-bold text-muted mb-3">
                                        <i class="fas fa-cogs me-2"></i>
                                        Paramètres avancés
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="date_limite" class="form-label fw-bold">
                                                <i class="fas fa-calendar-alt me-1"></i>
                                                Date limite de traitement
                                            </label>
                                            <input type="date" 
                                                   class="form-control @error('date_limite') is-invalid @enderror" 
                                                   id="date_limite" 
                                                   name="date_limite" 
                                                   value="{{ old('date_limite', date('Y-m-d', strtotime('+10 days'))) }}"
                                                   min="{{ date('Y-m-d') }}">
                                            @error('date_limite')
                                            <div class="invalid-feedback">
                                                <i class="fas fa-exclamation-circle me-1"></i>
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <label for="service_id" class="form-label fw-bold">
                                                <i class="fas fa-users me-1"></i>
                                                Service instructeur
                                            </label>
                                            <select class="form-select @error('service_id') is-invalid @enderror" 
                                                    id="service_id" 
                                                    name="service_id">
                                                <option value="">Sélectionnez un service...</option>
                                                <!-- Les services seront chargés dynamiquement -->
                                            </select>
                                            @error('service_id')
                                            <div class="invalid-feedback">
                                                <i class="fas fa-exclamation-circle me-1"></i>
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Section 4 : Pièces jointes -->
                            <div class="form-section mb-5">
                                <h5 class="mb-4">
                                    <i class="fas fa-paperclip me-2"></i>
                                    4. Pièces justificatives (optionnel)
                                </h5>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-upload me-1"></i>
                                        Documents à joindre
                                    </label>
                                    
                                    <div class="border rounded p-4 text-center mb-3" id="dropzone">
                                        <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">Cliquez pour sélectionner des fichiers</h5>
                                        <p class="text-muted mb-3">ou glissez-déposez vos fichiers ici</p>
                                        <input type="file" 
                                               name="pieces_jointes[]" 
                                               id="pieces_jointes" 
                                               class="form-control" 
                                               multiple 
                                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                        
                                        <div class="mt-3">
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Formats acceptés : PDF, DOC, DOCX, JPG, PNG (Max 10MB par fichier)
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Boutons d'action -->
                            <div class="d-flex justify-content-between mt-5 pt-4 border-top">
                                <div>
                                    <a href="{{ route('courriers.index') }}" class="btn btn-outline-secondary btn-lg">
                                        <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                                    </a>
                                </div>
                                <div class="d-flex gap-3">
                                    <button type="reset" class="btn btn-outline-danger btn-lg">
                                        <i class="fas fa-eraser me-2"></i>Effacer
                                    </button>
                                    <button type="submit" class="btn btn-primary btn-lg" id="btn-submit">
                                        <i class="fas fa-save me-2"></i>Créer le dossier fiscal
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Compteur de caractères pour la description
            const descriptionTextarea = document.getElementById('description');
            const charCount = document.getElementById('char-count');
            
            if (descriptionTextarea && charCount) {
                descriptionTextarea.addEventListener('input', function() {
                    charCount.textContent = this.value.length + ' caractères';
                });
                
                // Initialiser le compteur
                charCount.textContent = descriptionTextarea.value.length + ' caractères';
            }
            
            // Validation du formulaire
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const typeSelected = document.querySelector('input[name="type_dossier"]:checked');
                    if (!typeSelected) {
                        e.preventDefault();
                        alert('Veuillez sélectionner un type de demande.');
                        return false;
                    }
                    
                    // Afficher un indicateur de chargement
                    const submitBtn = document.getElementById('btn-submit');
                    if (submitBtn) {
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Traitement en cours...';
                        submitBtn.disabled = true;
                    }
                });
            }
            
            // Gestion du dropzone visuel
            const dropzone = document.getElementById('dropzone');
            const fileInput = document.getElementById('pieces_jointes');
            
            if (dropzone && fileInput) {
                dropzone.addEventListener('click', function() {
                    fileInput.click();
                });
                
                dropzone.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    dropzone.style.backgroundColor = '#e7f3ff';
                    dropzone.style.borderColor = '#0d6efd';
                });
                
                dropzone.addEventListener('dragleave', function() {
                    dropzone.style.backgroundColor = '';
                    dropzone.style.borderColor = '';
                });
                
                dropzone.addEventListener('drop', function(e) {
                    e.preventDefault();
                    dropzone.style.backgroundColor = '';
                    dropzone.style.borderColor = '';
                    
                    if (e.dataTransfer.files.length > 0) {
                        fileInput.files = e.dataTransfer.files;
                        // Afficher un message
                        alert(e.dataTransfer.files.length + ' fichier(s) sélectionné(s)');
                    }
                });
            }
        });
    </script>
</body>
</html>