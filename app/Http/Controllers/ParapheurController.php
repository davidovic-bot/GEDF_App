<?php

namespace App\Http\Controllers;

use App\Models\Parapheur;
use App\Models\ParapheurStatut;
use App\Models\TypeCourrier;
use App\Models\ParapheurHistorique;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ParapheurController extends Controller
{
    /**
     * Redirection principale selon le rôle
     */
    public function index()
    {
        $user = Auth::user();
        $roleName = $user->role->name;
        
        switch ($roleName) {
            case 'secretaire':
                return redirect()->route('parapheurs.secretaire');
            case 'agent':
            case 'gestionnaire':
                return redirect()->route('parapheurs.agent');
            case 'chef_service':
                return redirect()->route('parapheurs.chef_service');
            case 'directeur':
                return redirect()->route('parapheurs.directeur');
            case 'admin':
            case 'superadmin':
                return redirect()->route('parapheurs.supervision');
            default:
                return redirect()->route('dashboard.' . $roleName);
        }
    }

    /**
     * VUE SECRÉTAIRE
     */
    public function vueSecretaire()
    {
        $parapheurs = Parapheur::with(['statut', 'typeCourrier'])
            ->whereIn('statut_id', function($query) {
                $query->select('id')
                    ->from('parapheur_statuts')
                    ->whereIn('code', ['creer', 'rejete']);
            })
            ->where('created_by', Auth::id())
            ->orderBy('date_limite')
            ->paginate(20);
        
        return view('parapheurs.secretaire', compact('parapheurs'));
    }
    
    public function aSaisir()
    {
        $statutCreer = ParapheurStatut::where('code', 'creer')->first();
        
        $parapheurs = Parapheur::with(['typeCourrier'])
            ->where('statut_id', $statutCreer->id)
            ->where('created_by', Auth::id())
            ->orderBy('date_limite')
            ->paginate(20);
        
        return view('parapheurs.a-saisir', compact('parapheurs'));
    }
    
    public function rejetes()
    {
        $statutRejete = ParapheurStatut::where('code', 'rejete')->first();
        
        $parapheurs = Parapheur::with(['typeCourrier'])
            ->where('statut_id', $statutRejete->id)
            ->where('created_by', Auth::id())
            ->orderBy('date_limite')
            ->paginate(20);
        
        return view('parapheurs.rejetes', compact('parapheurs'));
    }
    
    public function create()
    {
        $types = TypeCourrier::where('actif', true)->get();
        return view('parapheurs.create', compact('types'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'objet' => 'required|string|max:500',
            'type_courrier_id' => 'required|exists:type_courriers,id',
            'expediteur' => 'required|string|max:255',
            'service_expediteur' => 'required|string|max:255',
            'date_reception' => 'required|date',
            'date_limite' => 'required|date|after_or_equal:date_reception',
            'priorite' => 'required|in:bas,normal,urgent',
        ]);
        
        // Générer la référence
        $statutCreer = ParapheurStatut::where('code', 'creer')->first();
        $reference = 'PAR-' . date('Ymd') . '-' . str_pad(Parapheur::count() + 1, 4, '0', STR_PAD_LEFT);
        
        $parapheur = Parapheur::create([
            'reference' => $reference,
            'objet' => $request->objet,
            'type_courrier_id' => $request->type_courrier_id,
            'expediteur' => $request->expediteur,
            'service_expediteur' => $request->service_expediteur,
            'date_reception' => $request->date_reception,
            'date_limite' => $request->date_limite,
            'priorite' => $request->priorite,
            'statut_id' => $statutCreer->id,
            'created_by' => Auth::id(),
            'current_role_id' => Auth::user()->role_id, // Secrétaire
        ]);
        
        // Historique
        ParapheurHistorique::create([
            'parapheur_id' => $parapheur->id,
            'user_id' => Auth::id(),
            'action' => 'Création du parapheur',
            'nouveau_statut_id' => $statutCreer->id,
            'commentaire' => 'Parapheur créé par le secrétariat'
        ]);
        
        return redirect()->route('parapheurs.show', $parapheur)
            ->with('success', 'Parapheur créé avec succès !');
    }
    
    public function transmettreAgent(Parapheur $parapheur)
    {
        // Vérifier que l'utilisateur peut faire cette action
        if (Auth::user()->role->name !== 'secretaire') {
            abort(403);
        }
        
        $statutAnalyse = ParapheurStatut::where('code', 'analyse')->first();
        $roleAgent = DB::table('roles')->where('name', 'agent')->first();
        
        $parapheur->update([
            'statut_id' => $statutAnalyse->id,
            'current_role_id' => $roleAgent->id,
        ]);
        
        // Historique
        ParapheurHistorique::create([
            'parapheur_id' => $parapheur->id,
            'user_id' => Auth::id(),
            'action' => 'Transmis à l\'agent',
            'ancien_statut_id' => $parapheur->statut_id,
            'nouveau_statut_id' => $statutAnalyse->id,
            'commentaire' => 'Parapheur transmis pour analyse'
        ]);
        
        return redirect()->route('parapheurs.show', $parapheur)
            ->with('success', 'Parapheur transmis à l\'agent pour analyse.');
    }

    /**
     * VUE AGENT/GESTIONNAIRE
     */
    public function vueAgent()
    {
        $statutAnalyse = ParapheurStatut::where('code', 'analyse')->first();
        
        $parapheurs = Parapheur::with(['statut', 'typeCourrier', 'createur'])
            ->where('statut_id', $statutAnalyse->id)
            ->orderBy('date_limite')
            ->paginate(20);
        
        return view('parapheurs.agent', compact('parapheurs'));
    }
    
    public function aAnalyser()
    {
        $statutAnalyse = ParapheurStatut::where('code', 'analyse')->first();
        
        $parapheurs = Parapheur::with(['typeCourrier', 'createur'])
            ->where('statut_id', $statutAnalyse->id)
            ->orderBy('date_limite')
            ->paginate(20);
        
        return view('parapheurs.a-analyser', compact('parapheurs'));
    }
    
    public function transmettreChefService(Request $request, Parapheur $parapheur)
    {
        if (!in_array(Auth::user()->role->name, ['agent', 'gestionnaire'])) {
            abort(403);
        }
        
        $request->validate([
            'commentaire' => 'nullable|string|max:1000'
        ]);
        
        $statutAttenteValidation = ParapheurStatut::where('code', 'attente_validation')->first();
        $roleChefService = DB::table('roles')->where('name', 'chef_service')->first();
        
        $parapheur->update([
            'statut_id' => $statutAttenteValidation->id,
            'current_role_id' => $roleChefService->id,
        ]);
        
        ParapheurHistorique::create([
            'parapheur_id' => $parapheur->id,
            'user_id' => Auth::id(),
            'action' => 'Transmis au Chef de Service',
            'ancien_statut_id' => $parapheur->statut_id,
            'nouveau_statut_id' => $statutAttenteValidation->id,
            'commentaire' => $request->commentaire ?? 'Analyse terminée, transmis pour validation'
        ]);
        
        return redirect()->route('parapheurs.show', $parapheur)
            ->with('success', 'Parapheur transmis au Chef de Service pour validation.');
    }
    
    public function rejeterVersSecretaire(Request $request, Parapheur $parapheur)
    {
        if (!in_array(Auth::user()->role->name, ['agent', 'gestionnaire'])) {
            abort(403);
        }
        
        $request->validate([
            'motif' => 'required|string|max:1000'
        ]);
        
        $statutRejete = ParapheurStatut::where('code', 'rejete')->first();
        $roleSecretaire = DB::table('roles')->where('name', 'secretaire')->first();
        
        $parapheur->update([
            'statut_id' => $statutRejete->id,
            'current_role_id' => $roleSecretaire->id,
        ]);
        
        ParapheurHistorique::create([
            'parapheur_id' => $parapheur->id,
            'user_id' => Auth::id(),
            'action' => 'Rejeté vers secrétariat',
            'ancien_statut_id' => $parapheur->statut_id,
            'nouveau_statut_id' => $statutRejete->id,
            'commentaire' => 'Motif: ' . $request->motif
        ]);
        
        return redirect()->route('parapheurs.show', $parapheur)
            ->with('warning', 'Parapheur rejeté vers le secrétariat pour correction.');
    }

    /**
     * VUE CHEF SERVICE
     */
    public function vueChefService()
    {
        $statuts = ParapheurStatut::whereIn('code', ['attente_validation', 'valide_cs'])->pluck('id');
        
        $parapheurs = Parapheur::with(['statut', 'typeCourrier', 'createur'])
            ->whereIn('statut_id', $statuts)
            ->orderBy('date_limite')
            ->paginate(20);
        
        return view('parapheurs.chef-service', compact('parapheurs'));
    }
    
    public function aValider()
    {
        $statutAttenteValidation = ParapheurStatut::where('code', 'attente_validation')->first();
        
        $parapheurs = Parapheur::with(['typeCourrier', 'createur'])
            ->where('statut_id', $statutAttenteValidation->id)
            ->orderBy('date_limite')
            ->paginate(20);
        
        return view('parapheurs.a-valider', compact('parapheurs'));
    }
    
    public function valider(Request $request, Parapheur $parapheur)
    {
        if (Auth::user()->role->name !== 'chef_service') {
            abort(403);
        }
        
        $request->validate([
            'commentaire' => 'nullable|string|max:1000'
        ]);
        
        $statutValideCS = ParapheurStatut::where('code', 'valide_cs')->first();
        
        $parapheur->update([
            'statut_id' => $statutValideCS->id,
        ]);
        
        ParapheurHistorique::create([
            'parapheur_id' => $parapheur->id,
            'user_id' => Auth::id(),
            'action' => 'Validé par Chef Service',
            'ancien_statut_id' => $parapheur->statut_id,
            'nouveau_statut_id' => $statutValideCS->id,
            'commentaire' => $request->commentaire ?? 'Validation du Chef de Service'
        ]);
        
        return redirect()->route('parapheurs.show', $parapheur)
            ->with('success', 'Parapheur validé par le Chef de Service.');
    }
    
    public function transmettreDirecteur(Request $request, Parapheur $parapheur)
    {
        if (Auth::user()->role->name !== 'chef_service') {
            abort(403);
        }
        
        $request->validate([
            'commentaire' => 'nullable|string|max:1000'
        ]);
        
        $statutAttenteSignature = ParapheurStatut::where('code', 'attente_signature')->first();
        $roleDirecteur = DB::table('roles')->where('name', 'directeur')->first();
        
        $parapheur->update([
            'statut_id' => $statutAttenteSignature->id,
            'current_role_id' => $roleDirecteur->id,
        ]);
        
        ParapheurHistorique::create([
            'parapheur_id' => $parapheur->id,
            'user_id' => Auth::id(),
            'action' => 'Transmis au Directeur',
            'ancien_statut_id' => $parapheur->statut_id,
            'nouveau_statut_id' => $statutAttenteSignature->id,
            'commentaire' => $request->commentaire ?? 'Transmis pour signature'
        ]);
        
        return redirect()->route('parapheurs.show', $parapheur)
            ->with('success', 'Parapheur transmis au Directeur pour signature.');
    }
    
    public function rejeterVersAgent(Request $request, Parapheur $parapheur)
    {
        if (Auth::user()->role->name !== 'chef_service') {
            abort(403);
        }
        
        $request->validate([
            'motif' => 'required|string|max:1000'
        ]);
        
        $statutRejete = ParapheurStatut::where('code', 'rejete')->first();
        $roleAgent = DB::table('roles')->where('name', 'agent')->first();
        
        $parapheur->update([
            'statut_id' => $statutRejete->id,
            'current_role_id' => $roleAgent->id,
        ]);
        
        ParapheurHistorique::create([
            'parapheur_id' => $parapheur->id,
            'user_id' => Auth::id(),
            'action' => 'Rejeté vers agent',
            'ancien_statut_id' => $parapheur->statut_id,
            'nouveau_statut_id' => $statutRejete->id,
            'commentaire' => 'Motif: ' . $request->motif
        ]);
        
        return redirect()->route('parapheurs.show', $parapheur)
            ->with('warning', 'Parapheur rejeté vers l\'agent pour correction.');
    }

    /**
     * VUE DIRECTEUR
     */
    public function vueDirecteur()
    {
        $statutAttenteSignature = ParapheurStatut::where('code', 'attente_signature')->first();
        
        $parapheurs = Parapheur::with(['statut', 'typeCourrier', 'createur'])
            ->where('statut_id', $statutAttenteSignature->id)
            ->orderBy('date_limite')
            ->paginate(20);
        
        return view('parapheurs.directeur', compact('parapheurs'));
    }
    
    public function aSigner()
    {
        $statutAttenteSignature = ParapheurStatut::where('code', 'attente_signature')->first();
        
        $parapheurs = Parapheur::with(['typeCourrier', 'createur'])
            ->where('statut_id', $statutAttenteSignature->id)
            ->orderBy('date_limite')
            ->paginate(20);
        
        return view('parapheurs.a-signer', compact('parapheurs'));
    }
    
    public function signer(Request $request, Parapheur $parapheur)
    {
        if (Auth::user()->role->name !== 'directeur') {
            abort(403);
        }
        
        $request->validate([
            'commentaire' => 'nullable|string|max:1000'
        ]);
        
        $statutSigne = ParapheurStatut::where('code', 'signe')->first();
        
        $parapheur->update([
            'statut_id' => $statutSigne->id,
            'current_role_id' => null, // Plus personne en charge
        ]);
        
        ParapheurHistorique::create([
            'parapheur_id' => $parapheur->id,
            'user_id' => Auth::id(),
            'action' => 'Signé par le Directeur',
            'ancien_statut_id' => $parapheur->statut_id,
            'nouveau_statut_id' => $statutSigne->id,
            'commentaire' => $request->commentaire ?? 'Signature du Directeur'
        ]);
        
        return redirect()->route('parapheurs.show', $parapheur)
            ->with('success', 'Parapheur signé avec succès !');
    }
    
    public function rejeterExceptionnel(Request $request, Parapheur $parapheur)
    {
        if (Auth::user()->role->name !== 'directeur') {
            abort(403);
        }
        
        $request->validate([
            'motif' => 'required|string|max:1000'
        ]);
        
        $statutRejete = ParapheurStatut::where('code', 'rejete')->first();
        $roleSecretaire = DB::table('roles')->where('name', 'secretaire')->first();
        
        $parapheur->update([
            'statut_id' => $statutRejete->id,
            'current_role_id' => $roleSecretaire->id,
        ]);
        
        ParapheurHistorique::create([
            'parapheur_id' => $parapheur->id,
            'user_id' => Auth::id(),
            'action' => 'Rejeté exceptionnellement par le Directeur',
            'ancien_statut_id' => $parapheur->statut_id,
            'nouveau_statut_id' => $statutRejete->id,
            'commentaire' => 'Motif (Directeur): ' . $request->motif
        ]);
        
        return redirect()->route('parapheurs.show', $parapheur)
            ->with('warning', 'Parapheur rejeté exceptionnellement.');
    }

    /**
     * VUE SUPERVISION (Superadmin/Admin)
     */
    public function supervision()
    {
        $parapheurs = Parapheur::with(['statut', 'typeCourrier', 'createur', 'currentRole'])
            ->orderBy('date_limite')
            ->paginate(30);
        
        $stats = [
            'total' => Parapheur::count(),
            'par_statut' => DB::table('parapheurs')
                ->join('parapheur_statuts', 'parapheurs.statut_id', '=', 'parapheur_statuts.id')
                ->select('parapheur_statuts.nom', DB::raw('count(*) as total'))
                ->groupBy('parapheur_statuts.nom')
                ->get(),
        ];
        
        return view('parapheurs.supervision', compact('parapheurs', 'stats'));
    }
    
    public function historique(Parapheur $parapheur)
    {
        $historique = ParapheurHistorique::with(['user', 'ancienStatut', 'nouveauStatut'])
            ->where('parapheur_id', $parapheur->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('parapheurs.historique', compact('parapheur', 'historique'));
    }
    
    public function archiver(Parapheur $parapheur)
    {
        $statutArchive = ParapheurStatut::where('code', 'archive')->first();
        
        $parapheur->update([
            'statut_id' => $statutArchive->id,
            'current_role_id' => null,
        ]);
        
        ParapheurHistorique::create([
            'parapheur_id' => $parapheur->id,
            'user_id' => Auth::id(),
            'action' => 'Archivé',
            'ancien_statut_id' => $parapheur->statut_id,
            'nouveau_statut_id' => $statutArchive->id,
            'commentaire' => 'Archivage manuel par l\'administrateur'
        ]);
        
        return redirect()->route('parapheurs.supervision')
            ->with('success', 'Parapheur archivé avec succès.');
    }

    /**
     * ROUTES COMMUNES
     */
    public function show(Parapheur $parapheur)
    {
        // Vérifier les permissions
        $user = Auth::user();
        $peutVoir = $this->verifierPermissionVoir($user, $parapheur);
        
        if (!$peutVoir) {
            abort(403, 'Vous n\'avez pas accès à ce parapheur.');
        }
        
        $parapheur->load(['statut', 'typeCourrier', 'createur', 'currentRole', 'fichiers']);
        $historique = ParapheurHistorique::with(['user', 'ancienStatut', 'nouveauStatut'])
            ->where('parapheur_id', $parapheur->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Récupérer les actions possibles pour cet utilisateur
        $actionsPossibles = $this->getActionsPossibles($user, $parapheur);
        
        return view('parapheurs.show', compact('parapheur', 'historique', 'actionsPossibles'));
    }
    
    public function edit(Parapheur $parapheur)
    {
        // Seul le créateur peut éditer si statut "créé" ou "rejeté"
        if (Auth::id() !== $parapheur->created_by || 
            !in_array($parapheur->statut->code, ['creer', 'rejete'])) {
            abort(403);
        }
        
        $types = TypeCourrier::where('actif', true)->get();
        return view('parapheurs.edit', compact('parapheur', 'types'));
    }
    
    public function update(Request $request, Parapheur $parapheur)
    {
        // Vérification des permissions
        if (Auth::id() !== $parapheur->created_by || 
            !in_array($parapheur->statut->code, ['creer', 'rejete'])) {
            abort(403);
        }
        
        $request->validate([
            'objet' => 'required|string|max:500',
            'type_courrier_id' => 'required|exists:type_courriers,id',
            'expediteur' => 'required|string|max:255',
            'service_expediteur' => 'required|string|max:255',
            'date_reception' => 'required|date',
            'date_limite' => 'required|date|after_or_equal:date_reception',
            'priorite' => 'required|in:bas,normal,urgent',
        ]);
        
        $parapheur->update($request->only([
            'objet', 'type_courrier_id', 'expediteur', 
            'service_expediteur', 'date_reception', 'date_limite', 'priorite'
        ]));
        
        ParapheurHistorique::create([
            'parapheur_id' => $parapheur->id,
            'user_id' => Auth::id(),
            'action' => 'Modification',
            'commentaire' => 'Parapheur modifié par le créateur'
        ]);
        
        return redirect()->route('parapheurs.show', $parapheur)
            ->with('success', 'Parapheur mis à jour avec succès.');
    }

    /**
     * MÉTHODES PRIVÉES
     */
    private function verifierPermissionVoir($user, $parapheur)
    {
        $roleName = $user->role->name;
        
        // Superadmin/Admin voit tout
        if (in_array($roleName, ['superadmin', 'admin'])) {
            return true;
        }
        
        // Le créateur voit toujours son parapheur
        if ($parapheur->created_by === $user->id) {
            return true;
        }
        
        // Vérification par rôle et statut
        $statutCode = $parapheur->statut->code;
        
        switch ($roleName) {
            case 'secretaire':
                return in_array($statutCode, ['creer', 'rejete']);
            case 'agent':
            case 'gestionnaire':
                return $statutCode === 'analyse';
            case 'chef_service':
                return in_array($statutCode, ['attente_validation', 'valide_cs']);
            case 'directeur':
                return $statutCode === 'attente_signature';
            default:
                return false;
        }
    }
    
    private function getActionsPossibles($user, $parapheur)
    {
        $roleName = $user->role->name;
        $statutCode = $parapheur->statut->code;
        $actions = [];
        
        // Vérifier les transitions autorisées
        $transitions = DB::table('parapheur_transitions as t')
            ->join('parapheur_statuts as s', 't.statut_source_id', '=', 's.id')
            ->join('parapheur_statuts as c', 't.statut_cible_id', '=', 'c.id')
            ->join('roles as r', 't.role_id', '=', 'r.id')
            ->where('s.code', $statutCode)
            ->where('r.name', $roleName)
            ->select('c.code as statut_cible', 't.action')
            ->get();
        
        foreach ($transitions as $transition) {
            $actions[$transition->statut_cible] = $transition->action;
        }
        
        // Actions supplémentaires
        if ($user->id === $parapheur->created_by && in_array($statutCode, ['creer', 'rejete'])) {
            $actions['edit'] = 'Modifier';
        }
        
        // Superadmin peut archiver
        if ($roleName === 'superadmin' && $statutCode !== 'archive') {
            $actions['archive'] = 'Archiver';
        }
        
        return $actions;
    }
}