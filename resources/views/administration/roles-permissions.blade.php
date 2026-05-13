@extends('layouts.gdf')

@section('title', 'Gestion des permissions')

@section('content')

@if(session('success'))
    <div class="container-fluid mt-3">
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    </div>
@endif

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3">
                <i class="bi bi-key me-2"></i>Permissions du rôle
            </h1>
            <p class="text-muted">
                Gérer les permissions pour le rôle <strong>{{ $role->name }}</strong>
            </p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.roles-list') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.roles-permissions-update', $role->id) }}" method="POST">
                @csrf

                <div class="mb-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="select-all">
                        <label class="form-check-label fw-bold" for="select-all">
                            Tout sélectionner / Tout désélectionner
                        </label>
                    </div>
                </div>

                <hr>

                <div class="row">
                    @php
                        $groupedPermissions = $permissions->groupBy(function($perm) {
                            return explode('.', $perm->name)[0];
                        });
                    @endphp

                    @foreach($groupedPermissions as $module => $modulePermissions)
                        <div class="col-md-6 mb-4">
                            <div class="card border">
                                <div class="card-header bg-light">
                                    <strong>{{ ucfirst($module) }}</strong>
                                </div>
                                <div class="card-body">
                                    @foreach($modulePermissions as $permission)
                                        <div class="form-check mb-2">
                                            <input type="checkbox" 
                                                   class="form-check-input permission-checkbox" 
                                                   name="permissions[]" 
                                                   value="{{ $permission->id }}"
                                                   id="perm-{{ $permission->id }}"
                                                   {{ in_array($permission->id, $assignedPermissions) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="perm-{{ $permission->id }}">
                                                {{ $permission->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Enregistrer les permissions
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('select-all').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.permission-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
</script>
@endsection