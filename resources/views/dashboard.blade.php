@extends('layouts.gdf')

@section('title', 'Tableau de bord')

@section('content')
<div class="container-fluid py-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="h2 fw-bold text-dark mb-1">Tableau de bord</h1>
            <p class="text-muted fs-5">
                Bienvenue back,
                <span class="fw-semibold text-primary">{{ auth()->user()->name }}</span> 👋
            </p>
        </div>
        <div>
            <span class="badge bg-light text-dark py-2 px-3">
                {{ now()->format('d F Y') }}
            </span>
        </div>
    </div>

    <!-- STATISTIQUES -->
    <div class="row g-4">

        <!-- CARD 1 -->
        <div class="col-xl-4 col-md-6">
            <div class="card border-0 shadow-sm h-100 modern-card navy-card">
                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-white-50 mb-1 fw-medium">Dossiers en cours</p>
                            <h2 class="display-5 fw-bold text-white mb-0">
                                {{ $stats['dossiers_en_cours'] ?? 0 }}
                            </h2>
                        </div>

                        <div class="icon-wrapper">
                            📁
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="progress progress-custom">
                            <div class="progress-bar" style="width: 65%"></div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- CARD 2 -->
        <div class="col-xl-4 col-md-6">
            <div class="card border-0 shadow-sm h-100 modern-card navy-card">
                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-white-50 mb-1 fw-medium">En attente de validation</p>
                            <h2 class="display-5 fw-bold text-white mb-0">
                                {{ $stats['en_attente_validation'] ?? 0 }}
                            </h2>
                        </div>

                        <div class="icon-wrapper">
                            ⏳
                        </div>
                    </div>

                    <div class="mt-3 text-white-50 small fw-medium">
                        Nécessite votre attention
                    </div>

                </div>
            </div>
        </div>

        <!-- CARD 3 -->
        <div class="col-xl-4 col-md-6">
            <div class="card border-0 shadow-sm h-100 modern-card navy-card">
                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-white-50 mb-1 fw-medium">Validés ce mois</p>
                            <h2 class="display-5 fw-bold text-white mb-0">
                                {{ $stats['valides_mois'] ?? 0 }}
                            </h2>
                        </div>

                        <div class="icon-wrapper">
                            ✅
                        </div>
                    </div>

                    <div class="mt-4 text-white-50 small">
                        +12% par rapport au mois dernier
                    </div>

                </div>
            </div>
        </div>

    </div>

</div>

<!-- STYLE -->
<style>

/* CARD BASE */
.modern-card {
    transition: all 0.3s ease;
    border-radius: 18px;
    overflow: hidden;
}

/* BLEU MARINE PREMIUM */
.navy-card {
    background: linear-gradient(135deg, #0b1f3a, #0a2a52);
    color: white;
    position: relative;
}

/* effet lumière subtil */
.navy-card::before {
    content: "";
    position: absolute;
    top: -60%;
    right: -60%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.10), transparent 60%);
    transform: rotate(25deg);
}

/* hover pro */
.navy-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 25px 50px rgba(0,0,0,0.35) !important;
}

/* icône glass */
.icon-wrapper {
    font-size: 1.8rem;
    background: rgba(255,255,255,0.12);
    padding: 12px;
    border-radius: 12px;
    backdrop-filter: blur(10px);
}

/* progress bar sobre */
.progress-custom {
    height: 6px;
    background: rgba(255,255,255,0.15);
    border-radius: 10px;
}

.progress-custom .progress-bar {
    background: #ffffff;
    border-radius: 10px;
}

/* chiffres */
.display-5 {
    font-size: 2.6rem;
    line-height: 1.1;
}

</style>

@endsection