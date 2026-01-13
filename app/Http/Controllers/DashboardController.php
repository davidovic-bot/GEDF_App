<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use App\Models\Courrier;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistiques pour le dashboard
        $stats = [
            'total_dossiers' => Courrier::count(),
            'dossiers_en_cours' => Courrier::whereIn('statut', ['en_analyse', 'en_validation'])->count(),
            'dossiers_en_retard' => Courrier::where('date_limite', '<', Carbon::now())
                                          ->whereIn('statut', ['en_analyse', 'en_validation'])
                                          ->count(),
            'dossiers_signes' => Courrier::where('statut', 'signe')->count(),
            'dossiers_archives' => Courrier::where('statut', 'archive')->count(),
        ];
        
        // Dossiers en retard
        $dossiers_retard = Courrier::where('date_limite', '<', Carbon::now())
                                  ->whereIn('statut', ['en_analyse', 'en_validation'])
                                  ->orderBy('date_limite', 'asc')
                                  ->limit(10)
                                  ->get();
        
        // Derniers dossiers créés
        $derniers_dossiers = Courrier::with('createur')
                                    ->orderBy('created_at', 'desc')
                                    ->limit(10)
                                    ->get();
        
        return view('dashboard', compact('stats', 'dossiers_retard', 'derniers_dossiers'));
    }
}