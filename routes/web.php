<?php

use App\http\Controllers\CourrierController;
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
// DASHBOARDS PAR RÔLE
// =============================================================================

Route::middleware(['auth', 'issuperadmin'])->get('/dashboard/superadmin', function () {
    return view('dashboards.superadmin');
})->name('dashboard.superadmin');

Route::middleware(['auth', 'isadmin'])->get('/dashboard/admin', function () {
    return view('dashboards.admin');
})->name('dashboard.admin');

Route::middleware(['auth', 'issecretaire'])->get('/dashboard/secretaire', function () {
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
})->name('dashboard.secretaire');

Route::middleware(['auth', 'isgestionnaire'])->get('/dashboard/gestionnaire', function () {
    $parapheursAAnalyser = DB::table('parapheurs')
        ->join('parapheur_statuts', 'parapheurs.statut_id', '=', 'parapheur_statuts.id')
        ->where('parapheur_statuts.code', 'analyse')
        ->count();
    
    return view('dashboards.gestionnaire', compact('parapheursAAnalyser'));
})->name('dashboard.gestionnaire');

Route::middleware(['auth', 'ischefservice'])->get('/dashboard/chef-service', function () {
    $parapheursAValider = DB::table('parapheurs')
        ->join('parapheur_statuts', 'parapheurs.statut_id', '=', 'parapheur_statuts.id')
        ->where('parapheur_statuts.code', 'attente_validation')
        ->count();
    
    return view('dashboards.chef-service', compact('parapheursAValider'));
})->name('dashboard.chefservice');

Route::middleware(['auth', 'isdirecteur'])->get('/dashboard/directeur', function () {
    $parapheursASigner = DB::table('parapheurs')
        ->join('parapheur_statuts', 'parapheurs.statut_id', '=', 'parapheur_statuts.id')
        ->where('parapheur_statuts.code', 'attente_signature')
        ->count();
    
    return view('dashboards.directeur', compact('parapheursASigner'));
})->name('dashboard.directeur');

// Authentification Breeze
require __DIR__.'/auth.php';


// =============================================================================
// MODULE COURRIERS
// =============================================================================

// Route pour le module Courrier (Superadmin uniquement)
Route::middleware(['auth', 'issuperadmin'])->get('/courriers', function () {
    return "<h1 style='padding: 20px;'>📨 Module Courriers Superadmin</h1>
            <div style='padding: 20px; background: #f8f9fa; border-radius: 10px;'>
                <p>Module fonctionnel !</p>
                <p><a href='/dashboard/superadmin'>← Retour au tableau de bord</a></p>
            </div>";
})->name('courriers');

// Après les routes d'authentification de Breeze
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // Module Courrier - Routes principales
    Route::prefix('courriers')->name('courriers.')->group(function () {
        Route::get('/', [CourrierController::class, 'index'])->name('index');
        Route::get('/enregistrer', [CourrierController::class, 'enregistrer'])->name('enregistrer');
        Route::post('/', [CourrierController::class, 'store'])->name('store');
        Route::get('/{courrier}', [CourrierController::class, 'show'])->name('show');
        Route::get('/{courrier}/edit', [CourrierController::class, 'edit'])->name('edit');
        Route::put('/{courrier}', [CourrierController::class, 'update'])->name('update');
        Route::delete('/{courrier}', [CourrierController::class, 'destroy'])->name('destroy');
        
        // Routes spécifiques au métier
        Route::post('/{courrier}/attribuer', [CourrierController::class, 'attribuer'])->name('attribuer');
        Route::post('/{courrier}/upload-document', [CourrierController::class, 'uploadDocument'])->name('upload-document');
        Route::get('/{courrier}/historique', [CourrierController::class, 'historique'])->name('historique');
        Route::post('/{courrier}/changer-statut', [CourrierController::class, 'changerStatut'])->name('changer-statut');
        Route::post('/{courrier}/deposer-parapheur', [CourrierController::class, 'deposerParapheur'])->name('deposer-parapheur');
    });
});



// =============================================================================
// MODULE PARAPHEURS - ORDRE CORRECT : SPÉCIFIQUE → GÉNÉRIQUE
// =============================================================================

Route::middleware(['auth'])->prefix('parapheurs')->name('parapheurs.')->group(function () {
    
    // ====================
    // 1. ROUTES SPÉCIFIQUES (DOIVENT ÊTRE EN PREMIER)
    // ====================
    
    // Redirection selon le rôle
    Route::get('/', function () {
        $user = auth()->user();
        switch ($user->role->name) {
            case 'secretaire':
                return redirect()->route('parapheurs.secretaire');
            case 'agent':
            case 'gestionnaire':
                return redirect()->route('parapheurs.agent');
            case 'chef_service':
                return redirect()->route('parapheurs.chef_service');
            case 'directeur':
                return redirect()->route('parapheurs.directeur');
            default:
                return redirect()->route('parapheurs.supervision');
        }
    })->name('index');
    
    // Vue SECRÉTAIRE
    Route::middleware(['issecretaire'])->group(function () {
        Route::get('/secretaire', [ParapheurController::class, 'vueSecretaire'])->name('secretaire');
        Route::get('/a-saisir', [ParapheurController::class, 'aSaisir'])->name('a.saisir');
        Route::get('/rejetes', [ParapheurController::class, 'rejetes'])->name('rejetes');
        Route::get('/create', [ParapheurController::class, 'create'])->name('create');
        Route::post('/', [ParapheurController::class, 'store'])->name('store');
        Route::post('/{parapheur}/transmettre-agent', [ParapheurController::class, 'transmettreAgent'])->name('transmettre.agent');
    });
    
    // Vue AGENT/GESTIONNAIRE
    Route::middleware(['isgestionnaire'])->group(function () {
        Route::get('/agent', [ParapheurController::class, 'vueAgent'])->name('agent');
        Route::get('/a-analyser', [ParapheurController::class, 'aAnalyser'])->name('a.analyser');
        Route::post('/{parapheur}/transmettre-chef-service', [ParapheurController::class, 'transmettreChefService'])->name('transmettre.chef_service');
        Route::post('/{parapheur}/rejeter-vers-secretaire', [ParapheurController::class, 'rejeterVersSecretaire'])->name('rejeter.secretaire');
    });
    
    // Vue CHEF SERVICE
    Route::middleware(['ischefservice'])->group(function () {
        Route::get('/chef-service', [ParapheurController::class, 'vueChefService'])->name('chef_service');
        Route::get('/a-valider', [ParapheurController::class, 'aValider'])->name('a.valider');
        Route::post('/{parapheur}/valider', [ParapheurController::class, 'valider'])->name('valider');
        Route::post('/{parapheur}/rejeter-vers-agent', [ParapheurController::class, 'rejeterVersAgent'])->name('rejeter.agent');
        Route::post('/{parapheur}/transmettre-directeur', [ParapheurController::class, 'transmettreDirecteur'])->name('transmettre.directeur');
    });
    
    // Vue DIRECTEUR
    Route::middleware(['isdirecteur'])->group(function () {
        Route::get('/directeur', [ParapheurController::class, 'vueDirecteur'])->name('directeur');
        Route::get('/a-signer', [ParapheurController::class, 'aSigner'])->name('a.signer');
        Route::post('/{parapheur}/signer', [ParapheurController::class, 'signer'])->name('signer');
        Route::post('/{parapheur}/rejeter-exceptionnel', [ParapheurController::class, 'rejeterExceptionnel'])->name('rejeter.exceptionnel');
    });
    
    // ====================
    // 2. SUPERVISION (Admin/Superadmin) - AVANT les routes avec paramètres
    // ====================
    
    Route::middleware(['issuperadmin'])->group(function () {
        Route::get('/supervision', [ParapheurController::class, 'supervision'])->name('supervision');
        Route::post('/{parapheur}/archiver', [ParapheurController::class, 'archiver'])->name('archiver');
        Route::get('/historique/{parapheur}', [ParapheurController::class, 'historique'])->name('historique');
    });
    
    // ====================
    // 3. ROUTES AVEC PARAMÈTRES (DOIVENT ÊTRE EN DERNIER)
    // ====================
    
    Route::get('/{parapheur}', [ParapheurController::class, 'show'])->name('show');
    Route::get('/{parapheur}/edit', [ParapheurController::class, 'edit'])->name('edit');
    Route::put('/{parapheur}', [ParapheurController::class, 'update'])->name('update');
});

// =============================================================================
// MODULES ADMINISTRATIFS
// =============================================================================

Route::middleware(['auth', 'issuperadmin'])->prefix('administration')->group(function () {
    Route::get('/utilisateurs', [AdminController::class, 'index'])->name('admin.utilisateurs');
    Route::get('/roles', [AdminController::class, 'roles'])->name('admin.roles');
    Route::get('/parametres', [AdminController::class, 'parametres'])->name('admin.parametres');
    Route::get('/audit', [AdminController::class, 'audit'])->name('admin.audit');
});

Route::middleware(['auth', 'issuperadmin'])->prefix('statistiques')->group(function () {
    Route::get('/', [StatistiqueController::class, 'index'])->name('statistiques.index');
});


// Fallback
Route::fallback(function () {
    return redirect()->route('dashboard.superadmin');
});