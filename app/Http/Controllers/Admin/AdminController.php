<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Service;
use App\Models\Parapheur;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class AdminController extends Controller
{
    /**
     * Affiche le tableau de bord d'administration
     */
    public function index()
    {
        // Statistiques générales
        $stats = [
            'users' => [
                'total' => User::count(),
                'actifs' => User::where('actif', 1)->count(),
                'nouveaux' => User::whereDate('created_at', today())->count(),
            ],
            'roles' => [
                'total' => Role::count(),
                'liste' => Role::pluck('name'),
            ],
            'services' => [
                'total' => Service::count(),
            ],
            'parapheurs' => [
                'total' => Parapheur::count(),
                'en_cours' => Parapheur::whereIn('statut', [
                    'en_attente', 
                    'en_cours'
                ])->count(),
                'valides' => Parapheur::where('statut', 'valide')->count(),
                'signes' => 0,
                'rejetes' => Parapheur::where('statut', 'rejete')->count(),
                'en_retard' => Parapheur::where('statut', 'en_retard')->count(),
                'brouillons' => Parapheur::where('statut', 'brouillon')->count(),
            ],
        ];

        // Activité récente (avec gestion d'erreur)
        try {
            $recentActivity = AuditLog::with('utilisateur')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            $recentActivity = collect([]);
        }

               // Statistiques par service en utilisant les accesseurs du modèle
        $services = Service::with(['users', 'courriers'])->get();
        
        $statsParService = $services->map(function($service) {
            return (object)[
                'id' => $service->id,
                'code' => $service->code,
                'nom' => $service->nom,
                'parapheurs_total' => $service->nombre_courriers,
                'parapheurs_en_cours' => $service->courriers_en_cours,
                'utilisateurs_count' => $service->nombre_utilisateurs,
            ];
        });

        // Derniers utilisateurs inscrits
        $recentUsers = User::orderBy('created_at', 'desc')->limit(5)->get();

        // Derniers parapheurs
        $recentParapheurs = Parapheur::with(['service'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('administration.dashboard', compact(
            'stats', 
            'recentActivity', 
            'statsParService',
            'recentUsers',
            'recentParapheurs'
        ));
    }

    /**
     * Affiche les logs d'audit
     */
    public function audit()
    {
        try {
            $logs = AuditLog::with('utilisateur')
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        } catch (\Exception $e) {
            $logs = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
        }

        return view('administration.audit', compact('logs'));
    }

    /**
     * Affiche la gestion des utilisateurs
     */
    public function utilisateurs()
    {
        $users = User::with(['service', 'roles'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('administration.utilisateurs', compact('users'));
    }

    /**
     * Affiche la gestion des rôles
     */
    public function roles()
    {
        $roles = Role::withCount('users')
            ->orderBy('name')
            ->get();

        return view('administration.roles', compact('roles'));
    }

    /**
     * Affiche les paramètres
     */
    public function parametres()
    {
        return view('administration.parametres');
    }

    /**
     * Nettoie le cache
     */
    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');
            
            return back()->with('success', 'Cache nettoyé avec succès !');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors du nettoyage du cache : ' . $e->getMessage());
        }
    }

    /**
     * Statistiques avancées
     */
    public function statistiques()
    {
        // Évolution des parapheurs par mois
        $evolutionParapheurs = Parapheur::select(
                DB::raw('MONTH(created_at) as mois'),
                DB::raw('YEAR(created_at) as annee'),
                DB::raw('count(*) as total')
            )
            ->whereYear('created_at', date('Y'))
            ->groupBy('annee', 'mois')
            ->orderBy('annee')
            ->orderBy('mois')
            ->get();

        // Top 5 des utilisateurs les plus actifs
        $topUsers = User::withCount('validations')
            ->orderBy('validations_count', 'desc')
            ->limit(5)
            ->get();

        // Répartition des parapheurs par service
        $parapheursParService = Service::withCount('parapheurs')
            ->orderBy('parapheurs_count', 'desc')
            ->get();

        return view('administration.statistiques', compact(
            'evolutionParapheurs', 
            'topUsers',
            'parapheursParService'
        ));
    }

    /**
     * Recherche globale
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $results = [];

        // Recherche dans les utilisateurs
        $users = User::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->limit(5)
            ->get()
            ->map(function($user) {
                return [
                    'type' => 'Utilisateur',
                    'title' => $user->name,
                    'subtitle' => $user->email,
                    'url' => route('admin.users-list'), // À modifier selon tes routes
                    'icon' => 'person'
                ];
            });

        $results = array_merge($results, $users->toArray());

        // Recherche dans les parapheurs (corrigé : reference au lieu de numero_parapheur)
        $parapheurs = Parapheur::where('reference', 'like', "%{$query}%")
            ->limit(5)
            ->get()
            ->map(function($p) {
                return [
                    'type' => 'Parapheur',
                    'title' => $p->reference,
                    'subtitle' => $p->statut,
                    'url' => route('parapheurs.show', $p->id),
                    'icon' => 'folder'
                ];
            });

        $results = array_merge($results, $parapheurs->toArray());

        return response()->json($results);
    }
}