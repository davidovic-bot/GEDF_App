@extends('layouts.gdf')

@section('title', 'Enregistrer un courrier')

@section('content')

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3">Enregistrement d’une demande</h1>
            <p class="text-muted">Saisie des informations du contribuable</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('courriers.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Bénéficiaire (nom / raison sociale) *</label>
                        <input type="text" name="beneficiaire" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">NIF (si connu)</label>
                        <input type="text" name="nif" class="form-control">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Objet de la demande *</label>
                    <input type="text" name="objet" class="form-control" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Type de demande *</label>
                        <select name="type_demande" class="form-select" required>
                            <option value="">Sélectionner</option>
                            <option value="exoneration_tva">Exonération de TVA</option>
                            <option value="dispense_tva">Dispense de TVA</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
    <label class="form-label">Secteur d’activité *</label>
    <select name="secteur" id="secteur" class="form-select" required>
        <option value="">Sélectionner un secteur</option>
        <option value="mines_petrole_foret">Mines, pétrole, forêt</option>
        <option value="agriculture">Agriculture, élevage</option>
        <option value="tourisme">Tourisme</option>
        <option value="transport">Transport</option>
        <option value="zes">Zone économique spéciale (ZES)</option>
        <option value="association">Association, ONG</option>
        <option value="autre">Autre (préciser)</option>
    </select>
</div>
      <div class="col-md-6 mb-3">
    <label class="form-label">Précision (si autre)</label>
    <input type="text" name="secteur_autre" class="form-control">
</div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Service concerné *</label>
                        <select name="service_emetteur_id" class="form-select" required>
                            <option value="">Sélectionner un service</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}">{{ $service->nom }} ({{ $service->code }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Date de réception *</label>
                    <input type="date" name="date_reception" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Courrier scanné (PDF)</label>
                    <input type="file" name="fichier" class="form-control" accept="application/pdf">
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Enregistrer la demande</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection