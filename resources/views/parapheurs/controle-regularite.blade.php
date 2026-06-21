@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">✅ Contrôle de régularité</h3>
                    <div class="card-tools">
                        <a href="{{ route('parapheurs.show', $parapheur) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Récapitulatif -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box bg-info">
                                <span class="info-box-icon"><i class="fas fa-file-invoice"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">💰 TVA non perçue</span>
                                    <span class="info-box-number">{{ $parapheur->montant_tva_formate ?? '0 FCFA' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box bg-success">
                                <span class="info-box-icon"><i class="fas fa-calculator"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">📊 Total Dépense fiscale</span>
                                    <span class="info-box-number">{{ $parapheur->montant_total_formate ?? '0 FCFA' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-secondary">
                        <strong>🔍 Vérifié par :</strong> {{ $parapheur->verifieParNom ?? 'Non vérifié' }} |
                        <strong>📅 Date :</strong> {{ $parapheur->verifie_le ? \Carbon\Carbon::parse($parapheur->verifie_le)->format('d/m/Y H:i') : 'N/A' }}
                    </div>

                    <hr>

                    <form action="{{ route('parapheurs.valider-controle', $parapheur) }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="commentaire">📝 Avis du Chef de Service</label>
                            <textarea class="form-control @error('commentaire') is-invalid @enderror" 
                                      id="commentaire" name="commentaire" rows="3"
                                      placeholder="Confirmez la régularité des factures..."></textarea>
                            @error('commentaire')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check"></i> Valider le contrôle
                            </button>
                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#rejetModal">
                                <i class="fas fa-times"></i> Rejeter
                            </button>
                            <a href="{{ route('parapheurs.show', $parapheur) }}" class="btn btn-secondary">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modale de rejet -->
<div class="modal fade" id="rejetModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('parapheurs.rejeter-pieces', $parapheur) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">❌ Rejeter les pièces</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="motif">Motif du rejet <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="motif" name="motif" rows="3" required 
                                  placeholder="Expliquez pourquoi le contrôle est rejeté..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">Confirmer le rejet</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection