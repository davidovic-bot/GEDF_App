<?php

use App\Http\Controllers\CourrierController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ParapheurController;
use App\Http\Controllers\StatistiqueController;
use App\Http\Controllers\DashboardController;

// Page d'accueil
Route::get('/', function () {
    return view('auth.login');
});

// =============================================================================
// 1. AUTHENTIFICATION BREEZE (DOIT ÊTRE EN PREMIER)
// =============================================================================
require __DIR__.'/auth.php';

// =============================================================================
// 2. DASHBOARD PRINCIPAL (POUR TOUS LES UTILISATEURS)
// =============================================================================
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard')
    ->middleware('auth');

// =============================================================================
// 3. DASHBOARDS SPÉCIFIQUES (SUPERADMIN ET ADMIN UNIQUEMENT)
// =============================================================================

// SUPERADMIN
Route::middleware(['auth'])->get('/superadmin', function () {
    $user = auth()->user();
    if ($user->email !== 'superadmin@gedf.com') {
        abort(403, 'Accès réservé au superadmin');
    }
    return view('dashboards.superadmin');
})->name('superadmin.dashboard');

// ADMIN
Route::middleware(['auth'])->get('/admin', function () {
    $user = auth()->user();
    $allowed = ['superadmin@gedf.com', 'admin@gedf.com'];
    if (!in_array($user->email, $allowed)) {
        abort(403, 'Accès réservé aux administrateurs');
    }
    return view('dashboards.admin');
})->name('admin.dashboard');

// =============================================================================
// 4. MODULE COURRIERS
// =============================================================================
Route::middleware(['auth'])->prefix('courriers')->name('courriers.')->group(function () {
    Route::get('/archives', [CourrierController::class, 'archives'])->name('archives');
    Route::get('/', [CourrierController::class, 'index'])->name('index');
    Route::get('/create', [CourrierController::class, 'create'])->name('create');
    Route::post('/', [CourrierController::class, 'store'])->name('store');
    Route::get('/{courrier}', [CourrierController::class, 'show'])->name('show');
    Route::get('/{courrier}/edit', [CourrierController::class, 'edit'])->name('edit');
    Route::put('/{courrier}', [CourrierController::class, 'update'])->name('update');
    Route::delete('/{courrier}', [CourrierController::class, 'destroy'])->name('destroy');
    Route::post('/{courrier}/transmettre', [CourrierController::class, 'transmettre'])->name('transmettre');
    Route::get('/{courrier}/analyse', [CourrierController::class, 'analyse'])->name('analyse');
    Route::post('/{courrier}/transmettre-chef', [CourrierController::class, 'transmettreChef'])->name('transmettre.chef');
    Route::post('/{courrier}/valider', [CourrierController::class, 'valider'])->name('valider');
    Route::get('/courriers/{courrier}/instruire', [CourrierController::class, 'instruire'])->name('instruire');
    Route::post('/courriers/{courrier}/instruire', [CourrierController::class, 'storeInstruction'])->name('store.instruction');
    Route::get('/{courrier}/instruction', [CourrierController::class, 'instructionDrs'])->name('instruction.drs');
    Route::post('/{courrier}/instruction', [CourrierController::class, 'storeInstructionDrs'])->name('instruction.store');
    

    Route::post('/{courrier}/attribuer', [CourrierController::class, 'attribuer'])->name('attribuer');
    Route::post('/{courrier}/upload-document', [CourrierController::class, 'uploadDocument'])->name('upload-document');
    Route::get('/{courrier}/historique', [CourrierController::class, 'historique'])->name('historique');
    Route::post('/{courrier}/changer-statut', [CourrierController::class, 'changerStatut'])->name('changer-statut');
    Route::post('/{courrier}/deposer-parapheur', [CourrierController::class, 'deposerParapheur'])->name('deposer-parapheur');
});

