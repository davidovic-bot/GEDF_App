<?php

use App\Http\Controllers\CourrierController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ParapheurController;
use App\Http\Controllers\StatistiqueController;
use App\Http\Controllers\AdminController;

// Page d'accueil
Route::get('/', function () {
    return view('auth.login');
});

// =============================================================================
// 1. AUTHENTIFICATION BREEZE (DOIT ÊTRE EN PREMIER)
// =============================================================================
require __DIR__.'/auth.php';

// =============================================================================
// 2. DASHBOARD PRINCIPAL (DOIT ÊTRE AVANT LES DASHBOARDS SPÉCIFIQUES)
// =============================================================================
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        // DÉTERMINE LE DASHBOARD SELON L'UTILISATEUR
        $user = auth()->user();
        
        // OPTION SIMPLE : vérifie par email
        if ($user->email === 'superadmin@gedf.com') {
            return view('dashboards.superadmin');
        }
        
        // OPTION 2 : vérifie par poste
        if ($user->poste === 'Super Admin') {
            return view('dashboards.superadmin');
        }
        
        // OPTION 3 : vue par défaut de Breeze
        return view('dashboard');
    })->name('dashboard');
});

// =============================================================================
// 3. DASHBOARDS SPÉCIFIQUES (AVEC URLS DIFFÉRENTES POUR ÉVITER LES CONFLITS)
// =============================================================================

// SUPERADMIN - URL: /superadmin
Route::middleware(['auth'])->get('/superadmin', function () {
    // Vérifie MANUELLEMENT dans la route
    $user = auth()->user();
    
    if ($user->email !== 'superadmin@gedf.com') {
        abort(403, 'Accès réservé au superadmin');
    }
    
    return view('dashboards.superadmin');
})->name('superadmin.dashboard');

// ADMIN - URL: /admin
Route::middleware(['auth'])->get('/admin', function () {
    // Vérifie MANUELLEMENT
    $user = auth()->user();
    $allowed = ['superadmin@gedf.com', 'admin@gedf.com'];
    
    if (!in_array($user->email, $allowed)) {
        abort(403, 'Accès réservé aux administrateurs');
    }
    
    return view('dashboards.admin');
})->name('admin.dashboard');

// SECRÉTAIRE - URL: /secretaire
Route::middleware(['auth'])->get('/secretaire', function () {
    // Vérifie MANUELLEMENT
    $user = auth()->user();
    $allowed = ['superadmin@gedf.com', 'admin@gedf.com', 'secretaire@gedf.com'];
    
    if (!in_array($user->email, $allowed)) {
        abort(403, 'Accès réservé au secrétariat');
    }
    
    $parapheursASaisir = DB::table('parapheurs')
        ->join('parapheur_statuts', 'parapheurs.statut_id', '=', 'parapheur_statuts.id')
        ->where('parapheur_statuts.code', 'creer')
        ->where('parapheurs.created_by', auth()->id())
        ->count();
        
    $parapheursRejetes = DB::table('parapheurs')
        ->join('parapheur_statuts', 'parapheurs.statut_id', '=', 'parapheur_statuts.id')
        ->where('parapheur_statuts.code', 'rejete')
        ->where('parapheurs.created_by', auth()->id())
        ->count();
    
    return view('dashboards.secretaire', compact('parapheursASaisir', 'parapheursRejetes'));
})->name('secretaire.dashboard');

// GESTIONNAIRE - URL: /gestionnaire
Route::middleware(['auth'])->get('/gestionnaire', function () {
    // Vérifie MANUELLEMENT
    $user = auth()->user();
    $allowed = ['superadmin@gedf.com', 'admin@gedf.com', 'gestionnaire@gedf.com'];
    
    if (!in_array($user->email, $allowed)) {
        abort(403, 'Accès réservé aux gestionnaires');
    }
    
    $parapheursAAnalyser = DB::table('parapheurs')
        ->join('parapheur_statuts', 'parapheurs.statut_id', '=', 'parapheur_statuts.id')
        ->where('parapheur_statuts.code', 'analyse')
        ->count();
    
    return view('dashboards.gestionnaire', compact('parapheursAAnalyser'));
})->name('gestionnaire.dashboard');

// CHEF SERVICE - URL: /chef-service
Route::middleware(['auth'])->get('/chef-service', function () {
    // Vérifie MANUELLEMENT
    $user = auth()->user();
    $allowed = ['superadmin@gedf.com', 'admin@gedf.com', 'chefservice@gedf.com'];
    
    if (!in_array($user->email, $allowed)) {
        abort(403, 'Accès réservé aux chefs de service');
    }
    
    $parapheursAValider = DB::table('parapheurs')
        ->join('parapheur_statuts', 'parapheurs.statut_id', '=', 'parapheur_statuts.id')
        ->where('parapheur_statuts.code', 'attente_validation')
        ->count();
    
    return view('dashboards.chef-service', compact('parapheursAValider'));
})->name('chefservice.dashboard');

// DIRECTEUR - URL: /directeur
Route::middleware(['auth'])->get('/directeur', function () {
    // Vérifie MANUELLEMENT
    $user = auth()->user();
    $allowed = ['superadmin@gedf.com', 'admin@gedf.com', 'directeur@gedf.com'];
    
    if (!in_array($user->email, $allowed)) {
        abort(403, 'Accès réservé aux directeurs');
    }
    
    $parapheursASigner = DB::table('parapheurs')
        ->join('parapheur_statuts', 'parapheurs.statut_id', '=', 'parapheur_statuts.id')
        ->where('parapheur_statuts.code', 'attente_signature')
        ->count();
    
    return view('dashboards.directeur', compact('parapheursASigner'));
})->name('directeur.dashboard');

// =============================================================================
// MODULE COURRIERS
// =============================================================================
Route::middleware(['auth'])->group(function () {
    // Module Courrier - Routes principales (suivant les conventions Laravel)
    Route::prefix('courriers')->name('courriers.')->group(function () {
        Route::get('/archives', [CourrierController::class, 'archives'])->name('archives');
        // Routes RESTful standards
        Route::get('/', [CourrierController::class, 'index'])->name('index');
        Route::get('/create', [CourrierController::class, 'create'])->name('create'); // Changé 'enregistrer' en 'create'
        Route::post('/', [CourrierController::class, 'store'])->name('store');
        Route::get('/{courrier}', [CourrierController::class, 'show'])->name('show');
        Route::get('/{courrier}/edit', [CourrierController::class, 'edit'])->name('edit');
        Route::put('/{courrier}', [CourrierController::class, 'update'])->name('update');
        Route::delete('/{courrier}', [CourrierController::class, 'destroy'])->name('destroy');
        
        // Routes métier spécifiques à la procédure DRS
        Route::post('/{courrier}/attribuer', [CourrierController::class, 'attribuer'])->name('attribuer');
        Route::post('/{courrier}/upload-document', [CourrierController::class, 'uploadDocument'])->name('upload-document');
        Route::get('/{courrier}/historique', [CourrierController::class, 'historique'])->name('historique');
        Route::post('/{courrier}/changer-statut', [CourrierController::class, 'changerStatut'])->name('changer-statut');
        Route::post('/{courrier}/deposer-parapheur', [CourrierController::class, 'deposerParapheur'])->name('deposer-parapheur');
    });
});
// =============================================================================
// MODULE PARAPHEURS
// =============================================================================
Route::middleware(['auth'])->prefix('parapheurs')->name('parapheurs.')->group(function () {
    
    // Page d'accueil simple des parapheurs
    Route::get('/', [ParapheurController::class, 'index'])->name('index');
    // Vue SECRÉTAIRE
    Route::middleware(['auth'])->group(function () {
        Route::get('/secretaire', function () {
            $user = auth()->user();
            $allowed = ['superadmin@gedf.com', 'admin@gedf.com', 'secretaire@gedf.com'];
            
            if (!in_array($user->email, $allowed)) {
                abort(403, 'Accès réservé au secrétariat');
            }
            
            return app(ParapheurController::class)->vueSecretaire();
        })->name('secretaire');
        
        Route::get('/a-saisir', [ParapheurController::class, 'aSaisir'])->name('a.saisir');
        Route::get('/rejetes', [ParapheurController::class, 'rejetes'])->name('rejetes');
        Route::get('/create', [ParapheurController::class, 'create'])->name('create');
        Route::post('/', [ParapheurController::class, 'store'])->name('store');
        Route::post('/{parapheur}/transmettre-agent', [ParapheurController::class, 'transmettreAgent'])->name('transmettre.agent');
    });
    
    // Vue AGENT/GESTIONNAIRE
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
    
    // Vue CHEF SERVICE
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
    
    // Vue DIRECTEUR
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
    
    // SUPERVISION (Admin/Superadmin)
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
    
    // ROUTES AVEC PARAMÈTRES (EN DERNIER)
    Route::get('/{parapheur}', [ParapheurController::class, 'show'])->name('show');
    Route::get('/{parapheur}/edit', [ParapheurController::class, 'edit'])->name('edit');
    Route::put('/{parapheur}', [ParapheurController::class, 'update'])->name('update');
});

