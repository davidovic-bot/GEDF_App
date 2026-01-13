<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier {{ $courrier->reference }} - GDF</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
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
            <a class="navbar-brand" href="{{ route('courriers.show', $courrier) }}">
                <i class="fas fa-arrow-left me-2"></i>
                Modifier {{ $courrier->reference }}
            </a>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h2 class="h4 mb-0">
                            <i class="fas fa-edit text-primary me-2"></i>
                            Modification du dossier {{ $courrier->reference }}
                        </h2>
                        <p class="text-muted mb-0 mt-2">
                            Modifiez les informations du dossier fiscal.
                        </p>
                    </div>
                    
                    <div class="card-body">
                        <form method="POST" action="{{ route('courriers.update', $courrier) }}">
                            @csrf
                            @method('PUT')
                            
                            <!-- Section 1 : Informations du contribuable -->
                            <div class="form-section mb-5">
                                <h5 class="mb-4">
                                    <i class="fas fa-user-tie me-2"></i>
                                    1. Informations du contribuable
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
                                               value="{{ old('contribuable_nom', $courrier->contribuable_nom) }}" 
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
                                               value="{{ old('contribuable_id_fiscal', $courrier->contribuable_id_fiscal) }}"
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
                                            <option value="industrie" {{ (old('secteur_activite', $courrier->secteur_activite) == 'industrie') ? 'selected' : '' }}>Industrie</option>
                                            <option value="commerce" {{ (old('secteur_activite', $courrier->secteur_activite) == 'commerce') ? 'selected' : '' }}>Commerce</option>
                                            <option value="services" {{ (old('secteur_activite', $courrier->secteur_activite) == 'services') ? 'selected' : '' }}>Services</option>
                                            <option value="agriculture" {{ (old('secteur_activite', $courrier->secteur_activite) == 'agriculture') ? 'selected' : '' }}>Agriculture</option>
                                            <option value="batiment" {{ (old('secteur_activite', $courrier->secteur_activite) == 'batiment') ? 'selected' : '' }}>Bâtiment & Construction</option>
                                            <option value="transport" {{ (old('secteur_activite', $courrier->secteur_activite) == 'transport') ? 'selected' : '' }}>Transport & Logistique</option>
                                            <option value="tourisme" {{ (old('secteur_activite', $courrier->secteur_activite) == 'tourisme') ? 'selected' : '' }}>Tourisme & Hôtellerie</option>
                                            <option value="autre" {{ (old('secteur_activite', $courrier->secteur_activite) == 'autre') ? 'selected' : '' }}>Autre</option>
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
                                                   value="{{ old('montant_impact', $courrier->montant_impact) }}" 
                                                   step="0.01" 
                                                   min="0">
                                            <span class="input-group-text bg-light">€</span>
                                            @error('montant_impact')
                                            <div class="invalid-feedback">
                                                <i class="fas fa-exclamation-circle me-1"></i>
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Section 2 : Détails de la demande -->
                            <div class="form-section mb-5">
                                <h5 class="mb-4">
                                    <i class="fas fa-edit me-2"></i>
                                    2. Détails de la demande
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
                                           value="{{ old('sujet', $courrier->sujet) }}" 
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
                                              required>{{ old('description', $courrier->description) }}</textarea>
                                    @error('description')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Section 3 : Gestion du dossier -->
                            <div class="form-section mb-5">
                                <h5 class="mb-4">
                                    <i class="fas fa-cogs me-2"></i>
                                    3. Gestion du dossier
                                </h5>
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="type_dossier" class="form-label fw-bold">
                                            <i class="fas fa-tag me-1"></i>
                                            Type de dossier *
                                        </label>
                                        <select class="form-select @error('type_dossier') is-invalid @enderror" 
                                                id="type_dossier" 
                                                name="type_dossier"
                                                required>
                                            <option value="exoneration" {{ (old('type_dossier', $courrier->type_dossier) == 'exoneration') ? 'selected' : '' }}>Exonération fiscale</option>
                                            <option value="dispense_tva" {{ (old('type_dossier', $courrier->type_dossier) == 'dispense_tva') ? 'selected' : '' }}>Dispense de TVA</option>
                                            <option value="rejet" {{ (old('type_dossier', $courrier->type_dossier) == 'rejet') ? 'selected' : '' }}>Proposition de rejet</option>
                                            <option value="autre" {{ (old('type_dossier', $courrier->type_dossier) == 'autre') ? 'selected' : '' }}>Autre dossier</option>
                                        </select>
                                        @error('type_dossier')
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-circle me-1"></i>
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="statut" class="form-label fw-bold">
                                            <i class="fas fa-flag me-1"></i>
                                            Statut *
                                        </label>
                                        <select class="form-select @error('statut') is-invalid @enderror" 
                                                id="statut" 
                                                name="statut"
                                                required>
                                            <option value="en_analyse" {{ (old('statut', $courrier->statut) == 'en_analyse') ? 'selected' : '' }}>En analyse</option>
                                            <option value="en_validation" {{ (old('statut', $courrier->statut) == 'en_validation') ? 'selected' : '' }}>En validation</option>
                                            <option value="signe" {{ (old('statut', $courrier->statut) == 'signe') ? 'selected' : '' }}>Signé</option>
                                            <option value="archive" {{ (old('statut', $courrier->statut) == 'archive') ? 'selected' : '' }}>Archivé</option>
                                        </select>
                                        @error('statut')
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-circle me-1"></i>
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="date_limite" class="form-label fw-bold">
                                            <i class="fas fa-calendar-alt me-1"></i>
                                            Date limite de traitement
                                        </label>
                                        <input type="date" 
                                               class="form-control @error('date_limite') is-invalid @enderror" 
                                               id="date_limite" 
                                               name="date_limite" 
                                               value="{{ old('date_limite', $courrier->date_limite ? \Carbon\Carbon::parse($courrier->date_limite)->format('Y-m-d') : '') }}">
                                        @error('date_limite')
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-circle me-1"></i>
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="motif_rejet" class="form-label fw-bold">
                                            <i class="fas fa-comment me-1"></i>
                                            Motif de rejet (si applicable)
                                        </label>
                                        <textarea class="form-control @error('motif_rejet') is-invalid @enderror" 
                                                  id="motif_rejet" 
                                                  name="motif_rejet" 
                                                  rows="2">{{ old('motif_rejet', $courrier->motif_rejet) }}</textarea>
                                        @error('motif_rejet')
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-circle me-1"></i>
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Boutons d'action -->
                            <div class="d-flex justify-content-between mt-5 pt-4 border-top">
                                <div>
                                    <a href="{{ route('courriers.show', $courrier) }}" class="btn btn-outline-secondary btn-lg">
                                        <i class="fas fa-times me-2"></i>Annuler
                                    </a>
                                </div>
                                <div class="d-flex gap-3">
                                    <button type="reset" class="btn btn-outline-danger btn-lg">
                                        <i class="fas fa-eraser me-2"></i>Réinitialiser
                                    </button>
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save me-2"></i>Enregistrer les modifications
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
</body>
</html>