// =============================================================================
// 5. MODULE PARAPHEURS
// =============================================================================
Route::middleware(['auth'])->prefix('parapheurs')->name('parapheurs.')->group(function () {
    Route::get('/', [ParapheurController::class, 'index'])->name('index');

    // Agent / Gestionnaire
    Route::middleware(['auth'])->group(function () {
        Route::get('/agent', function () {
            $user = auth()->user();
            $allowed = ['superadmin@gedf.com', 'admin@gedf.com', 'gestionnaire@gedf.com'];
            if (!in_array($user->email, $allowed)) {
                abort(403, 'Accès réservé aux gestionnaires');
            }
            return app(ParapheurController::class)->vueAgent();
        })->name('agent');

        Route::get('/a-analyser', [ParapheurController::class, 'aAnalyser'])->name('a.analyser');
        Route::post('/{parapheur}/transmettre-chef-service', [ParapheurController::class, 'transmettreChefService'])->name('transmettre.chef_service');
        Route::post('/{parapheur}/rejeter-vers-secretaire', [ParapheurController::class, 'rejeterVersSecretaire'])->name('rejeter.secretaire');
    });

    // Chef de service
    Route::middleware(['auth'])->group(function () {
        Route::get('/chef-service', function () {
            $user = auth()->user();
            $allowed = ['superadmin@gedf.com', 'admin@gedf.com', 'chefservice@gedf.com'];
            if (!in_array($user->email, $allowed)) {
                abort(403, 'Accès réservé aux chefs de service');
            }
            return app(ParapheurController::class)->vueChefService();
        })->name('chef_service');

        Route::get('/a-valider', [ParapheurController::class, 'aValider'])->name('a.valider');
        Route::post('/{parapheur}/valider', [ParapheurController::class, 'valider'])->name('valider');
        Route::post('/{parapheur}/rejeter-vers-agent', [ParapheurController::class, 'rejeterVersAgent'])->name('rejeter.agent');
        Route::post('/{parapheur}/transmettre-directeur', [ParapheurController::class, 'transmettreDirecteur'])->name('transmettre.directeur');
    });

    // Directeur DRS
    Route::middleware(['auth'])->group(function () {
        Route::get('/directeur', function () {
            $user = auth()->user();
            $allowed = ['superadmin@gedf.com', 'admin@gedf.com', 'directeur@gedf.com'];
            if (!in_array($user->email, $allowed)) {
                abort(403, 'Accès réservé aux directeurs');
            }
            return app(ParapheurController::class)->vueDirecteur();
        })->name('directeur');

        Route::get('/a-signer', [ParapheurController::class, 'aSigner'])->name('a.signer');
        Route::post('/{parapheur}/signer', [ParapheurController::class, 'signer'])->name('signer');
        Route::post('/{parapheur}/rejeter-exceptionnel', [ParapheurController::class, 'rejeterExceptionnel'])->name('rejeter.exceptionnel');
    });

    // Supervision (superadmin uniquement)
    Route::middleware(['auth'])->group(function () {
        Route::get('/supervision', function () {
            $user = auth()->user();
            if ($user->email !== 'superadmin@gedf.com') {
                abort(403, 'Accès réservé au superadmin');
            }
            return app(ParapheurController::class)->supervision();
        })->name('supervision');

        Route::post('/{parapheur}/archiver', [ParapheurController::class, 'archiver'])->name('archiver');
        Route::get('/historique/{parapheur}', [ParapheurController::class, 'historique'])->name('historique');
    });

    // Routes avec paramètres (toujours en dernier)
    Route::get('/{parapheur}', [ParapheurController::class, 'show'])->name('show');
    Route::get('/{parapheur}/edit', [ParapheurController::class, 'edit'])->name('edit');
    Route::put('/{parapheur}', [ParapheurController::class, 'update'])->name('update');
});

// =============================================================================
// ROUTE TEST
// =============================================================================
Route::middleware(['auth'])->get('/test-final', function () {
    $user = auth()->user();
    if ($user->email !== 'superadmin@gedf.com') {
        abort(403, 'Test échoué: ' . $user->email);
    }
    return "🎉 TEST FINAL RÉUSSI ! Email: " . $user->email;
});