// =============================================================================
// MODULES ADMINISTRATIFS
// =============================================================================
Route::middleware(['auth'])->prefix('administration')->group(function () {
    Route::get('/utilisateurs', function () {
        $user = auth()->user();
        
        if ($user->email !== 'superadmin@gedf.com') {
            abort(403, 'Accès réservé au superadmin');
        }
        
        return app(AdminController::class)->index();
    })->name('admin.utilisateurs');
    
    Route::get('/roles', [AdminController::class, 'roles'])->name('admin.roles');
    Route::get('/parametres', [AdminController::class, 'parametres'])->name('admin.parametres');
    Route::get('/audit', [AdminController::class, 'audit'])->name('admin.audit');
});

// =============================================================================
// ROUTE TEST DÉFINITIVE
// =============================================================================
Route::middleware(['auth'])->get('/test-final', function() {
    $user = auth()->user();
    
    if ($user->email !== 'superadmin@gedf.com') {
        abort(403, 'Test échoué: ' . $user->email);
    }
    
    return "🎉 TEST FINAL RÉUSSI ! Email: " . $user->email;
});

// =============================================================================
// GESTION DES SERVICES (DRS)
// =============================================================================
Route::middleware(['auth'])->prefix('admin/services')->name('admin.services.')->group(function () {
    // Routes CRUD standards
    Route::get('/', [App\Http\Controllers\Admin\ServiceController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\Admin\ServiceController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\Admin\ServiceController::class, 'store'])->name('store');
    Route::get('/{service}', [App\Http\Controllers\Admin\ServiceController::class, 'show'])->name('show');
    Route::get('/{service}/edit', [App\Http\Controllers\Admin\ServiceController::class, 'edit'])->name('edit');
    Route::put('/{service}', [App\Http\Controllers\Admin\ServiceController::class, 'update'])->name('update');
    Route::delete('/{service}', [App\Http\Controllers\Admin\ServiceController::class, 'destroy'])->name('destroy');
    
    // Routes supplémentaires pour activer/désactiver
    Route::post('/{service}/activer', [App\Http\Controllers\Admin\ServiceController::class, 'activer'])->name('activer');
    Route::post('/{service}/desactiver', [App\Http\Controllers\Admin\ServiceController::class, 'desactiver'])->name('desactiver');
});

// =============================================================================
// FALLBACK
// =============================================================================
Route::fallback(function () {
    // Redirige vers le dashboard principal
    return redirect()->route('dashboard');
});

// =============================================================================
// ROUTES D'ADMINISTRATION (TOUTES DANS LE MÊME GROUPE)
// =============================================================================
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Admin\AdminController::class, 'index'])->name('dashboard');
    Route::get('/', [App\Http\Controllers\Admin\AdminController::class, 'index'])->name('home');
    
    // Services DRS
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
    
    // Statistiques (AVEC LE BON CONTROLLER)
    Route::prefix('statistiques')->name('statistiques.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\StatistiqueController::class, 'index'])->name('index');
        Route::get('/periode', [App\Http\Controllers\Admin\StatistiqueController::class, 'parPeriode'])->name('periode');
        Route::get('/export', [App\Http\Controllers\Admin\StatistiqueController::class, 'export'])->name('export');
    });
    
    // Audit
    Route::get('/audit', [App\Http\Controllers\Admin\AdminController::class, 'audit'])->name('audit-logs');
    
    // Utilisateurs
    Route::get('/utilisateurs', [App\Http\Controllers\Admin\AdminController::class, 'utilisateurs'])->name('users-list');
    
    // Rôles
    Route::get('/roles', [App\Http\Controllers\Admin\AdminController::class, 'roles'])->name('roles-list');
    
    // Paramètres
    Route::get('/parametres', [App\Http\Controllers\Admin\AdminController::class, 'parametres'])->name('settings');
    
    // Cache
    Route::post('/clear-cache', [App\Http\Controllers\Admin\AdminController::class, 'clearCache'])->name('clear-cache');
    
    // Recherche
    Route::get('/search', [App\Http\Controllers\Admin\AdminController::class, 'search'])->name('search');
});