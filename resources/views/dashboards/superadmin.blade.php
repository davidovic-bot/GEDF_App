@extends('layouts.admin')

@section('page_title')

@section('content')

<!-- HEADER PRINCIPAL (STYLE CFU) -->
<div class="bg-white p-6 rounded-xl shadow-sm border mb-8">
    <h1 class="text-2xl font-bold text-gray-800">Bonjour SuperAdmin 👋</h1>
    <p class="text-gray-500 mt-1">
        Aperçu général de vos modules GDF.
    </p>
</div>

<!-- GRID DES CARTES STATISTIQUES (STYLE CFU) -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">

    <!-- Parapheurs -->
    <div class="bg-white p-6 border rounded-xl shadow-sm hover:shadow-md transition">
        <h3 class="text-gray-600 font-semibold mb-2">Parapheurs</h3>
        <p class="text-gray-500 text-sm">Accédez aux dossiers entrants.</p>
    </div>

    <!-- Statistiques -->
    <div class="bg-white p-6 border rounded-xl shadow-sm hover:shadow-md transition">
        <h3 class="text-gray-600 font-semibold mb-2">Statistiques</h3>
        <p class="text-gray-500 text-sm">Visualisez les performances.</p>
    </div>

    <!-- Administration -->
    <div class="bg-white p-6 border rounded-xl shadow-sm hover:shadow-md transition">
        <h3 class="text-gray-600 font-semibold mb-2">Administration</h3>
        <p class="text-gray-500 text-sm">Configurer le système et les utilisateurs.</p>
    </div>

</div>

<!-- BLOC PRINCIPAL (LAYOUT CFU) -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- GRANDE CARTE À GAUCHE (2/3) -->
    <div class="lg:col-span-2 bg-white border rounded-xl shadow-sm p-6">
        <h2 class="text-xl font-bold text-gray-700 mb-4">Activité Générale</h2>
        <p class="text-gray-500">
            Résumé des modules et actions effectuées récemment.
        </p>

        <div class="mt-6 h-64 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400">
            (Contenu visuel à venir)
        </div>
    </div>

    <!-- CARTE INFORMATIVE À DROITE (1/3) -->
    <div class="bg-white border rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">Informations Système</h3>

        <ul class="text-gray-500 space-y-2">
            <li>Module : GDF</li>
            <li>Profil : SuperAdmin</li>
            <li>Date : {{ now()->format('d/m/Y') }}</li>
        </ul>
    </div>

</div>

@endsection