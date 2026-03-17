@extends('layouts.app')

@section('title', 'Gestion des rôles')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3">
                <i class="bi bi-shield-lock me-2"></i>Rôles et permissions
            </h1>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Rôle</th>
                        <th>Nombre d'utilisateurs</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles ?? [] as $role)
                    <tr>
                        <td>{{ $role->name }}</td>
                        <td>{{ $role->users_count ?? 0 }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="text-center">Aucun rôle</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection