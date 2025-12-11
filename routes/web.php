<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Routes publiques
|
*/

// Page d'accueil par défaut (optionnelle)
Route::get('/', function () {
    return redirect('/login');
});

// Routes d'authentification Breeze
require __DIR__.'/auth.php';


/*
|--------------------------------------------------------------------------
| DASHBOARDS SELON LES RÔLES
|--------------------------------------------------------------------------
|
| Chaque rôle accède à son propre dashboard.
| Protection effectuée grâce aux middlewares personnalisés.
|
*/

// SUPERADMIN
Route::middleware(['auth', 'issuperadmin'])->get('/dashboard/superadmin', function () {
    return view('dashboards.superadmin');
})->name('dashboard.superadmin');

// ADMIN
Route::middleware(['auth', 'isadmin'])->get('/dashboard/admin', function () {
    return view('dashboards.admin');
})->name('dashboard.admin');

// SECRETAIRE
Route::middleware(['auth', 'issecretaire'])->get('/dashboard/secretaire', function () {
    return view('dashboards.secretaire');
})->name('dashboard.secretaire');

// GESTIONNAIRE
Route::middleware(['auth', 'isgestionnaire'])->get('/dashboard/gestionnaire', function () {
    return view('dashboards.gestionnaire');
})->name('dashboard.gestionnaire');

// CHEF DE SERVICE
Route::middleware(['auth', 'ischefservice'])->get('/dashboard/chef-service', function () {
    return view('dashboards.chef-service');
})->name('dashboard.chefservice');

// DIRECTEUR
Route::middleware(['auth', 'isdirecteur'])->get('/dashboard/directeur', function () {
    return view('dashboards.directeur');
})->name('dashboard.directeur');

// ===== MODULES POUR SUPER ADMIN =====

// Module Parapheurs (accessible seulement à Super Admin)
Route::middleware(['auth', 'issuperadmin'])->prefix('parapheurs')->group(function () {
    Route::get('/', [ParapheurController::class, 'index'])->name('parapheurs.index');
    Route::get('/create', [ParapheurController::class, 'create'])->name('parapheurs.create');
    // ... autres routes
});

// Module Statistiques (accessible à Super Admin)
Route::middleware(['auth', 'issuperadmin'])->prefix('statistiques')->group(function () {
    Route::get('/', [StatistiqueController::class, 'index'])->name('statistiques.index');
});

// Module Administration (accessible à Super Admin)
Route::middleware(['auth', 'issuperadmin'])->prefix('administration')->group(function () {
    Route::get('/utilisateurs', [AdminController::class, 'index'])->name('admin.utilisateurs');
    Route::get('/roles', [AdminController::class, 'roles'])->name('admin.roles');
    Route::get('/parametres', [AdminController::class, 'parametres'])->name('admin.parametres');
    Route::get('/audit', [AdminController::class, 'audit'])->name('admin.audit');
});