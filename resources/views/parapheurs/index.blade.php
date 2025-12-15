@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3>Module Parapheurs - TEST</h3>
        </div>
        <div class="card-body">
            <div class="alert alert-success">
                ✅ Contrôleur fonctionnel !
                <br>
                <strong>Message :</strong> {{ $message }}
                <br>
                <strong>Utilisateur :</strong> {{ $user_name }} ({{ $user_role }})
            </div>
            
            <table class="table">
                <tr><th>Référence</th><th>Objet</th><th>Statut</th><th>Action</th></tr>
                @foreach($parapheurs as $p)
                <tr>
                    <td>{{ $p->reference }}</td>
                    <td>{{ $p->objet }}</td>
                    <td><span class="badge badge-warning">{{ $p->statut }}</span></td>
                    <td>
                        <a href="{{ route('parapheurs.show', $p->id) }}" class="btn btn-sm btn-primary">
                            Voir
                        </a>
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
@endsection