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

    if (!auth()->user()->hasRole('superadmin')) {
        abort(403, 'Accès réservé au super administrateur.');
    }
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

                    // Statistiques par service - Version corrigée avec service_emetteur_id
        try {
            $services = Service::all();
            $statsParService = $services->map(function($service) {
                // Compter les courriers liés à ce service via service_emetteur_id
                $totalCourriers = DB::table('courriers')
                    ->where('service_emetteur_id', $service->id)
                    ->count();
                
                $courriersEnCours = DB::table('courriers')
                    ->where('service_emetteur_id', $service->id)
                    ->whereIn('statut', ['en_attente', 'en_cours', 'en_parapheur'])
                    ->count();
                
                $usersCount = DB::table('users')
                    ->where('service_id', $service->id)
                    ->count();
                
                return (object)[
                    'id' => $service->id,
                    'code' => $service->code,
                    'nom' => $service->nom,
                    'parapheurs_total' => $totalCourriers,
                    'parapheurs_en_cours' => $courriersEnCours,
                    'utilisateurs_count' => $usersCount,
                ];
            });
        } catch (\Exception $e) {
            $statsParService = collect([]);
        }

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
    // Récupérer tous les utilisateurs avec leurs rôles et services
    $users = User::with(['service'])
        ->orderBy('created_at', 'desc')
        ->get(); // Utilise get() au lieu de paginate() pour tester
    
    // Debug : afficher le nombre d'utilisateurs
    \Log::info('Nombre d\'utilisateurs : ' . $users->count());
    
    return view('administration.utilisateurs', compact('users'));
}

   /**
 * Affiche la liste des rôles
 */
public function roles()
{
    // Version simple : récupère tous les rôles
    $roles = DB::table('roles')->get();
    
    // Pour chaque rôle, compte le nombre d'utilisateurs via la table pivot
    foreach ($roles as $role) {
        $role->users_count = DB::table('model_has_roles')
            ->where('role_id', $role->id)
            ->count();
        $role->permissions_count = DB::table('role_has_permissions')
            ->where('role_id', $role->id)
            ->count();
    }
    
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
        /**
     * Affiche le formulaire de création d'utilisateur
     */
    public function utilisateursCreate()
    {
        $services = Service::where('actif', 1)->orderBy('code')->get();
        
        return view('administration.utilisateurs-create', compact('services'));
    }

    /**
     * Enregistre un nouvel utilisateur
     */
    public function utilisateursStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'matricule' => 'required|string|unique:users,matricule',
            'poste' => 'required|string|max:255',
            'role_id' => 'required|exists:roles,id',
            'service_id' => 'nullable|exists:services,id',
            'password' => 'required|min:8|confirmed',
            'actif' => 'boolean',
        ]);

        // Créer l'utilisateur
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'matricule' => $validated['matricule'],
            'poste' => $validated['poste'],
            'service_id' => $validated['service_id'],
            'actif' => $validated['actif'] ?? 1,
        ]);

        // Assigner le rôle via Spatie
        $role = DB::table('roles')->where('id', $validated['role_id'])->first();
        if ($role) {
            $user->assignRole($role->name);
        }

        return redirect()->route('admin.utilisateurs')
            ->with('success', "Utilisateur {$user->name} créé avec succès");
    }
    
        /**
     * Affiche le formulaire de création d'un rôle
     */
    public function rolesCreate()
    {
        return view('administration.roles-create');
    }

    /**
     * Enregistre un nouveau rôle
     */
    public function rolesStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        DB::table('roles')->insert([
            'name' => $validated['name'],
            'guard_name' => 'web',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.roles-list')
            ->with('success', "Rôle '{$validated['name']}' créé avec succès");
    }

    /**
     * Affiche le formulaire d'édition d'un rôle
     */
    public function rolesEdit($id)
    {
        $role = DB::table('roles')->where('id', $id)->first();
        if (!$role) {
            return redirect()->route('admin.roles-list')->with('error', 'Rôle non trouvé');
        }
        
        return view('administration.roles-edit', compact('role'));
    }

    /**
     * Met à jour un rôle
     */
    public function rolesUpdate(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
        ]);

        DB::table('roles')->where('id', $id)->update([
            'name' => $validated['name'],
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.roles-list')
            ->with('success', "Rôle mis à jour avec succès");
    }

    /**
     * Supprime un rôle
     */
    public function rolesDestroy($id)
    {
        $role = DB::table('roles')->where('id', $id)->first();
        
        // Vérifier si le rôle a des utilisateurs
        $hasUsers = DB::table('model_has_roles')->where('role_id', $id)->exists();
        
        if ($hasUsers) {
            return back()->with('error', 'Impossible de supprimer ce rôle car il est assigné à des utilisateurs');
        }
        
        DB::table('roles')->where('id', $id)->delete();
        
        return redirect()->route('admin.roles-list')
            ->with('success', "Rôle '{$role->name}' supprimé avec succès");
    }

    /**
     * Affiche les permissions d'un rôle
     */
    public function rolesPermissions($id)
    {
        $role = DB::table('roles')->where('id', $id)->first();
        $permissions = DB::table('permissions')->get();
        
        // Récupérer les permissions déjà assignées
        $assignedPermissions = DB::table('role_has_permissions')
            ->where('role_id', $id)
            ->pluck('permission_id')
            ->toArray();
        
        return view('administration.roles-permissions', compact('role', 'permissions', 'assignedPermissions'));
    }

    /**
     * Met à jour les permissions d'un rôle
     */
    public function rolesPermissionsUpdate(Request $request, $id)
    {
        // Supprimer toutes les permissions existantes
        DB::table('role_has_permissions')->where('role_id', $id)->delete();
        
        // Ajouter les nouvelles permissions
        if ($request->has('permissions')) {
            foreach ($request->permissions as $permissionId) {
                DB::table('role_has_permissions')->insert([
                    'role_id' => $id,
                    'permission_id' => $permissionId,
                ]);
            }
        }
        
        return redirect()->route('admin.roles-list')
            ->with('success', 'Permissions mises à jour avec succès');
    }

        /**
     * Affiche le formulaire d'édition d'un utilisateur
     */
    public function utilisateursEdit($id)
    {
        $user = User::with('roles')->findOrFail($id);
        $services = Service::where('actif', 1)->orderBy('code')->get();
        
        return view('administration.utilisateurs-edit', compact('user', 'services'));
    }

    /**
     * Met à jour un utilisateur
     */
    public function utilisateursUpdate(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'matricule' => 'required|string|unique:users,matricule,' . $id,
            'poste' => 'required|string|max:255',
            'role_id' => 'required|exists:roles,id',
            'service_id' => 'nullable|exists:services,id',
            'password' => 'nullable|min:8|confirmed',
            'actif' => 'boolean',
        ]);

        // Mettre à jour les données
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->matricule = $validated['matricule'];
        $user->poste = $validated['poste'];
        $user->service_id = $validated['service_id'];
        $user->actif = $validated['actif'] ?? 0;
        
        // Changer le mot de passe si fourni
        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }
        
        $user->save();
        
        // Mettre à jour le rôle
        $role = DB::table('roles')->where('id', $validated['role_id'])->first();
        if ($role) {
            $user->syncRoles([$role->name]);
        }
        
        return redirect()->route('admin.utilisateurs')
            ->with('success', "Utilisateur {$user->name} modifié avec succès");
    }
}