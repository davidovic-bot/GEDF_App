<?php

namespace App\Http\Controllers;

use App\Models\Courrier;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $serviceId = $user->service_id;

        // Requête de base : filtrer par service si utilisateur a un service (agent ou chef)
        if ($user->hasRole(['agent', 'chef_service']) && $serviceId) {
            $courriers = Courrier::where('service_emetteur_id', $serviceId);
        } else {
            $courriers = Courrier::query(); // superadmin voit tout
        }

        // Statistiques
        $stats = [
            'total_dossiers' => (clone $courriers)->count(),
            'dossiers_en_cours' => (clone $courriers)->whereIn('statut_general', ['en_analyse', 'en_validation'])->count(),
            'dossiers_en_retard' => (clone $courriers)
                ->where('date_traitement', '<', Carbon::now())
                ->whereIn('statut_general', ['en_analyse', 'en_validation'])
                ->count(),
            'dossiers_signes' => (clone $courriers)->where('statut_general', 'signe')->count(),
            'dossiers_archives' => (clone $courriers)->where('statut_general', 'archive')->count(),
        ];

        // Dossiers en retard
        $dossiers_retard = (clone $courriers)
            ->where('date_traitement', '<', Carbon::now())
            ->whereIn('statut_general', ['en_analyse', 'en_validation'])
            ->orderBy('date_traitement', 'asc')
            ->limit(10)
            ->get();

        // Derniers dossiers créés
        $derniers_dossiers = (clone $courriers)
            ->with('createur')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard', compact('stats', 'dossiers_retard', 'derniers_dossiers'));
    }
}