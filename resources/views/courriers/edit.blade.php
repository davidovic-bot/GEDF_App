@extends('layouts.gdf')

@section('title', 'Modifier un courrier')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3">Modifier un courrier</h1>
            <p class="text-muted">Référence : {{ $courrier->reference ?? $courrier->numero }}</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('courriers.update', $courrier->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Bénéficiaire *</label>
                        <input type="text" name="beneficiaire" class="form-control" value="{{ old('beneficiaire', $courrier->beneficiaire) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">NIF</label>
                        <input type="text" name="nif" class="form-control" value="{{ old('nif', $courrier->nif) }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Objet *</label>
                    <input type="text" name="objet" class="form-control" value="{{ old('objet', $courrier->objet) }}" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Type de demande *</label>
                        <select name="type_demande" class="form-select" required>
                            <option value="exoneration_tva" {{ $courrier->type_demande == 'exoneration_tva' ? 'selected' : '' }}>Exonération de TVA</option>
                            <option value="dispense_tva" {{ $courrier->type_demande == 'dispense_tva' ? 'selected' : '' }}>Dispense de TVA</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Service concerné *</label>
                        <select name="service_emetteur_id" class="form-select" required>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}" {{ $courrier->service_emetteur_id == $service->id ? 'selected' : '' }}>
                                    {{ $service->nom }} ({{ $service->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date de réception *</label>
                        <input type="date" name="date_reception" class="form-control" value="{{ old('date_reception', $courrier->date_reception) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Motif (optionnel)</label>
                        <textarea name="motif" class="form-control" rows="2">{{ old('motif', $courrier->motif) }}</textarea>
                    </div>
                </div>

                <div class="text-end">
                    <a href="{{ route('courriers.show', $courrier->id) }}" class="btn btn-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection