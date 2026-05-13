@extends('layouts.gdf')

@section('title', 'Gestion des rôles')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3">
                <i class="bi bi-shield-lock me-2"></i>Gestion des rôles
            </h1>
            <p class="text-muted">
                Gérer les rôles et leurs permissions dans le système
            </p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.roles-create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nouveau rôle
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nom du rôle</th>
                            <th>Nombre d'utilisateurs</th>
                            <th>Permissions</th>
                            <th>Date de création</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles ?? [] as $role)
                        <tr>
                            <td>{{ $role->id }}</td>
                            <td>
                                <strong>{{ $role->name }}</strong>
                                @if($role->name == 'superadmin')
                                    <span class="badge bg-danger ms-2">Super Admin</span>
                                @elseif($role->name == 'admin')
                                    <span class="badge bg-warning ms-2">Admin</span>
                                @elseif($role->name == 'directeur')
                                    <span class="badge bg-primary ms-2">Directeur</span>
                                @elseif($role->name == 'chef_service')
                                    <span class="badge bg-info ms-2">Chef Service</span>
                                @elseif($role->name == 'agent')
                                    <span class="badge bg-success ms-2">Agent</span>
                                @elseif($role->name == 'secretaire')
                                    <span class="badge bg-secondary ms-2">Secrétaire</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $role->users_count ?? 0 }}</span>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $role->permissions_count ?? 0 }}</span>
                            </td>
                            <td>{{ $role->created_at ? \Carbon\Carbon::parse($role->created_at)->format('d/m/Y H:i') : '-' }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.roles-edit', $role->id) }}" class="btn btn-warning" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="{{ route('admin.roles-permissions', $role->id) }}" class="btn btn-info" title="Permissions">
                                        <i class="bi bi-key"></i>
                                    </a>
                                    @if($role->name != 'superadmin')
                                        <button type="button" class="btn btn-danger" onclick="confirmDelete({{ $role->id }})" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        <form id="delete-form-{{ $role->id }}" action="{{ route('admin.roles-destroy', $role->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                Aucun rôle trouvé
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce rôle ?')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
@endsection