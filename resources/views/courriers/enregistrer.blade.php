@extends('layouts.app')

@section('title', 'Enregistrement d\'un courrier reçu')

@section('content')
<div class="card">
    <div class="card-header bg-primary text-white">
        <h5><i class="fas fa-inbox"></i> Enregistrement d'un courrier physique reçu</h5>
    </div>
    
    <div class="card-body">
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> 
            Ce formulaire sert à créer une trace numérique d'un courrier physique 
            reçu par le service. Veuillez remplir les informations du courrier 
            papier et y associer son scan.
        </div>
        
        <form action="{{ route('courriers.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <!-- Informations du courrier physique -->
                <div class="col-md-6">
                    <h6 class="border-bottom pb-2">Informations du courrier physique</h6>
                    
                    <div class="mb-3">
                        <label class="form-label">Numéro du courrier physique *</label>
                        <input type="text" name="numero_courrier_physique" class="form-control" 
                               placeholder="Ex: CA/2024/001 ou numéro de récépissé" required>
                        <small class="text-muted">Numéro attribué au courrier papier</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Date de réception physique *</label>
                        <input type="date" name="date_reception" class="form-control" 
                               value="{{ date('Y-m-d') }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Expéditeur *</label>
                        <input type="text" name="expediteur" class="form-control" 
                               placeholder="Nom de l'entreprise ou du contribuable" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Objet du courrier *</label>
                        <textarea name="objet" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                
                <!-- Traitement numérique -->
                <div class="col-md-6">
                    <h6 class="border-bottom pb-2">Traitement numérique</h6>
                    
                    <div class="mb-3">
                        <label class="form-label">Type de demande *</label>
                        <select name="type_demande" class="form-control" required>
                            <option value="">-- Sélectionner --</option>
                            <option value="exoneration">Demande d'exonération</option>
                            <option value="dispense_tva">Demande de dispense de TVA</option>
                            <option value="autre">Autre demande</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Service destinataire *</label>
                        <select name="service_destinataire_id" class="form-control" required>
                            <option value="">-- Sélectionner le service --</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}">{{ $service->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Scan du courrier *</label>
                        <input type="file" name="document_scan" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                        <small class="text-muted">Formats acceptés: PDF, JPG, PNG (max 5MB)</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Notes complémentaires</label>
                        <textarea name="notes" class="form-control" rows="3" 
                                  placeholder="Observations, particularités..."></textarea>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="urgent" class="form-check-input" id="urgent">
                        <label class="form-check-label" for="urgent">
                            <i class="fas fa-exclamation-triangle text-danger"></i> Marquer comme urgent
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Enregistrer la trace numérique
                </button>
                <a href="{{ route('courriers.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Annuler
                </a>
            </div>
        </form>
    </div>
</div>
@endsection