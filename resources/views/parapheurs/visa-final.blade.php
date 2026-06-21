@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">✍️ Visa final - Direction</h3>
                    <div class="card-tools">
                        <a href="{{ route('parapheurs.show', $parapheur) }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Récapitulatif complet -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-file"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Référence</span>
                                    <span class="info-box-number">{{ $parapheur->reference }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-money-bill"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">TVA</span>
                                    <span class="info-box-number">{{ $parapheur->montant_tva_formate ?? '0 FCFA' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-calculator"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total</span>
                                    <span class="info-box-number">{{ $parapheur->montant_total_formate ?? '0 FCFA' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-secondary"><i class="fas fa-user-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Contrôlé par</span>
                                    <span class="info-box-number">{{ $parapheur->controleParNom ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-success mt-3">
                        <i class="fas fa-check-circle"></i>
                        <strong>Dossier prêt pour le visa final</strong>
                        <br>
                        Toutes les vérifications et contrôles ont été effectués.
                    </div>

                    <hr>

                    <form action="{{ route('parapheurs.apposer-visa', $parapheur) }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="commentaire">📝 Commentaire du Directeur</label>
                            <textarea class="form-control @error('commentaire') is-invalid @enderror" 
                                      id="commentaire" name="commentaire" rows="3"
                                      placeholder="Approbation finale, observations éventuelles..."></textarea>
                            @error('commentaire')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-signature"></i> Apposer le visa final
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
                    <h5 class="modal-title">❌ Rejeter définitivement</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        Cette action est irréversible. Le dossier devra être repris depuis le début.
                    </div>
                    <div class="form-group">
                        <label for="motif">Motif du rejet <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="motif" name="motif" rows="3" required 
                                  placeholder="Expliquez pourquoi le visa final est refusé..."></textarea>
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