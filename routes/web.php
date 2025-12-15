<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ParapheurController;
use App\Http\Controllers\StatistiqueController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Routes publiques
|
*/

// Page d'accueil redirige vers le login
Route::get('/', function () {
    return view('auth.login');
});

// Routes d'authentification Breeze
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| DASHBOARDS SELON LES RÔLES
|--------------------------------------------------------------------------
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

// =============================================================================
// MODULE PARAPHEURS - ACCESSIBLE SELON LES RÔLES
// =============================================================================

Route::middleware(['auth'])->prefix('parapheurs')->name('parapheurs.')->group(function () {
    // Routes principales (la sécurité se fait dans le contrôleur)
    Route::get('/', [ParapheurController::class, 'index'])->name('index');
    Route::get('/create', [ParapheurController::class, 'create'])->name('create');
    Route::post('/', [ParapheurController::class, 'store'])->name('store');
    Route::get('/{parapheur}', [ParapheurController::class, 'show'])->name('show');
    Route::get('/{parapheur}/edit', [ParapheurController::class, 'edit'])->name('edit');
    Route::put('/{parapheur}', [ParapheurController::class, 'update'])->name('update');
    Route::delete('/{parapheur}', [ParapheurController::class, 'destroy'])->name('destroy');
    
    // Actions workflow
    Route::post('/{parapheur}/valider', [ParapheurController::class, 'valider'])->name('valider');
    Route::post('/{parapheur}/rejeter', [ParapheurController::class, 'rejeter'])->name('rejeter');
    Route::post('/{parapheur}/transmettre', [ParapheurController::class, 'transmettre'])->name('transmettre');
    Route::post('/{parapheur}/fichiers', [ParapheurController::class, 'joindreFichier'])->name('fichiers.store');
    
    // Actions spécifiques superadmin
    Route::middleware(['issuperadmin'])->group(function () {
        Route::post('/{parapheur}/reassign', [ParapheurController::class, 'reassign'])->name('reassign');
        Route::post('/{parapheur}/forcer-etape', [ParapheurController::class, 'forcerEtape'])->name('forcer.etape');
        Route::post('/{parapheur}/archiver', [ParapheurController::class, 'archiver'])->name('archiver');
    });
});

// =============================================================================
// MODULE STATISTIQUES
// =============================================================================

Route::middleware(['auth', 'issuperadmin'])->prefix('statistiques')->group(function () {
    Route::get('/', [StatistiqueController::class, 'index'])->name('statistiques.index');
});

// =============================================================================
// MODULE ADMINISTRATION
// =============================================================================

Route::middleware(['auth', 'issuperadmin'])->prefix('administration')->group(function () {
    Route::get('/utilisateurs', [AdminController::class, 'index'])->name('admin.utilisateurs');
    Route::get('/roles', [AdminController::class, 'roles'])->name('admin.roles');
    Route::get('/parametres', [AdminController::class, 'parametres'])->name('admin.parametres');
    Route::get('/audit', [AdminController::class, 'audit'])->name('admin.audit');
});

// =============================================================================
// DASHBOARD SUPERADMIN (alternative)
// =============================================================================

Route::get('/dashboard-superadmin', function () {
    return view('dashboard-superadmin');
})->name('dashboard.superadmin');

// =============================================================================
// ADMINISTRATION AVEC RESSOURCES
// =============================================================================

Route::middleware(['auth', 'role:superadmin|admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
    
    // À décommenter quand tu auras ces contrôleurs
    // Route::resource('utilisateurs', Admin\UtilisateurController::class);
    // Route::resource('roles', Admin\RoleController::class);
});

// =============================================================================
// ROUTE FALLBACK POUR LES ERREURS 404
// =============================================================================

Route::fallback(function () {
    return redirect()->route('dashboard.superadmin');
});