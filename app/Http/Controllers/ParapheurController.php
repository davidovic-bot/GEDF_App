<?php

namespace App\Http\Controllers;

use App\Models\Parapheur;
use App\Models\Service;
use App\Models\Direction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ParapheurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            // Vérifier d'abord si la table existe
            if (!\Schema::hasTable('parapheurs')) {
                throw new \Exception("La table 'parapheurs' n'existe pas. Exécutez les migrations.");
            }
            
            // Initialiser la requête
            $query = Parapheur::query();
            
            $user = auth()->user();
            
            // FILTRAGE PAR RÔLE
            if ($user->hasRole('superadmin')) {
                // Superadmin voit TOUT - pas de restriction
            } 
            elseif ($user->hasRole('directeur')) {
                // Directeur : voir les parapheurs de sa direction
                if ($user->direction_id) {
                    $query->where('direction_id', $user->direction_id);
                }
            }
            elseif ($user->hasRole('chef_service') || $user->hasRole('chef')) {
                // Chef de service : voir les parapheurs de son service
                if ($user->service_id) {
                    $query->where('service_id', $user->service_id);
                }
            }
            else {
                // Agents et autres : voir leurs propres parapheurs
                $query->where(function($q) use ($user) {
                    $q->where('createur_id', $user->id)
                      ->orWhere('responsable_actuel_id', $user->id);
                });
            }
            
            // FILTRES PARAMÉTRABLES
            if ($request->filled('statut')) {
                $query->where('statut', $request->statut);
            }
            
            if ($request->filled('priorite')) {
                $query->where('priorite', $request->priorite);
            }
            
            if ($request->filled('service_id')) {
                $query->where('service_id', $request->service_id);
            }
            
            if ($request->filled('date_debut')) {
                $query->where('date_creation', '>=', $request->date_debut);
            }
            
            if ($request->filled('date_fin')) {
                $query->where('date_creation', '<=', $request->date_fin);
            }
            
            // Recherche texte
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('reference', 'LIKE', "%{$search}%")
                      ->orWhere('objet', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%");
                });
            }
            
            // Tri
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);
            
            // Pagination
            $parapheurs = $query->paginate(20)->withQueryString();
            
            // Données pour les filtres (superadmin)
            $services = $user->hasRole('superadmin') ? Service::where('actif', true)->get() : collect([]);
            $directions = $user->hasRole('superadmin') ? Direction::where('actif', true)->get() : collect([]);
            
            return view('parapheurs.index', compact('parapheurs', 'services', 'directions'));
            
        } catch (\Exception $e) {
            // Mode dégradé - données de test
            $parapheursTest = $this->getDonneesTest();
            
            return view('parapheurs.index', [
                'parapheurs' => $parapheursTest,
                'services' => collect([]),
                'directions' => collect([]),
                'error_message' => 'Mode démo - ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $services = Service::where('actif', true)->get();
        $directions = Direction::where('actif', true)->get();
        
        return view('parapheurs.create', compact('services', 'directions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'objet' => 'required|string|max:500',
            'description' => 'required|string',
            'service_id' => 'required|exists:services,id',
            'direction_id' => 'required|exists:directions,id',
            'date_echeance' => 'required|date|after:today',
            'priorite' => 'required|in:basse,normale,haute,urgente',
            'confidentialite' => 'sometimes|in:standard,confidentiel,tres_confidentiel',
            'fichiers.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,png'
        ]);
        
        DB::beginTransaction();
        
        try {
            // Générer référence
            $reference = $this->genererReference();
            
            // Déterminer le responsable initial (chef de service par défaut)
            $responsableInitial = $this->getResponsableInitial($request->service_id, $request->direction_id);
            
            $parapheur = Parapheur::create([
                'reference' => $reference,
                'objet' => $request->objet,
                'description' => $request->description,
                'priorite' => $request->priorite,
                'confidentialite' => $request->confidentialite ?? 'standard',
                'date_creation' => now(),
                'date_echeance' => $request->date_echeance,
                'createur_id' => auth()->id(),
                'service_id' => $request->service_id,
                'direction_id' => $request->direction_id,
                'statut' => 'en_attente',
                'etape_actuelle' => 1,
                'etapes_total' => 3, // À configurer
                'responsable_actuel_id' => $responsableInitial,
                'workflow' => 'standard_drs'
            ]);
            
            // Gestion fichiers
            if ($request->hasFile('fichiers')) {
                foreach ($request->file('fichiers') as $fichier) {
                    $this->stockerFichier($parapheur, $fichier);
                }
            }
            
            // Historique
            $this->creerHistorique($parapheur, 'creation', 'Parapheur créé', auth()->id());
            
            DB::commit();
            
            return redirect()->route('parapheurs.show', $parapheur)
                            ->with('success', 'Parapheur créé avec succès. Référence: ' . $reference);
                            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                        ->with('error', 'Erreur lors de la création: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $parapheur = Parapheur::with([
                'createur', 
                'service', 
                'direction', 
                'responsableActuel',
                'fichiers',
                'historiques.user'
            ])->findOrFail($id);
            
            // Vérifier les permissions
            $this->authorizeView($parapheur);
            
            return view('parapheurs.show', compact('parapheur'));
            
        } catch (\Exception $e) {
            // Mode test
            $parapheurTest = $this->getParapheurTest($id);
            return view('parapheurs.show', [
                'parapheur' => $parapheurTest,
                'message' => 'Mode démo - ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $parapheur = Parapheur::findOrFail($id);
            
            // Vérifier permissions
            $this->authorizeEdit($parapheur);
            
            $services = Service::where('actif', true)->get();
            $directions = Direction::where('actif', true)->get();
            
            return view('parapheurs.edit', compact('parapheur', 'services', 'directions'));
            
        } catch (\Exception $e) {
            return redirect()->route('parapheurs.index')
                            ->with('error', 'Parapheur non trouvé ou non modifiable');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $parapheur = Parapheur::findOrFail($id);
            
            // Vérifier permissions
            $this->authorizeEdit($parapheur);
            
            $request->validate([
                'objet' => 'required|string|max:500',
                'description' => 'required|string',
                'service_id' => 'required|exists:services,id',
                'direction_id' => 'required|exists:directions,id',
                'date_echeance' => 'required|date|after:today',
                'priorite' => 'required|in:basse,normale,haute,urgente',
                'confidentialite' => 'sometimes|in:standard,confidentiel,tres_confidentiel'
            ]);
            
            $parapheur->update([
                'objet' => $request->objet,
                'description' => $request->description,
                'priorite' => $request->priorite,
                'confidentialite' => $request->confidentialite ?? $parapheur->confidentialite,
                'date_echeance' => $request->date_echeance,
                'service_id' => $request->service_id,
                'direction_id' => $request->direction_id
            ]);
            
            $this->creerHistorique($parapheur, 'modification', 'Parapheur modifié', auth()->id());
            
            return redirect()->route('parapheurs.show', $parapheur)
                            ->with('success', 'Parapheur mis à jour avec succès.');
                            
        } catch (\Exception $e) {
            return back()->withInput()
                        ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $parapheur = Parapheur::findOrFail($id);
            
            // Seul superadmin ou créateur peut supprimer (si brouillon)
            if (!auth()->user()->hasRole('superadmin') && 
                ($parapheur->createur_id !== auth()->id() || $parapheur->statut !== 'brouillon')) {
                abort(403, 'Action non autorisée.');
            }
            
            $parapheur->delete();
            
            return redirect()->route('parapheurs.index')
                            ->with('success', 'Parapheur supprimé avec succès.');
                            
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // ACTIONS WORKFLOW
    // =========================================================================
    
    /**
     * Valider une étape
     */
    public function valider(Request $request, $id)
    {
        $request->validate([
            'commentaire' => 'nullable|string|max:1000'
        ]);
        
        try {
            $parapheur = Parapheur::findOrFail($id);
            
            // Vérifier que l'utilisateur est le responsable actuel
            if ($parapheur->responsable_actuel_id !== auth()->id()) {
                throw new \Exception('Vous n\'êtes pas le responsable de ce parapheur.');
            }
            
            // Calculer la prochaine étape
            $prochaineEtape = $parapheur->etape_actuelle + 1;
            
            // Vérifier si c'est la dernière étape
            $nouveauStatut = $prochaineEtape >= $parapheur->etapes_total ? 'valide' : 'en_cours';
            
            // Déterminer le prochain responsable
            $prochainResponsable = $this->getProchainResponsable($parapheur, $prochaineEtape);
            
            DB::beginTransaction();
            
            $parapheur->update([
                'etape_actuelle' => $prochaineEtape,
                'statut' => $nouveauStatut,
                'responsable_actuel_id' => $prochainResponsable,
                'date_validation' => $nouveauStatut === 'valide' ? now() : null
            ]);
            
            $details = "Étape {$parapheur->etape_actuelle} validée" . 
                      ($request->commentaire ? " - " . $request->commentaire : "");
            
            $this->creerHistorique($parapheur, 'validation', $details, auth()->id());
            
            DB::commit();
            
            return back()->with('success', 'Étape validée avec succès.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }
    
    /**
     * Rejeter un parapheur
     */
    public function rejeter(Request $request, $id)
    {
        $request->validate([
            'motif' => 'required|string|min:10|max:2000'
        ]);
        
        try {
            $parapheur = Parapheur::findOrFail($id);
            
            // Vérifier que l'utilisateur est le responsable actuel
            if ($parapheur->responsable_actuel_id !== auth()->id()) {
                throw new \Exception('Vous n\'êtes pas le responsable de ce parapheur.');
            }
            
            DB::beginTransaction();
            
            $parapheur->update([
                'statut' => 'rejete',
                'motif_rejet' => $request->motif,
                'date_rejet' => now()
            ]);
            
            $details = "Parapheur rejeté - Motif: " . $request->motif;
            $this->creerHistorique($parapheur, 'rejet', $details, auth()->id());
            
            DB::commit();
            
            return back()->with('success', 'Parapheur rejeté avec succès.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }
    
    /**
     * Transmettre à l'étape suivante
     */
    public function transmettre(Request $request, $id)
    {
        $request->validate([
            'commentaire' => 'nullable|string|max:1000'
        ]);
        
        try {
            $parapheur = Parapheur::findOrFail($id);
            
            // Seul le créateur ou le responsable peut transmettre
            if (!in_array(auth()->id(), [$parapheur->createur_id, $parapheur->responsable_actuel_id])) {
                throw new \Exception('Action non autorisée.');
            }
            
            DB::beginTransaction();
            
            // Pour l'instant, simple transmission au responsable suivant
            $prochainResponsable = $this->getProchainResponsable($parapheur, $parapheur->etape_actuelle);
            
            $parapheur->update([
                'responsable_actuel_id' => $prochainResponsable,
                'statut' => 'en_cours'
            ]);
            
            $details = "Transmis à l'étape suivante" . 
                      ($request->commentaire ? " - " . $request->commentaire : "");
            
            $this->creerHistorique($parapheur, 'transmission', $details, auth()->id());
            
            DB::commit();
            
            return back()->with('success', 'Parapheur transmis avec succès.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }
    
    /**
     * Joindre un fichier
     */
    public function joindreFichier(Request $request, $id)
    {
        $request->validate([
            'fichier' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,png',
            'commentaire' => 'nullable|string|max:500'
        ]);
        
        try {
            $parapheur = Parapheur::findOrFail($id);
            
            // Vérifier permissions
            $this->authorizeView($parapheur);
            
            DB::beginTransaction();
            
            $fichier = $this->stockerFichier($parapheur, $request->file('fichier'), $request->commentaire);
            
            $details = "Fichier joint: " . $fichier->nom_original . 
                      ($request->commentaire ? " - " . $request->commentaire : "");
            
            $this->creerHistorique($parapheur, 'ajout_fichier', $details, auth()->id());
            
            DB::commit();
            
            return back()->with('success', 'Fichier joint avec succès.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }
    
    /**
     * Télécharger un fichier
     */
    public function telechargerFichier($fichierId)
    {
        try {
            // À implémenter avec ton modèle de fichiers
            throw new \Exception('Fonctionnalité à implémenter');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // ACTIONS SUPERADMIN
    // =========================================================================
    
    /**
     * Réassigner un parapheur
     */
    public function reassign(Request $request, $id)
    {
        // Vérifier superadmin
        if (!auth()->user()->hasRole('superadmin')) {
            abort(403, 'Action réservée au superadmin.');
        }
        
        $request->validate([
            'nouveau_responsable_id' => 'required|exists:users,id',
            'motif' => 'required|string|min:10|max:1000'
        ]);
        
        try {
            $parapheur = Parapheur::findOrFail($id);
            
            DB::beginTransaction();
            
            $ancienResponsable = $parapheur->responsable_actuel_id;
            $parapheur->update(['responsable_actuel_id' => $request->nouveau_responsable_id]);
            
            $details = "Réassigné de #$ancienResponsable à #" . $request->nouveau_responsable_id . 
                      " - Motif: " . $request->motif;
            
            $this->creerHistorique($parapheur, 'reassignation_admin', $details, auth()->id());
            
            DB::commit();
            
            return back()->with('success', 'Parapheur réassigné avec succès.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }
    
    /**
     * Forcer l'étape
     */
    public function forcerEtape(Request $request, $id)
    {
        // Vérifier superadmin
        if (!auth()->user()->hasRole('superadmin')) {
            abort(403, 'Action réservée au superadmin.');
        }
        
        $request->validate([
            'nouvelle_etape' => 'required|integer|min:1|max:10',
            'justification' => 'required|string|min:20|max:1000'
        ]);
        
        try {
            $parapheur = Parapheur::findOrFail($id);
            
            DB::beginTransaction();
            
            $ancienneEtape = $parapheur->etape_actuelle;
            $parapheur->update(['etape_actuelle' => $request->nouvelle_etape]);
            
            $details = "Étape forcée de $ancienneEtape à " . $request->nouvelle_etape . 
                      " - Justification: " . $request->justification;
            
            $this->creerHistorique($parapheur, 'force_etape_admin', $details, auth()->id());
            
            DB::commit();
            
            return back()->with('success', 'Étape forcée avec succès.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }
    
    /**
     * Archiver un parapheur
     */
    public function archiver($id)
    {
        // Vérifier superadmin
        if (!auth()->user()->hasRole('superadmin')) {
            abort(403, 'Action réservée au superadmin.');
        }
        
        try {
            $parapheur = Parapheur::findOrFail($id);
            
            $parapheur->update(['statut' => 'archive']);
            
            $this->creerHistorique($parapheur, 'archivage_admin', 'Archivé par superadmin', auth()->id());
            
            return back()->with('success', 'Parapheur archivé.');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // MÉTHODES PRIVÉES
    // =========================================================================
    
    /**
     * Générer une référence unique
     */
    private function genererReference()
    {
        $annee = date('Y');
        $prefixe = 'GDF';
        
        // Chercher le dernier numéro de l'année
        $dernier = Parapheur::where('reference', 'LIKE', "{$prefixe}-{$annee}-%")
                           ->orderBy('reference', 'desc')
                           ->first();
        
        if ($dernier && preg_match('/-(\d+)$/', $dernier->reference, $matches)) {
            $numero = intval($matches[1]) + 1;
        } else {
            $numero = 1;
        }
        
        return sprintf('%s-%s-%05d', $prefixe, $annee, $numero);
    }
    
    /**
     * Déterminer le responsable initial
     */
    private function getResponsableInitial($serviceId, $directionId)
    {
        // Par défaut: chef du service
        $service = Service::find($serviceId);
        
        if ($service && $service->chef_id) {
            return $service->chef_id;
        }
        
        // Sinon: directeur de la direction
        $direction = Direction::find($directionId);
        
        if ($direction && $direction->directeur_id) {
            return $direction->directeur_id;
        }
        
        // Par défaut: l'utilisateur connecté
        return auth()->id();
    }
    
    /**
     * Déterminer le prochain responsable
     */
    private function getProchainResponsable($parapheur, $etape)
    {
        // Workflow simple:
        // Étape 1: Chef de service (déjà fait à la création)
        // Étape 2: Directeur
        // Étape 3: Finalisé (pas de responsable)
        
        if ($etape == 2) {
            // Directeur de la direction
            $direction = Direction::find($parapheur->direction_id);
            return $direction->directeur_id ?? null;
        }
        
        // Dernière étape ou inconnue
        return null;
    }
    
    /**
     * Stocker un fichier
     */
    private function stockerFichier($parapheur, $fichier, $commentaire = null)
    {
        $path = $fichier->store('parapheurs/' . $parapheur->id, 'public');
        
        // À adapter selon ton modèle de fichiers
        return $parapheur->fichiers()->create([
            'nom_original' => $fichier->getClientOriginalName(),
            'nom_stockage' => $fichier->hashName(),
            'chemin' => $path,
            'taille' => $fichier->getSize(),
            'type_mime' => $fichier->getMimeType(),
            'extension' => $fichier->getClientOriginalExtension(),
            'uploader_id' => auth()->id(),
            'commentaire' => $commentaire
        ]);
    }
    
    /**
     * Créer un historique
     */
    private function creerHistorique($parapheur, $action, $details, $userId)
    {
        // À adapter selon ton modèle d'historique
        return $parapheur->historiques()->create([
            'action' => $action,
            'details' => $details,
            'user_id' => $userId,
            'ip_address' => request()->ip()
        ]);
    }
    
    /**
     * Autorisation pour voir
     */
    private function authorizeView($parapheur)
    {
        $user = auth()->user();
        
        // Superadmin peut tout voir
        if ($user->hasRole('superadmin')) {
            return true;
        }
        
        // Directeur peut voir sa direction
        if ($user->hasRole('directeur') && 
            $parapheur->direction_id == $user->direction_id) {
            return true;
        }
        
        // Chef de service peut voir son service
        if (($user->hasRole('chef_service') || $user->hasRole('chef')) && 
            $parapheur->service_id == $user->service_id) {
            return true;
        }
        
        // Créateur ou responsable actuel peut voir
        if ($parapheur->createur_id == $user->id || 
            $parapheur->responsable_actuel_id == $user->id) {
            return true;
        }
        
        abort(403, 'Vous n\'avez pas accès à ce parapheur.');
    }
    
    /**
     * Autorisation pour modifier
     */
    private function authorizeEdit($parapheur)
    {
        $user = auth()->user();
        
        // Seul le créateur peut modifier (si en attente)
        if ($parapheur->createur_id !== $user->id || 
            !in_array($parapheur->statut, ['brouillon', 'en_attente'])) {
            abort(403, 'Vous ne pouvez pas modifier ce parapheur.');
        }
        
        return true;
    }
    
    /**
     * Données de test
     */
    private function getDonneesTest()
    {
        return collect([
            (object)[
                'id' => 1,
                'reference' => 'GDF-2024-00127',
                'objet' => 'Demande d\'exonération fiscale - Société ABC',
                'description' => 'Demande complète d\'exonération pour investissement',
                'statut' => 'en_attente',
                'priorite' => 'urgente',
                'confidentialite' => 'standard',
                'date_creation' => now()->subDays(2),
                'date_echeance' => now()->addDays(5),
                'createur_id' => 1,
                'service_id' => 1,
                'direction_id' => 1,
                'responsable_actuel_id' => 1,
                'etape_actuelle' => 1,
                'etapes_total' => 3,
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(1)
            ],
            (object)[
                'id' => 2,
                'reference' => 'GDF-2024-00126',
                'objet' => 'Dispense de TVA - Projet X',
                'description' => 'Demande de dispense de TVA pour projet d\'investissement',
                'statut' => 'valide',
                'priorite' => 'normale',
                'confidentialite' => 'standard',
                'date_creation' => now()->subDays(5),
                'date_echeance' => now()->addDays(2),
                'createur_id' => 1,
                'service_id' => 2,
                'direction_id' => 1,
                'responsable_actuel_id' => null,
                'etape_actuelle' => 3,
                'etapes_total' => 3,
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(2)
            ]
        ]);
    }
    
    /**
     * Parapheur de test
     */
    private function getParapheurTest($id)
    {
        return (object)[
            'id' => $id,
            'reference' => 'GDF-2024-' . str_pad($id, 5, '0', STR_PAD_LEFT),
            'objet' => 'Parapheur de test #' . $id,
            'description' => 'Ceci est un parapheur de démonstration',
            'statut' => 'en_attente',
            'priorite' => 'normale',
            'confidentialite' => 'standard',
            'date_creation' => now()->subDays(3),
            'date_echeance' => now()->addDays(7),
            'createur' => (object)['name' => auth()->user()->name],
            'service' => (object)['nom' => 'Service test'],
            'direction' => (object)['nom' => 'Direction test'],
            'responsableActuel' => (object)['name' => auth()->user()->name],
            'etape_actuelle' => 1,
            'etapes_total' => 3,
            'created_at' => now()->subDays(3),
            'updated_at' => now()->subDays(1)
        ];
    }
}