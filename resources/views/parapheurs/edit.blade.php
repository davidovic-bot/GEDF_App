@extends('layouts.app')

@section('title', 'Modifier Parapheur - GDF')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <div class="row mb-3">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Tableau de bord</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('parapheurs.index') }}">Parapheurs</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('parapheurs.show', $parapheur) }}">{{ $parapheur->reference }}</a></li>
                    <li class="breadcrumb-item active">Modification</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 text-gray-800">
                    <i class="fas fa-edit text-primary mr-2"></i>Modifier le parapheur
                </h1>
                <div>
                    <a href="{{ route('parapheurs.show', $parapheur) }}" class="btn btn-outline-secondary mr-2">
                        <i class="fas fa-times mr-1"></i> Annuler
                    </a>
                    <button type="submit" form="editForm" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Enregistrer
                    </button>
                </div>
            </div>
            <p class="text-muted">Référence: {{ $parapheur->reference }} • Statut: {{ $parapheur->statut }}</p>
        </div>
    </div>

    <!-- Alerte statut -->
    @if(!in_array($parapheur->statut, ['brouillon', 'en_attente']))
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>Attention :</strong> Ce parapheur est en statut "{{ $parapheur->statut }}". 
                Seules certaines modifications sont autorisées.
            </div>
        </div>
    </div>
    @endif

    <!-- Formulaire -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <form id="editForm" action="{{ route('parapheurs.update', $parapheur) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Colonne gauche -->
                            <div class="col-md-6">
                                <!-- Objet -->
                                <div class="form-group">
                                    <label for="objet" class="font-weight-bold">Objet *</label>
                                    <input type="text" 
                                           class="form-control @error('objet') is-invalid @enderror" 
                                           id="objet" 
                                           name="objet" 
                                           value="{{ old('objet', $parapheur->objet) }}"
                                           required
                                           {{ !in_array($parapheur->statut, ['brouillon', 'en_attente']) ? 'readonly' : '' }}>
                                    @error('objet')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Description -->
                                <div class="form-group">
                                    <label for="description" class="font-weight-bold">Description *</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" 
                                              name="description" 
                                              rows="6"
                                              required
                                              {{ !in_array($parapheur->statut, ['brouillon', 'en_attente']) ? 'readonly' : '' }}>{{ old('description', $parapheur->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Colonne droite -->
                            <div class="col-md-6">
                                <!-- Service -->
                                <div class="form-group">
                                    <label for="service_id" class="font-weight-bold">Service *</label>
                                    <select class="form-control @error('service_id') is-invalid @enderror" 
                                            id="service_id" 
                                            name="service_id" 
                                            required
                                            {{ !in_array($parapheur->statut, ['brouillon', 'en_attente']) ? 'disabled' : '' }}>
                                        <option value="">Sélectionnez un service</option>
                                        @foreach($services as $service)
                                        <option value="{{ $service->id }}" 
                                                {{ old('service_id', $parapheur->service_id) == $service->id ? 'selected' : '' }}>
                                            {{ $service->nom }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('service_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Direction -->
                                <div class="form-group">
                                    <label for="direction_id" class="font-weight-bold">Direction *</label>
                                    <select class="form-control @error('direction_id') is-invalid @enderror" 
                                            id="direction_id" 
                                            name="direction_id" 
                                            required
                                            {{ !in_array($parapheur->statut, ['brouillon', 'en_attente']) ? 'disabled' : '' }}>
                                        <option value="">Sélectionnez une direction</option>
                                        @foreach($directions as $direction)
                                        <option value="{{ $direction->id }}" 
                                                {{ old('direction_id', $parapheur->direction_id) == $direction->id ? 'selected' : '' }}>
                                            {{ $direction->nom }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('direction_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Priorité -->
                                <div class="form-group">
                                    <label class="font-weight-bold">Priorité *</label>
                                    <div class="row">
                                        @foreach(['basse' => 'Basse', 'normale' => 'Normale', 'haute' => 'Haute', 'urgente' => 'Urgente'] as $value => $label)
                                        <div class="col-6 mb-2">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" 
                                                       id="priorite_{{ $value }}" 
                                                       name="priorite" 
                                                       value="{{ $value }}"
                                                       class="custom-control-input @error('priorite') is-invalid @enderror"
                                                       {{ old('priorite', $parapheur->priorite) == $value ? 'checked' : '' }}
                                                       required
                                                       {{ !in_array($parapheur->statut, ['brouillon', 'en_attente']) ? 'disabled' : '' }}>
                                                <label class="custom-control-label" for="priorite_{{ $value }}">
                                                    @if($value == 'urgente')
                                                    <span class="text-danger">{{ $label }}</span>
                                                    @elseif($value == 'haute')
                                                    <span class="text-warning">{{ $label }}</span>
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

                                <!-- Date échéance -->
                                <div class="form-group">
                                    <label for="date_echeance" class="font-weight-bold">Date d'échéance *</label>
                                    <input type="date" 
                                           class="form-control @error('date_echeance') is-invalid @enderror" 
                                           id="date_echeance" 
                                           name="date_echeance" 
                                           value="{{ old('date_echeance', $parapheur->date_echeance ? \Carbon\Carbon::parse($parapheur->date_echeance)->format('Y-m-d') : '') }}"
                                           min="{{ \Carbon\Carbon::now()->addDay()->format('Y-m-d') }}"
                                           required
                                           {{ !in_array($parapheur->statut, ['brouillon', 'en_attente']) ? 'readonly' : '' }}>
                                    @error('date_echeance')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Confidentialité -->
                                <div class="form-group">
                                    <label for="confidentialite" class="font-weight-bold">Confidentialité</label>
                                    <select class="form-control @error('confidentialite') is-invalid @enderror" 
                                            id="confidentialite" 
                                            name="confidentialite"
                                            {{ !in_array($parapheur->statut, ['brouillon', 'en_attente']) ? 'disabled' : '' }}>
                                        <option value="standard" {{ old('confidentialite', $parapheur->confidentialite) == 'standard' ? 'selected' : '' }}>
                                            Standard
                                        </option>
                                        <option value="confidentiel" {{ old('confidentialite', $parapheur->confidentialite) == 'confidentiel' ? 'selected' : '' }}>
                                            Confidentiel
                                        </option>
                                        <option value="tres_confidentiel" {{ old('confidentialite', $parapheur->confidentialite) == 'tres_confidentiel' ? 'selected' : '' }}>
                                            Très confidentiel
                                        </option>
                                    </select>
                                    @error('confidentialite')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Notes internes (toujours modifiables) -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="notes_internes" class="font-weight-bold">Notes internes</label>
                                    <textarea class="form-control @error('notes_internes') is-invalid @enderror" 
                                              id="notes_internes" 
                                              name="notes_internes" 
                                              rows="3"
                                              placeholder="Notes à usage interne...">{{ old('notes_internes', $parapheur->notes_internes) }}</textarea>
                                    @error('notes_internes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Ces notes sont visibles uniquement par les utilisateurs ayant accès à ce parapheur.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <a href="{{ route('parapheurs.show', $parapheur) }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-times mr-1"></i> Annuler
                                        </a>
                                        
                                        @if(in_array($parapheur->statut, ['brouillon', 'en_attente']))
                                        <button type="button" class="btn btn-outline-danger ml-2" 
                                                data-toggle="modal" data-target="#deleteModal">
                                            <i class="fas fa-trash mr-1"></i> Supprimer
                                        </button>
                                        @endif
                                    </div>
                                    
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save mr-1"></i> Enregistrer les modifications
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
                <h6><i class="fas fa-info-circle mr-2"></i>Informations</h6>
                <ul class="mb-0">
                    <li>Les champs marqués d'une * sont obligatoires</li>
                    <li>La référence ({{ $parapheur->reference }}) ne peut pas être modifiée</li>
                    <li>Le statut actuel est "{{ $parapheur->statut }}"</li>
                    <li>Créé le {{ $parapheur->created_at->format('d/m/Y à H:i') }}</li>
                    @if($parapheur->updated_at != $parapheur->created_at)
                    <li>Dernière modification le {{ $parapheur->updated_at->format('d/m/Y à H:i') }}</li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Modal de suppression -->
@if(in_array($parapheur->statut, ['brouillon', 'en_attente']))
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer ce parapheur ?</p>
                <p class="text-danger">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Cette action est irréversible.
                </p>
                <p><strong>Référence :</strong> {{ $parapheur->reference }}</p>
                <p><strong>Objet :</strong> {{ $parapheur->objet }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <form action="{{ route('parapheurs.destroy', $parapheur) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash mr-1"></i> Supprimer définitivement
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Désactiver les champs en lecture seule
        const disabledFields = document.querySelectorAll('[disabled], [readonly]');
        disabledFields.forEach(field => {
            if (field.hasAttribute('disabled')) {
                field.setAttribute('data-original-disabled', 'true');
            }
            if (field.hasAttribute('readonly')) {
                field.setAttribute('data-original-readonly', 'true');
            }
        });

        // Validation de la date
        const dateEcheance = document.getElementById('date_echeance');
        if (dateEcheance && !dateEcheance.hasAttribute('readonly')) {
            dateEcheance.addEventListener('change', function() {
                const selectedDate = new Date(this.value);
                const tomorrow = new Date();
                tomorrow.setDate(tomorrow.getDate() + 1);
                
                if (selectedDate < tomorrow) {
                    alert('La date d\'échéance doit être au moins demain.');
                    this.value = tomorrow.toISOString().split('T')[0];
                }
            });
        }

        // Confirmation avant soumission
        const form = document.getElementById('editForm');
        form.addEventListener('submit', function(e) {
            const changedFields = Array.from(this.elements).filter(el => 
                el.defaultValue !== undefined && 
                el.value !== el.defaultValue &&
                !el.hasAttribute('data-original-disabled') &&
                !el.hasAttribute('data-original-readonly')
            );
            
            if (changedFields.length === 0) {
                e.preventDefault();
                alert('Aucune modification détectée.');
                return false;
            }
            
            return true;
        });
    });
</script>
@endpush