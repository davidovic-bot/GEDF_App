@extends('layouts.gdf')

@section('title', 'Instruction DRS')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3">Instruction de la demande</h1>
            <p class="text-muted">Référence : {{ $courrier->reference ?? $courrier->numero }}</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('courriers.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Qualification DRS</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('courriers.instruction.store', $courrier->id) }}" method="POST">
                        @csrf

                        {{-- 1. OBJET DE LA DEMANDE --}}
                        <div class="mb-3">
                            <label class="form-label">Objet de la demande *</label>
                            <input type="text" name="objet" class="form-control" value="{{ old('objet') }}" required>
                        </div>

                        {{-- 2. TYPE DE DEMANDE --}}
                        <div class="mb-3">
                            <label class="form-label">Type de demande *</label>
                            <select name="type_demande" class="form-select" required>
                                <option value="">Sélectionner</option>
                                <option value="exoneration_tva">Exonération de TVA</option>
                                <option value="dispense_tva">Dispense de TVA</option>
                            </select>
                        </div>

                        {{-- 3. SECTEUR D’ACTIVITÉ --}}
                        <div class="mb-3">
                            <label class="form-label">Secteur d’activité *</label>
                            <select name="secteur" id="secteur" class="form-select" required>
                                <option value="">Sélectionner un secteur</option>
                                <option value="ifd">Agriculture, tourisme, transport, association (IFD)</option>
                                <option value="gfmpf">Mines, pétrole, forêts (GFMPF)</option>
                                <option value="zes">Zone économique spéciale (ZES)</option>
                            </select>
                        </div>

                        {{-- 4. SERVICE (auto-rempli) --}}
                        <div class="mb-3">
                            <label class="form-label">Service concerné *</label>
                            <select name="service_emetteur_id" id="service_emetteur_id" class="form-select" required>
                                <option value="">Sélectionner un service</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}">{{ $service->nom }} ({{ $service->code }})</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 5. INSTRUCTION / ANNOTATION --}}
                        <div class="mb-3">
                            <label class="form-label">Instruction / annotation</label>
                            <textarea name="instruction_drs" class="form-control" rows="3"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Transmettre au chef de service</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Document attaché</h5>
                </div>
                <div class="card-body">
                    @if($courrier->documents->count())
                        @foreach($courrier->documents as $doc)
                            <a href="{{ Storage::url($doc->chemin_fichier) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-download"></i> {{ $doc->nom_fichier }}
                            </a>
                        @endforeach
                    @else
                        <p class="text-muted">Aucun document</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('secteur').addEventListener('change', function() {
        const serviceSelect = document.getElementById('service_emetteur_id');
        const valeur = this.value;

        if (valeur === 'ifd') serviceSelect.value = '1';
        else if (valeur === 'gfmpf') serviceSelect.value = '2';
        else if (valeur === 'zes') serviceSelect.value = '3';
        else serviceSelect.value = '';
    });
</script>
@endsection