<?php

namespace App\Http\Controllers;

use App\Models\Parapheur;
use App\Models\Service;
use App\Models\Direction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ParapheurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Parapheur::with(['createur', 'service', 'direction', 'responsableActuel']);
        
        // FILTRAGE PAR RÔLE
        // Superadmin : voir TOUT
        if (auth()->user()->hasRole('superadmin')) {
            // Pas de restrictions, voir tous les parapheurs
            
            // Filtres avancés pour superadmin
            $filters = [
                'statut', 'priorite', 'service_id', 'direction_id',
                'createur_id', 'responsable_actuel_id', 'confidentialite'
            ];
            
            foreach ($filters as $filter) {
                if ($request->filled($filter)) {
                    $query->where($filter, $request->$filter);
                }
            }
            
            // Filtres par date
            if ($request->filled('date_debut')) {
                $query->where('created_at', '>=', $request->date_debut);
            }
            if ($request->filled('date_fin')) {
                $query->where('created_at', '<=', $request->date_fin);
            }
        }
        // Directeur : voir sa direction
        elseif (auth()->user()->hasRole('directeur')) {
            $query->where('direction_id', auth()->user()->direction_id);
            
            // Filtres basiques
            if ($request->filled('statut')) {
                $query->where('statut', $request->statut);
            }
            if ($request->filled('priorite')) {
                $query->where('priorite', $request->priorite);
            }
        }
        // Chef de service : voir son service
        elseif (auth()->user()->hasRole('chef_service')) {
            $query->where('service_id', auth()->user()->service_id);
            
            // Filtres basiques
            if ($request->filled('statut')) {
                $query->where('statut', $request->statut);
            }
        }
        // Autres rôles : voir leurs propres parapheurs
        else {
            $query->where(function($q) {
                $q->where('createur_id', auth()->id())
                  ->orWhere('responsable_actuel_id', auth()->id());
            });
            
            // Filtre statut seulement
            if ($request->filled('statut')) {
                $query->where('statut', $request->statut);
            }
        }
        
        // Tri par défaut
        $query->orderBy('created_at', 'desc');
        
        $parapheurs = $query->paginate(20);
        
        // Données pour les filtres (superadmin seulement)
        if (auth()->user()->hasRole('superadmin')) {
            $services = Service::all();
            $directions = Direction::all();
            $utilisateurs = User::with('roles')->get();
            return view('parapheurs.index', compact('parapheurs', 'services', 'directions', 'utilisateurs'));
        }
        
        return view('parapheurs.index', compact('parapheurs'));
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
            'objet' => 'required|string|max:255',
            'description' => 'required|string',
            'service_id' => 'required|exists:services,id',
            'direction_id' => 'required|exists:directions,id',
            'date_echeance' => 'required|date|after:today',
            'priorite' => 'required|in:basse,normale,haute,urgente',
            'confidentialite' => 'sometimes|in:standard,confidentiel,tres_confidentiel',
            'fichiers.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,png'
        ]);
        
        // Générer référence automatique
        $reference = 'PAR-' . date('Ymd') . '-' . str_pad(Parapheur::count() + 1, 4, '0', STR_PAD_LEFT);
        
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
            'etapes_total' => 3, // À configurer dans les paramètres
            'responsable_actuel_id' => $this->getResponsableInitial($request->service_id, $request->direction_id)
        ]);
        
        // Gestion des fichiers joints
        if ($request->hasFile('fichiers')) {
            foreach ($request->file('fichiers') as $fichier) {
                $path = $fichier->store('parapheurs/' . $parapheur->id, 'public');
                
                $parapheur->fichiers()->create([
                    'nom_original' => $fichier->getClientOriginalName(),
                    'nom_stockage' => $fichier->hashName(),
                    'chemin' => $path,
                    'taille' => $fichier->getSize(),
                    'type_mime' => $fichier->getMimeType(),
                    'extension' => $fichier->getClientOriginalExtension(),
                    'uploader_id' => auth()->id(),
                    'est_principal' => false
                ]);
            }
        }
        
        // Créer l'historique
        $parapheur->historiques()->create([
            'action' => 'creation',
            'details' => 'Parapheur créé par ' . auth()->user()->name . '. Référence: ' . $reference,
            'user_id' => auth()->id()
        ]);
        
        return redirect()->route('parapheurs.show', $parapheur)
                        ->with('success', 'Parapheur créé avec succès. Référence: ' . $reference);
    }

    /**
     * Display the specified resource.
     */
    public function show(Parapheur $parapheur)
    {
        // Vérifier les permissions
        $this->authorizeView($parapheur);
        
        $parapheur->load(['fichiers', 'historiques.user', 'createur', 'service', 'direction', 'responsableActuel']);
        
        return view('parapheurs.show', compact('parapheur'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Parapheur $parapheur)
    {
        // Vérifier les permissions
        $this->authorizeEdit($parapheur);
        
        $services = Service::where('actif', true)->get();
        $directions = Direction::where('actif', true)->get();
        
        return view('parapheurs.edit', compact('parapheur', 'services', 'directions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Parapheur $parapheur)
    {
        // Vérifier les permissions
        $this->authorizeEdit($parapheur);
        
        $request->validate([
            'objet' => 'required|string|max:255',
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
        
        $parapheur->historiques()->create([
            'action' => 'modification',
            'details' => 'Parapheur modifié par ' . auth()->user()->name,
            'user_id' => auth()->id()
        ]);
        
        return redirect()->route('parapheurs.show', $parapheur)
                        ->with('success', 'Parapheur mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Parapheur $parapheur)
    {
        // Seul superadmin ou créateur peut supprimer (si brouillon)
        if (!auth()->user()->hasRole('superadmin') && 
            ($parapheur->createur_id !== auth()->id() || $parapheur->statut !== 'brouillon')) {
            abort(403, 'Action non autorisée.');
        }
        
        // Soft delete
        $parapheur->delete();
        
        return redirect()->route('parapheurs.index')
                        ->with('success', 'Parapheur supprimé avec succès.');
    }

    // =========================================================================
    // ACTIONS SUR LES PARAPHEURS
    // =========================================================================
    
    /**
     * Valider une étape du parapheur
     */
    public function valider(Request $request, Parapheur $parapheur)
    {
        $request->validate([
            'commentaire' => 'nullable|string|max:500'
        ]);
        
        // Vérifier que l'utilisateur est le responsable actuel
        if ($parapheur->responsable_actuel_id !== auth()->id()) {
            abort(403, 'Vous n\'êtes pas le responsable de ce parapheur.');
        }
        
        $nouvelleEtape = $parapheur->etape_actuelle + 1;
        $nouveauStatut = ($nouvelleEtape >= $parapheur->etapes_total) ? 'valide' : 'en_cours';
        
        $parapheur->update([
            'statut' => $nouveauStatut,
            'etape_actuelle' => $nouvelleEtape,
            'responsable_actuel_id' => $this->getNextResponsable($parapheur),
            'date_validation' => $nouveauStatut === 'valide' ? now() : null
        ]);
        
        $parapheur->historiques()->create([
            'action' => 'validation',
            'details' => 'Étape ' . ($nouvelleEtape - 1) . ' validée par ' . auth()->user()->name . 
                        ($request->commentaire ? '. Commentaire: ' . $request->commentaire : ''),
            'user_id' => auth()->id()
        ]);
        
        return back()->with('success', 'Étape validée avec succès.');
    }
    
    /**
     * Rejeter un parapheur
     */
    public function rejeter(Request $request, Parapheur $parapheur)
    {
        $request->validate([
            'motif' => 'required|string|min:10|max:1000'
        ]);
        
        // Vérifier que l'utilisateur est le responsable actuel
        if ($parapheur->responsable_actuel_id !== auth()->id()) {
            abort(403, 'Vous n\'êtes pas le responsable de ce parapheur.');
        }
        
        $parapheur->update([
            'statut' => 'rejete',
            'motif_rejet' => $request->motif,
            'date_rejet' => now()
        ]);
        
        $parapheur->historiques()->create([
            'action' => 'rejet',
            'details' => 'Parapheur rejeté par ' . auth()->user()->name . '. Motif: ' . $request->motif,
            'user_id' => auth()->id()
        ]);
        
        return back()->with('success', 'Parapheur rejeté avec succès.');
    }
    
    /**
     * Joindre un fichier supplémentaire
     */
    public function joindreFichier(Request $request, Parapheur $parapheur)
    {
        $request->validate([
            'fichier' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,png',
            'commentaire' => 'nullable|string|max:500'
        ]);
        
        $path = $request->file('fichier')->store('parapheurs/' . $parapheur->id, 'public');
        
        $fichier = $parapheur->fichiers()->create([
            'nom_original' => $request->file('fichier')->getClientOriginalName(),
            'nom_stockage' => $request->file('fichier')->hashName(),
            'chemin' => $path,
            'taille' => $request->file('fichier')->getSize(),
            'type_mime' => $request->file('fichier')->getMimeType(),
            'extension' => $request->file('fichier')->getClientOriginalExtension(),
            'uploader_id' => auth()->id(),
            'commentaire' => $request->commentaire
        ]);
        
        $parapheur->historiques()->create([
            'action' => 'ajout_fichier',
            'details' => 'Fichier ajouté: ' . $fichier->nom_original . 
                        ($request->commentaire ? ' - ' . $request->commentaire : ''),
            'user_id' => auth()->id()
        ]);
        
        return back()->with('success', 'Fichier joint avec succès.');
    }
    
    /**
     * Télécharger un fichier
     */
    public function telechargerFichier($fichierId)
    {
        $fichier = \App\Models\FichierParapheur::findOrFail($fichierId);
        $parapheur = $fichier->parapheur;
        
        // Vérifier les permissions
        $this->authorizeView($parapheur);
        
        if (!Storage::disk('public')->exists($fichier->chemin)) {
            abort(404, 'Fichier non trouvé.');
        }
        
        $fichier->increment('telechargements');
        
        return Storage::disk('public')->download($fichier->chemin, $fichier->nom_original);
    }

    // =========================================================================
    // ACTIONS SUPERADMIN
    // =========================================================================
    
    /**
     * Réassigner un parapheur (Superadmin seulement)
     */
    public function reassign(Request $request, Parapheur $parapheur)
    {
        // Vérifier que l'utilisateur est superadmin
        if (!auth()->user()->hasRole('superadmin')) {
            abort(403, 'Action réservée au superadmin.');
        }
        
        $request->validate([
            'nouveau_responsable_id' => 'required|exists:users,id',
            'commentaire' => 'required|string|min:10|max:1000'
        ]);
        
        $ancienResponsable = $parapheur->responsable_actuel_id;
        $parapheur->update(['responsable_actuel_id' => $request->nouveau_responsable_id]);
        
        $parapheur->historiques()->create([
            'action' => 'reassignation_admin',
            'details' => "Réassigné par " . auth()->user()->name . 
                        " de l'utilisateur #$ancienResponsable à #" . $request->nouveau_responsable_id .
                        ". Raison: " . $request->commentaire,
            'user_id' => auth()->id()
        ]);
        
        return back()->with('success', 'Parapheur réassigné avec succès.');
    }
    
    /**
     * Forcer le passage d'étape (Superadmin seulement)
     */
    public function force(Request $request, Parapheur $parapheur)
    {
        // Vérifier que l'utilisateur est superadmin
        if (!auth()->user()->hasRole('superadmin')) {
            abort(403, 'Action réservée au superadmin.');
        }
        
        $request->validate([
            'nouvelle_etape' => 'required|integer|min:' . ($parapheur->etape_actuelle + 1) . '|max:' . $parapheur->etapes_total,
            'justification' => 'required|string|min:20|max:1000'
        ]);
        
        $ancienneEtape = $parapheur->etape_actuelle;
        $parapheur->update(['etape_actuelle' => $request->nouvelle_etape]);
        
        // Si on atteint la dernière étape, marquer comme validé
        if ($request->nouvelle_etape >= $parapheur->etapes_total) {
            $parapheur->update(['statut' => 'valide', 'date_validation' => now()]);
        }
        
        $parapheur->historiques()->create([
            'action' => 'force_etape_admin',
            'details' => "Étape forcée par " . auth()->user()->name . 
                        " de $ancienneEtape à " . $request->nouvelle_etape .
                        ". Justification: " . $request->justification,
            'user_id' => auth()->id()
        ]);
        
        return back()->with('success', 'Étape forcée avec succès.');
    }
    
    /**
     * Archiver un parapheur (Superadmin seulement)
     */
    public function archive(Parapheur $parapheur)
    {
        // Vérifier que l'utilisateur est superadmin
        if (!auth()->user()->hasRole('superadmin')) {
            abort(403, 'Action réservée au superadmin.');
        }
        
        $parapheur->update(['statut' => 'archive']);
        
        $parapheur->historiques()->create([
            'action' => 'archivage_admin',
            'details' => "Archivé par " . auth()->user()->name,
            'user_id' => auth()->id()
        ]);
        
        return back()->with('success', 'Parapheur archivé.');
    }

    // =========================================================================
    // MÉTHODES PRIVÉES
    // =========================================================================
    
    /**
     * Déterminer le responsable initial
     */
    private function getResponsableInitial($serviceId, $directionId)
    {
        // Par défaut: chef du service
        $service = Service::find($serviceId);
        return $service->chef_id ?? null;
    }
    
    /**
     * Déterminer le prochain responsable selon le workflow
     */
    private function getNextResponsable(Parapheur $parapheur)
    {
        // Workflow simple:
        // Étape 1: Chef de service (déjà fait)
        // Étape 2: Directeur
        // Étape 3: Archivage (pas de responsable)
        
        if ($parapheur->etape_actuelle + 1 == 2) {
            // Directeur de la direction
            $direction = Direction::find($parapheur->direction_id);
            return $direction->directeur_id ?? null;
        }
        
        // Dernière étape ou inconnue
        return null;
    }
    
    /**
     * Autorisation pour voir un parapheur
     */
    private function authorizeView(Parapheur $parapheur)
    {
        // Superadmin peut tout voir
        if (auth()->user()->hasRole('superadmin')) {
            return true;
        }
        
        // Directeur peut voir sa direction
        if (auth()->user()->hasRole('directeur') && 
            $parapheur->direction_id == auth()->user()->direction_id) {
            return true;
        }
        
        // Chef de service peut voir son service
        if (auth()->user()->hasRole('chef_service') && 
            $parapheur->service_id == auth()->user()->service_id) {
            return true;
        }
        
        // Créateur ou responsable actuel peut voir
        if ($parapheur->createur_id == auth()->id() || 
            $parapheur->responsable_actuel_id == auth()->id()) {
            return true;
        }
        
        abort(403, 'Vous n\'avez pas accès à ce parapheur.');
    }
    
    /**
     * Autorisation pour modifier un parapheur
     */
    private function authorizeEdit(Parapheur $parapheur)
    {
        // Seul le créateur peut modifier (si en attente)
        if ($parapheur->createur_id !== auth()->id() || $parapheur->statut !== 'en_attente') {
            abort(403, 'Vous ne pouvez pas modifier ce parapheur.');
        }
        
        return true;
    }
}