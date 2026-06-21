@extends('layouts.gdf')

@section('title', 'Enregistrer un courrier')

@section('content')

@if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3" style="color: #120d0d !important;">Enregistrement d’un courrier</h1>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <form action="{{ route('courriers.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Bénéficiaire *</label>
                        <input type="text" name="beneficiaire" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">NIF</label>
                        <input type="text" name="nif" class="form-control">
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

                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </form>
        </div>
    </div>
</div>
@endsection
@if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif