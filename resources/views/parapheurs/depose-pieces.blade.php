@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">📤 Dépôt des pièces justificatives</h3>
                    <div class="card-tools">
                        <a href="{{ route('parapheurs.show', $parapheur) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Informations du parapheur -->
                    <div class="alert alert-info">
                        <strong>Parapheur :</strong> {{ $parapheur->reference }} |
                        <strong>Objet :</strong> {{ $parapheur->objet }} |
                        <strong>Expéditeur :</strong> {{ $parapheur->expediteur }}
                    </div>

                    @if($parapheur->courrier)
                    <div class="alert alert-secondary">
                        <strong>Courrier associé :</strong> {{ $parapheur->courrier->numero ?? 'N/A' }} |
                        <strong>Bénéficiaire :</strong> {{ $parapheur->courrier->beneficiaire ?? 'N/A' }} |
                        <strong>Statut :</strong> 
                        <span class="badge badge-success">{{ $parapheur->courrier->statut_general ?? 'N/A' }}</span>
                    </div>
                    @endif

                    <form action="{{ route('parapheurs.store-pieces', $parapheur) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <!-- Tableau récapitulatif -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tableau">📄 Tableau récapitulatif <span class="text-danger">*</span></label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input @error('tableau') is-invalid @enderror" 
                                               id="tableau" name="tableau" accept=".pdf" required>
                                        <label class="custom-file-label" for="tableau">Choisir un fichier PDF</label>
                                    </div>
                                    <small class="form-text text-muted">Format accepté : PDF (max 10 Mo)</small>
                                    @error('tableau')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Factures -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="factures">📑 Factures <span class="text-danger">*</span></label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input @error('factures.*') is-invalid @enderror" 
                                               id="factures" name="factures[]" accept=".pdf" multiple required>
                                        <label class="custom-file-label" for="factures">Choisir les factures (PDF)</label>
                                    </div>
                                    <small class="form-text text-muted">Vous pouvez sélectionner plusieurs fichiers PDF (max 10 Mo chacun)</small>
                                    @error('factures.*')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-upload"></i> Déposer les pièces
                                </button>
                                <a href="{{ route('parapheurs.show', $parapheur) }}" class="btn btn-secondary">Annuler</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Mise à jour du nom du fichier pour les inputs type file
    document.querySelectorAll('.custom-file-input').forEach(function(input) {
        input.addEventListener('change', function(e) {
            var label = this.nextElementSibling;
            var files = this.files;
            if (files.length === 1) {
                label.innerHTML = files[0].name;
            } else if (files.length > 1) {
                label.innerHTML = files.length + ' fichiers sélectionnés';
            } else {
                label.innerHTML = 'Choisir un fichier';
            }
        });
    });
</script>
@endpush