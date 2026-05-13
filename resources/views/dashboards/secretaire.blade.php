@extends('layouts.gdf')

@section('title', 'Tableau de bord - Secrétariat')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3">Tableau de bord secrétariat</h1>
            <p class="text-muted">Bienvenue, {{ auth()->user()->name }}.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Parapheurs à saisir</h5>
                    <p class="card-text display-4">{{ $parapheursASaisir ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h5 class="card-title">Parapheurs rejetés</h5>
                    <p class="card-text display-4">{{ $parapheursRejetes ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection