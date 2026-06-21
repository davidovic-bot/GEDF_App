@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">🔍 Vérification des factures</h3>
                    <div class="card-tools">
                        <a href="{{ route('parapheurs.show', $parapheur) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Informations -->
                    <div class="alert alert-info">
                        <strong>Parapheur :</strong> {{ $parapheur->reference }} |
                        <strong>Objet :</strong> {{ $parapheur->objet }} |
                        <strong>Expéditeur :</strong> {{ $parapheur->expediteur }}
                    </div>

                    <!-- Liste des factures -->
                    <h5 class="mt-3">📑 Factures à vérifier</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nom du fichier</th>
                                    <th>Taille</th>
                                    <th>Date d'upload</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($factures as $index => $facture)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $facture['nom'] ?? 'N/A' }}</td>
                                    <td>{{ isset($facture['taille']) ? round($facture['taille'] / 1024, 2) . ' KB' : 'N/A' }}</td>
                                    <td>{{ isset($facture['upload_le']) ? \Carbon\Carbon::parse($facture['upload_le'])->format('d/m/Y H:i') : 'N/A' }}</td>
                                    <td>
                                        <a href="{{ asset('storage/' . $facture['chemin']) }}" target="_blank" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Voir
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Aucune facture trouvée</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Formulaire de validation -->
                    @if(count($factures) > 0)
                    <form action="{{ route('parapheurs.valider-factures', $parapheur) }}" method="POST" class="mt-4">
                        @csrf

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="montant_tva">Montant TVA non perçue <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control @error('montant_tva') is-invalid @enderror" 
                                               id="montant_tva" name="montant_tva" step="1" min="0" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">FCFA</span>
                                        </div>
                                    </div>
                                    @error('montant_tva')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="montant_css">Montant CSS non perçue</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control @error('montant_css') is-invalid @enderror" 
                                               id="montant_css" name="montant_css" step="1" min="0">
                                        <div class="input-group-append">
                                            <span class="input-group-text">FCFA</span>
                                        </div>
                                    </div>
                                    @error('montant_css')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Total estimé</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="total_estime" readonly>
                                        <div class="input-group-append">
                                            <span class="input-group-text">FCFA</span>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">Calculé automatiquement</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="commentaire">Commentaire</label>
                                    <textarea class="form-control @error('commentaire') is-invalid @enderror" 
                                              id="commentaire" name="commentaire" rows="2" 
                                              placeholder="Observations sur les factures..."></textarea>
                                    @error('commentaire')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-12">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check"></i> Valider les factures
                                </button>
                                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#rejetModal">
                                    <i class="fas fa-times"></i> Rejeter
                                </button>
                                <a href="{{ route('parapheurs.show', $parapheur) }}" class="btn btn-secondary">Annuler</a>
                            </div>
                        </div>
                    </form>
                    @endif
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
                                  placeholder="Expliquez pourquoi les pièces sont rejetées..."></textarea>
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

@push('scripts')
<script>
    // Calcul automatique du total
    document.getElementById('montant_tva').addEventListener('input', calculerTotal);
    document.getElementById('montant_css').addEventListener('input', calculerTotal);

    function calculerTotal() {
        var tva = parseInt(document.getElementById('montant_tva').value) || 0;
        var css = parseInt(document.getElementById('montant_css').value) || 0;
        document.getElementById('total_estime').value = (tva + css).toLocaleString();
    }
</script>
@endpush