// =============================================================================
// FALLBACK
// =============================================================================
Route::fallback(function () {
    return redirect()->route('dashboard');
});

// =============================================================================
// ROUTES D'ADMINISTRATION
// =============================================================================
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\AdminController::class, 'index'])->name('dashboard');
    Route::get('/', [App\Http\Controllers\Admin\AdminController::class, 'index'])->name('home');

    Route::resource('services', App\Http\Controllers\Admin\ServiceController::class)
        ->names([
            'index' => 'services.index',
            'create' => 'services.create',
            'store' => 'services.store',
            'show' => 'services.show',
            'edit' => 'services.edit',
            'update' => 'services.update',
            'destroy' => 'services.destroy',
        ]);

    Route::post('/services/{service}/activer', [App\Http\Controllers\Admin\ServiceController::class, 'activer'])->name('services.activer');
    Route::post('/services/{service}/desactiver', [App\Http\Controllers\Admin\ServiceController::class, 'desactiver'])->name('services.desactiver');

    Route::prefix('statistiques')->name('statistiques.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\StatistiqueController::class, 'index'])->name('index');
        Route::get('/periode', [App\Http\Controllers\Admin\StatistiqueController::class, 'parPeriode'])->name('periode');
        Route::get('/export', [App\Http\Controllers\Admin\StatistiqueController::class, 'export'])->name('export');
    });

    Route::get('/audit', [App\Http\Controllers\Admin\AdminController::class, 'audit'])->name('audit-logs');

    Route::get('/utilisateurs', [App\Http\Controllers\Admin\AdminController::class, 'utilisateurs'])->name('utilisateurs');
    Route::get('/utilisateurs/create', [App\Http\Controllers\Admin\AdminController::class, 'utilisateursCreate'])->name('utilisateurs-create');
    Route::post('/utilisateurs', [App\Http\Controllers\Admin\AdminController::class, 'utilisateursStore'])->name('utilisateurs-store');
    Route::get('/utilisateurs/{id}/edit', [App\Http\Controllers\Admin\AdminController::class, 'utilisateursEdit'])->name('utilisateurs-edit');
    Route::put('/utilisateurs/{id}', [App\Http\Controllers\Admin\AdminController::class, 'utilisateursUpdate'])->name('utilisateurs-update');

    Route::get('/parametres', [App\Http\Controllers\Admin\AdminController::class, 'parametres'])->name('settings');
    Route::post('/clear-cache', [App\Http\Controllers\Admin\AdminController::class, 'clearCache'])->name('clear-cache');
    Route::get('/search', [App\Http\Controllers\Admin\AdminController::class, 'search'])->name('search');

    Route::get('/roles', [App\Http\Controllers\Admin\AdminController::class, 'roles'])->name('roles-list');
    Route::get('/roles/create', [App\Http\Controllers\Admin\AdminController::class, 'rolesCreate'])->name('roles-create');
    Route::post('/roles', [App\Http\Controllers\Admin\AdminController::class, 'rolesStore'])->name('roles-store');
    Route::get('/roles/{id}/edit', [App\Http\Controllers\Admin\AdminController::class, 'rolesEdit'])->name('roles-edit');
    Route::put('/roles/{id}', [App\Http\Controllers\Admin\AdminController::class, 'rolesUpdate'])->name('roles-update');
    Route::delete('/roles/{id}', [App\Http\Controllers\Admin\AdminController::class, 'rolesDestroy'])->name('roles-destroy');
    Route::get('/roles/{id}/permissions', [App\Http\Controllers\Admin\AdminController::class, 'rolesPermissions'])->name('roles-permissions');
    Route::post('/roles/{id}/permissions', [App\Http\Controllers\Admin\AdminController::class, 'rolesPermissionsUpdate'])->name('roles-permissions-update');
    Route::post('/courriers/{courrier}/transmettre', [CourrierController::class, 'transmettre'])->name('courriers.transmettre');
});