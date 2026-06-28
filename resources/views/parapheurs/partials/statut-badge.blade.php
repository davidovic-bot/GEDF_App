@php
    $statutColors = [
        'creer' => 'secondary',
        'analyse' => 'info',
        'attente_validation' => 'warning',
        'valide_cs' => 'primary',
        'attente_signature' => 'warning',
        'signe' => 'success',
        'archive' => 'secondary',
        'rejete' => 'danger'
    ];
    $statutCode = $parapheur->statut->code ?? $parapheur->statut ?? 'inconnu';
    $statutLabel = ucfirst(str_replace('_', ' ', $statutCode));
@endphp
<span class="badge badge-{{ $statutColors[$statutCode] ?? 'secondary' }}">
    {{ $statutLabel }}
</span>