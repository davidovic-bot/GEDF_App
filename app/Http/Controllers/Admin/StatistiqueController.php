<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Parapheur;
use App\Models\User;
use App\Models\Service;
use App\Models\Courrier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatistiqueController extends Controller
{
    public function index()
    {
        // Statistiques générales
        $stats = [
            'parapheurs' => [
                'total' => Parapheur::count(),
                'par_statut' => [
                    'brouillon' => Parapheur::where('statut', 'brouillon')->count(),
                    'en_attente' => Parapheur::where('statut', 'en_attente')->count(),
                    'en_cours' => Parapheur::where('statut', 'en_cours')->count(),
                    'valide' => Parapheur::where('statut', 'valide')->count(),
                    'rejete' => Parapheur::where('statut', 'rejete')->count(),
                    'archive' => Parapheur::where('statut', 'archive')->count(),
                ]
            ],
            'courriers' => [
                'total' => Courrier::count(),
            ],
            'utilisateurs' => [
                'total' => User::count(),
                'actifs' => User::where('actif', 1)->count(),
            ]
        ];

        // Évolution mensuelle
        $evolution = Parapheur::select(
                DB::raw('MONTH(created_at) as mois'),
                DB::raw('YEAR(created_at) as annee'),
                DB::raw('count(*) as total')
            )
            ->whereYear('created_at', date('Y'))
            ->groupBy('annee', 'mois')
            ->orderBy('annee')
            ->orderBy('mois')
            ->get();

        // Top utilisateurs
        $topUsers = User::withCount('parapheurs')
            ->orderBy('parapheurs_count', 'desc')
            ->limit(5)
            ->get();

        // Statistiques par service
        $parService = Service::withCount('parapheurs')
            ->orderBy('parapheurs_count', 'desc')
            ->get();

        return view('administration.statistiques', compact(
            'stats', 
            'evolution', 
            'topUsers', 
            'parService'
        ));
    }

    public function parPeriode(Request $request)
    {
        $debut = $request->get('debut', now()->startOfMonth());
        $fin = $request->get('fin', now()->endOfMonth());

        $data = Parapheur::whereBetween('created_at', [$debut, $fin])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as total')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($data);
    }

    public function export()
    {
        // Logique d'export Excel/PDF
        return redirect()->back()->with('info', 'Export en cours de développement');
    }
}