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
use Illuminate\Support\Facades\Validator;

class ParapheurController extends Controller
{
    /**
     * Redirection principale selon le rôle
     */
    public function index()
    {
        try {
            // Récupère les parapheurs avec les relations
            $parapheurs = \App\Models\Parapheur::with(['courrier'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
            
            // Statistiques basiques
            $stats = [
                'total' => \App\Models\Parapheur::count(),
                'en_attente' => 0, // À adapter selon tes statuts
                'en_retard' => 0,  // À adapter
            ];
            
        } catch (\Exception $e) {
            // Version de secours si erreur
            $parapheurs = [];
            $stats = [
                'total' => 0,
                'en_attente' => 0,
                'en_retard' => 0,
            ];
        }
        
        return view('parapheurs.index', compact('parapheurs', 'stats'));
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
        $types = [
            'exoneration' => 'Exonération ouverte (TVA, CSS, etc.)',
            'dispense'    => 'Dispense ouverte (TVA, CSS, etc.)',
        ];

        return view('parapheurs.create', compact('types'));
    }

    /**
     * STORE - Création d'un parapheur (CORRIGÉE)
     */
    public function store(Request $request)
{
    // Définir des valeurs par défaut
    $request->merge([
        'objet' => $request->objet ?? 'Demande d\'exonération',
        'expediteur' => $request->expediteur ?? 'Contribuable',
        'priorite' => $request->priorite ?? 'normale',
    ]);

    // Validation
    $validator = Validator::make($request->all(), [
        'objet' => 'nullable|string|max:500',
        'type_attestation' => 'required|in:exoneration_tva,dispense_tva,exoneration_css,dispense_css',
        'expediteur' => 'nullable|string|max:255',
        'service_expediteur' => 'required|string|max:255',
        'date_reception' => 'required|date',
        'date_limite' => 'required|date|after_or_equal:date_reception',
        'priorite' => 'nullable|in:basse,normale,haute,urgente',
    ]);

    if ($validator->fails()) {
        return back()->withErrors($validator)->withInput();
    }

    // Générer la référence
    $statutCreer = ParapheurStatut::where('code', 'creer')->first();
    if (!$statutCreer) {
        return back()->with('error', 'Statut "creer" introuvable.');
    }

    $reference = 'PAR-' . date('Ymd') . '-' . str_pad(Parapheur::count() + 1, 4, '0', STR_PAD_LEFT);

    try {
        $parapheur = Parapheur::create([
            'reference' => $reference,
            'objet' => $request->objet,
            'type_attestation' => $request->type_attestation,
            'expediteur' => $request->expediteur,
            'service_expediteur' => $request->service_expediteur,
            'date_reception' => $request->date_reception,
            'date_limite' => $request->date_limite,
            'date_creation' => now()->format('Y-m-d'), // ⭐ AJOUTÉ
            'date_echeance' => $request->date_limite, // ⭐ AJOUTÉ
            'priorite' => $request->priorite,
            'statut_id' => $statutCreer->id,
            'created_by' => Auth::id(),
            'createur_id' => Auth::id(), // ⭐ AJOUTÉ
            'current_role_id' => Auth::user()->role_id ?? 1,
            'service_id' => 1,
            'direction_id' => 1,
        ]);

        ParapheurHistorique::create([
            'parapheur_id' => $parapheur->id,
            'user_id' => Auth::id(),
            'action' => 'Création du parapheur',
            'nouveau_statut_id' => $statutCreer->id,
            'commentaire' => 'Parapheur créé par le secrétariat'
        ]);

        return redirect()->route('parapheurs.index')
            ->with('success', 'Parapheur créé avec succès ! Réf: ' . $reference);

    } catch (\Exception $e) {
        return back()->with('error', 'Erreur : ' . $e->getMessage())->withInput();
    }
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
            'current_role_id' => null,
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
        $parapheurs = Parapheur::with(['statut', 'typeCourrier', 'createur', 'courrier'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        $stats = [
            'total' => Parapheur::count(),
            'en_attente' => Parapheur::whereHas('statut', function($q) {
                $q->where('code', 'like', 'attente%');
            })->count(),
            'en_cours' => Parapheur::whereHas('statut', function($q) {
                $q->whereIn('code', ['creer', 'analyse', 'valide_cs']);
            })->count(),
            'termines' => Parapheur::whereHas('statut', function($q) {
                $q->whereIn('code', ['signe', 'archive']);
            })->count(),
        ];
        
        return view('parapheurs.supervision', compact('parapheurs', 'stats'));
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
        
        $parapheur->load(['statut', 'typeCourrier', 'createur', 'currentRole', 'fichiers', 'courrier']);
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

    // ============================================================
    // NOUVELLES MÉTHODES POUR LE CIRCUIT 2 (PARAPHEUR)
    // ============================================================

    /**
     * Afficher le formulaire de dépôt des pièces justificatives
     */
    public function deposerPieces(Parapheur $parapheur)
    {
        // Vérifier que le courrier associé est signé (exonération ouverte)
        if ($parapheur->courrier && !in_array($parapheur->courrier->statut_general, ['signe', 'valide', 'termine'])) {
            return back()->with('error', 'Le courrier doit être signé avant de déposer des pièces dans le parapheur.');
        }
        
        return view('parapheurs.deposer-pieces', compact('parapheur'));
    }

    /**
     * Enregistrer les pièces justificatives (tableau + factures)
     */
    public function storePieces(Request $request, Parapheur $parapheur)
    {
        $request->validate([
            'tableau' => 'required|file|mimes:pdf|max:10240',
            'factures.*' => 'required|file|mimes:pdf|max:10240',
        ]);
        
        try {
            // Upload du tableau
            $tableauPath = $request->file('tableau')->store('parapheurs/tableaux', 'public');
            
            // Upload des factures
            $facturesPaths = [];
            foreach ($request->file('factures') as $index => $file) {
                $facturesPaths[] = [
                    'nom' => $file->getClientOriginalName(),
                    'chemin' => $file->store('parapheurs/factures', 'public'),
                    'taille' => $file->getSize(),
                    'upload_le' => now()->toDateTimeString(),
                ];
            }
            
            // Récupérer le statut "analyse"
            $statutAnalyse = ParapheurStatut::where('code', 'analyse')->first();
            if (!$statutAnalyse) {
                return back()->with('error', 'Le statut "analyse" n\'existe pas. Veuillez vérifier vos statuts.');
            }
            
            $roleAgent = DB::table('roles')->where('name', 'agent')->first();
            
            // Mise à jour du parapheur
            $parapheur->update([
                'tableau_factures' => $tableauPath,
                'factures' => json_encode($facturesPaths),
                'statut_id' => $statutAnalyse->id,
                'current_role_id' => $roleAgent->id ?? null,
            ]);
            
            // Historique
            ParapheurHistorique::create([
                'parapheur_id' => $parapheur->id,
                'user_id' => auth()->id(),
                'action' => 'Dépôt des pièces justificatives',
                'commentaire' => 'Tableau et ' . count($facturesPaths) . ' facture(s) déposées dans le parapheur'
            ]);
            
            return redirect()->route('parapheurs.verifier-factures', $parapheur)
                ->with('success', 'Pièces déposées avec succès. Veuillez vérifier les factures.');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors du dépôt : ' . $e->getMessage());
        }
    }

    /**
     * Vérifier les factures (Agent)
     */
    public function verifierFactures(Parapheur $parapheur)
    {
        // Vérifier que l'utilisateur a le bon rôle
        if (!auth()->user()->hasAnyRoles(['agent', 'gestionnaire', 'admin', 'superadmin'])) {
            abort(403, 'Accès réservé aux agents.');
        }
        
        $factures = json_decode($parapheur->factures, true) ?? [];
        
        return view('parapheurs.verifier-factures', compact('parapheur', 'factures'));
    }

    /**
     * Valider les factures et enregistrer les montants (Agent)
     */
    public function validerFactures(Request $request, Parapheur $parapheur)
    {
        $request->validate([
            'montant_tva' => 'required|numeric|min:0',
            'montant_css' => 'nullable|numeric|min:0',
            'commentaire' => 'nullable|string|max:1000',
        ]);
        
        try {
            // Calcul du total
            $montantTotal = $request->montant_tva + ($request->montant_css ?? 0);
            
            // Mise à jour des montants
            $parapheur->update([
                'montant_tva' => $request->montant_tva,
                'montant_css' => $request->montant_css,
                'montant_total' => $montantTotal,
                'verifie_par' => auth()->id(),
                'verifie_le' => now(),
            ]);
            
            // Changement de statut : attente validation chef
            $statutAttenteValidation = ParapheurStatut::where('code', 'attente_validation')->first();
            if (!$statutAttenteValidation) {
                return back()->with('error', 'Le statut "attente_validation" n\'existe pas.');
            }
            
            $roleChefService = DB::table('roles')->where('name', 'chef_service')->first();
            
            $parapheur->update([
                'statut_id' => $statutAttenteValidation->id,
                'current_role_id' => $roleChefService->id ?? null,
            ]);
            
            // Historique
            ParapheurHistorique::create([
                'parapheur_id' => $parapheur->id,
                'user_id' => auth()->id(),
                'action' => 'Vérification des factures',
                'commentaire' => 'Factures vérifiées. TVA: ' . number_format($request->montant_tva, 0, ',', ' ') . ' FCFA' . 
                                ($request->montant_css ? ', CSS: ' . number_format($request->montant_css, 0, ',', ' ') . ' FCFA' : '') .
                                '. Total: ' . number_format($montantTotal, 0, ',', ' ') . ' FCFA.' .
                                ($request->commentaire ? ' Commentaire: ' . $request->commentaire : '')
            ]);
            
            return redirect()->route('parapheurs.controle-regularite', $parapheur)
                ->with('success', 'Factures vérifiées. Transmission au Chef de Service pour contrôle.');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la validation : ' . $e->getMessage());
        }
    }

    /**
     * Contrôle de régularité (Chef de Service)
     */
    public function controleRegularite(Parapheur $parapheur)
    {
        // Vérifier que l'utilisateur a le bon rôle
        if (!auth()->user()->hasAnyRoles(['chef_service', 'admin', 'superadmin'])) {
            abort(403, 'Accès réservé aux chefs de service.');
        }
        
        return view('parapheurs.controle-regularite', compact('parapheur'));
    }

    /**
     * Valider le contrôle de régularité (Chef de Service)
     */
    public function validerControle(Request $request, Parapheur $parapheur)
    {
        $request->validate([
            'commentaire' => 'nullable|string|max:1000',
        ]);
        
        try {
            $statutAttenteSignature = ParapheurStatut::where('code', 'attente_signature')->first();
            if (!$statutAttenteSignature) {
                return back()->with('error', 'Le statut "attente_signature" n\'existe pas.');
            }
            
            $roleDirecteur = DB::table('roles')->where('name', 'directeur')->first();
            
            $parapheur->update([
                'statut_id' => $statutAttenteSignature->id,
                'current_role_id' => $roleDirecteur->id ?? null,
                'controle_par' => auth()->id(),
                'controle_le' => now(),
            ]);
            
            // Historique
            ParapheurHistorique::create([
                'parapheur_id' => $parapheur->id,
                'user_id' => auth()->id(),
                'action' => 'Contrôle de régularité',
                'commentaire' => $request->commentaire ?? 'Contrôle validé. Transmission au Directeur pour visa final.'
            ]);
            
            return redirect()->route('parapheurs.visa-final', $parapheur)
                ->with('success', 'Contrôle validé. Transmission au Directeur pour visa final.');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors du contrôle : ' . $e->getMessage());
        }
    }

    /**
     * Visa final (Directeur)
     */
    public function visaFinal(Parapheur $parapheur)
    {
        // Vérifier que l'utilisateur a le bon rôle
        if (!auth()->user()->hasAnyRoles(['directeur', 'admin', 'superadmin'])) {
            abort(403, 'Accès réservé aux directeurs.');
        }
        
        return view('parapheurs.visa-final', compact('parapheur'));
    }

    /**
     * Apposer le visa final (Directeur)
     */
    public function apposerVisaFinal(Request $request, Parapheur $parapheur)
    {
        $request->validate([
            'commentaire' => 'nullable|string|max:1000',
        ]);
        
        try {
            $statutSigne = ParapheurStatut::where('code', 'signe')->first();
            if (!$statutSigne) {
                return back()->with('error', 'Le statut "signe" n\'existe pas.');
            }
            
            $parapheur->update([
                'statut_id' => $statutSigne->id,
                'current_role_id' => null,
                'visa_final_par' => auth()->id(),
                'visa_final_le' => now(),
            ]);
            
            // Mettre à jour le statut du courrier associé
            if ($parapheur->courrier) {
                $parapheur->courrier->update(['statut_general' => 'termine']);
            }
            
            // Historique
            ParapheurHistorique::create([
                'parapheur_id' => $parapheur->id,
                'user_id' => auth()->id(),
                'action' => 'Visa final',
                'commentaire' => $request->commentaire ?? 'Visa final apposé. Dossier terminé.'
            ]);
            
            return redirect()->route('parapheurs.show', $parapheur)
                ->with('success', 'Visa final apposé avec succès. Dossier terminé !');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors du visa final : ' . $e->getMessage());
        }
    }

    /**
     * Rejeter les pièces (Agent ou Chef ou Directeur)
     */
    public function rejeterPieces(Request $request, Parapheur $parapheur)
    {
        $request->validate([
            'motif' => 'required|string|min:5|max:1000',
        ]);
        
        try {
            $statutRejete = ParapheurStatut::where('code', 'rejete')->first();
            if (!$statutRejete) {
                return back()->with('error', 'Le statut "rejete" n\'existe pas.');
            }
            
            // Déterminer qui peut faire ce rejet
            $user = auth()->user();
            $roleName = $user->role->name ?? '';
            
            // Le rejet retourne au rôle précédent ou au secrétariat
            $roleCible = DB::table('roles')->where('name', 'secretaire')->first();
            
            $parapheur->update([
                'statut_id' => $statutRejete->id,
                'current_role_id' => $roleCible->id ?? null,
                'motif_rejet' => $request->motif,
            ]);
            
            // Historique
            ParapheurHistorique::create([
                'parapheur_id' => $parapheur->id,
                'user_id' => auth()->id(),
                'action' => 'Rejet des pièces',
                'commentaire' => 'Rejet par ' . $roleName . '. Motif: ' . $request->motif
            ]);
            
            return redirect()->route('parapheurs.show', $parapheur)
                ->with('warning', 'Pièces rejetées. Motif: ' . $request->motif);
                
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors du rejet : ' . $e->getMessage());
        }
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