@extends('layouts.gdf')

@section('title', 'Tableau de bord GDF')

@section('content')
    <!-- Titre Dashboard -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Tableau de bord GDF</h1>
        <p class="text-gray-600 mt-1">Supervision complète du système de Gestion des Dépenses Fiscales via courriers et parapheurs électronique</p>
        
        <!-- Indicateurs rapides -->
        <div class="mt-4 flex flex-wrap gap-3">
            <div class="flex items-center text-sm text-green-700 bg-green-50 px-3 py-1.5 rounded-lg">
                <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                Système opérationnel
            </div>
            <div class="flex items-center text-sm text-blue-700 bg-blue-50 px-3 py-1.5 rounded-lg">
                <div class="w-2 h-2 bg-blue-500 rounded-full mr-2"></div>
                10 utilisateurs connectés
            </div>
            <div class="flex items-center text-sm text-yellow-700 bg-yellow-50 px-3 py-1.5 rounded-lg">
                <div class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></div>
                3 alertes en attente
            </div>
        </div>
    </div>

    <!-- Stats Cards Essentielles -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <p class="text-sm text-gray-500 mb-1">PARAPHEURS EN ATTENTE</p>
            <div class="flex items-baseline">
                <span class="text-3xl font-bold text-gray-900">18</span>
                <span class="ml-2 text-sm text-red-600">dont 3 en retard</span>
            </div>
            <p class="text-xs text-green-600 mt-2">+2 depuis hier</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6">
            <p class="text-sm text-gray-500 mb-1">PARAPHEURS VALIDÉS</p>
            <div class="flex items-baseline">
                <span class="text-3xl font-bold text-gray-900">47</span>
                <span class="ml-2 text-sm text-gray-500">ce mois</span>
            </div>
            <p class="text-xs text-green-600 mt-2">+8% vs mois dernier</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6">
            <p class="text-sm text-gray-500 mb-1">UTILISATEURS ACTIFS</p>
            <div class="flex items-baseline">
                <span class="text-3xl font-bold text-gray-900">10</span>
                <span class="ml-2 text-sm text-gray-500">sur 6 rôles</span>
            </div>
            <p class="text-xs text-gray-500 mt-2">stable cette semaine</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6">
            <p class="text-sm text-gray-500 mb-1">DÉLAI MOYEN</p>
            <div class="flex items-baseline">
                <span class="text-3xl font-bold text-gray-900">2,8</span>
                <span class="ml-2 text-sm text-gray-500">jours</span>
            </div>
            <p class="text-xs text-green-600 mt-2">-0,3 jour</p>
        </div>
    </div>
    
    <!-- Section informations essentielles -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Prochaines échéances</h3>
            <div class="space-y-3">
                <div>
                    <p class="font-medium text-gray-900">Rapport mensuel d'activité</p>
                    <p class="text-sm text-gray-600">Rapport de performance du système</p>
                    <p class="text-xs text-blue-600 mt-1">Dans 3 jours</p>
                </div>
                <div>
                    <p class="font-medium text-gray-900">Audit de sécurité</p>
                    <p class="text-sm text-gray-600">Audit semestriel du système</p>
                    <p class="text-xs text-blue-600 mt-1">15/04/2024</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Performances système</h3>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-sm text-gray-600">Temps de réponse</span>
                        <span class="font-bold text-green-600">98,7%</span>
                    </div>
                    <p class="text-xs text-gray-500">Objectif : 95%</p>
                </div>
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-sm text-gray-600">Disponibilité</span>
                        <span class="font-bold text-green-600">99,9%</span>
                    </div>
                    <p class="text-xs text-gray-500">Objectif : 99,5%</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Support technique</h3>
            <p class="text-sm text-gray-600 mb-4">Pour toute assistance technique :</p>
            <div class="space-y-2">
                <p class="text-blue-600 font-medium">support-drs@dgi.gov.ga</p>
                <p class="text-blue-600 font-medium">+241 01 44 08 08</p>
            </div>
        </div>
    </div>
@endsection