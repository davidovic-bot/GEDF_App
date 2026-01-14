<?php

namespace App\Http\Controllers;

use App\Models\Courrier;
use App\Models\User;
use App\Models\Service;
use App\Models\Validation;
use App\Models\HistoriqueCourrier;
use App\Models\PieceJointe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CourrierController extends Controller
{
    /**
     * Afficher la liste des dossiers fiscaux
     */
    public function index(Request $request)
    {
        // Récupérer les paramètres de filtrage
        $type = $request->input('type');
        $statut = $request->input('statut');
        $search = $request->input('search');
        $dateDebut = $request->input('date_debut');
        $dateFin = $request->input('date_fin');
        $retard = $request->has('retard');
        
        // Construire la requête
        $query = Courrier::with(['createur', 'service'])
                        ->orderBy('created_at', 'desc');
        
        // Appliquer les filtres
        if ($type) {
            $query->where('type_dossier', $type);
        }
        
        if ($statut) {
            $query->where('statut', $statut);
        }
        
        if ($retard) {
            $query->where('date_limite', '<', Carbon::now())
                  ->whereIn('statut', ['en_analyse', 'en_validation']);
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('reference', 'LIKE', "%{$search}%")
                  ->orWhere('sujet', 'LIKE', "%{$search}%")
                  ->orWhere('contribuable_nom', 'LIKE', "%{$search}%")
                  ->orWhere('contribuable_id_fiscal', 'LIKE', "%{$search}%");
            });
        }
        
        if ($dateDebut && $dateFin) {
            $query->whereBetween('created_at', [
                Carbon::parse($dateDebut)->startOfDay(),
                Carbon::parse($dateFin)->endOfDay()
            ]);
        }
        
        // Filtrer selon le rôle de l'utilisateur
        $user = Auth::user();
        
        if ($user->role == 'agent') {
            $query->where('createur_id', $user->id);
        } elseif ($user->role == 'chef_service') {
            $query->whereHas('service', function($q) use ($user) {
                $q->where('id', $user->service_id);
            });
        } elseif ($user->role == 'secretaire') {
            $query->where('createur_id', $user->id);
        }
        // Admin et Directeur voient tout
        
        // Pagination
        $perPage = $request->input('per_page', 10);
        $courriers = $query->paginate($perPage);
        
        // Statistiques pour le dashboard
        $stats = [
            'total' => Courrier::count(),
            'en_cours' => Courrier::whereIn('statut', ['en_analyse', 'en_validation'])->count(),
            'en_validation' => Courrier::where('statut', 'en_validation')->count(),
            'en_retard' => Courrier::where('date_limite', '<', Carbon::now())
                                ->whereIn('statut', ['en_analyse', 'en_validation'])
                                ->count(),
            'signes' => Courrier::where('statut', 'signe')->count(),
            'archives' => Courrier::where('statut', 'archive')->count(),
            'rejetes' => Courrier::where('statut', 'rejete')->count(),
        ];
        
        return view('courriers.index', [
            'courriers' => $courriers,
            'stats' => $stats,
            'types' => Courrier::getTypes(),
            'statuts' => Courrier::getStatuts(),
            'filters' => $request->all()
        ]);
    }
    
    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        // Récupérer la liste des services
        $services = Service::all();
        
        return view('courriers.create', [
            'types' => Courrier::getTypes(),
            'services' => $services
        ]);
    }
    
    /**
     * Enregistrer un nouveau dossier
     */
    public function store(Request $request)
    {
        // Validation
        $validated = $request->validate([
            'type_dossier' => 'required|in:exoneration,dispense_tva,rejet,autre',
            'contribuable_nom' => 'required|string|max:255',
            'contribuable_id_fiscal' => 'required|string|max:50',
            'secteur_activite' => 'nullable|string|max:100',
            'montant_impact' => 'nullable|numeric|min:0',
            'sujet' => 'required|string|max:500',
            'description' => 'required|string',
            'date_limite' => 'nullable|date|after_or_equal:today',
            'service_id' => 'required|exists:services,id',
            'pieces_jointes.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png'
        ]);
        
        DB::beginTransaction();
        
        try {
            // Générer la référence
            $reference = $this->generateReference($validated['type_dossier']);
            
            // Créer le dossier
            $courrier = Courrier::create([
                'reference' => $reference,
                'type_dossier' => $validated['type_dossier'],
                'contribuable_nom' => $validated['contribuable_nom'],
                'contribuable_id_fiscal' => $validated['contribuable_id_fiscal'],
                'secteur_activite' => $validated['secteur_activite'],
                'montant_impact' => $validated['montant_impact'],
                'sujet' => $validated['sujet'],
                'description' => $validated['description'],
                'date_limite' => $validated['date_limite'],
                'service_id' => $validated['service_id'],
                'statut' => Courrier::STATUT_ANALYSE,
                'createur_id' => Auth::id()
            ]);
            
            // Gérer les pièces jointes
            if ($request->hasFile('pieces_jointes')) {
                foreach ($request->file('pieces_jointes') as $file) {
                    $path = $file->store('pieces_jointes/' . $courrier->id, 'public');
                    
                    PieceJointe::create([
                        'courrier_id' => $courrier->id,
                        'nom_fichier' => $file->getClientOriginalName(),
                        'chemin_fichier' => $path,
                        'taille' => $file->getSize(),
                        'mime_type' => $file->getMimeType()
                    ]);
                }
            }
            
            // Enregistrer dans l'historique
            HistoriqueCourrier::create([
                'courrier_id' => $courrier->id,
                'user_id' => Auth::id(),
                'action' => 'creation',
                'details' => 'Création du dossier fiscal',
                'commentaire' => 'Dossier créé avec la référence ' . $reference
            ]);
            
            DB::commit();
            
            return redirect()->route('courriers.show', $courrier)
                            ->with('success', 'Dossier fiscal créé avec succès. Référence : ' . $reference);
                            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la création du dossier: ' . $e->getMessage())
                        ->withInput();
        }
    }
    
    /**
     * Afficher un dossier spécifique
     */
    public function show(Courrier $courrier)
    {
        // Vérifier les permissions
        if (!Auth::user()->can('view', $courrier)) {
            abort(403, 'Accès non autorisé à ce dossier.');
        }
        
        // Charger les relations
        $courrier->load([
            'createur',
            'service',
            'validations.user',
            'validationsEnAttente.user',
            'historiques.user',
            'piecesJointes'
        ]);
        
        // Récupérer le prochain validateur
        $prochainValidateur = $courrier->validationsEnAttente->first();
        
        return view('courriers.show', [
            'courrier' => $courrier,
            'prochainValidateur' => $prochainValidateur,
            'types' => Courrier::getTypes(),
            'statuts' => Courrier::getStatuts()
        ]);
    }
    
    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Courrier $courrier)
    {
        // Vérifier les permissions
        if (!Auth::user()->can('update', $courrier)) {
            abort(403, 'Vous ne pouvez pas modifier ce dossier.');
        }
        
        // Vérifier que le dossier est encore modifiable
        if ($courrier->statut != Courrier::STATUT_ANALYSE) {
            return redirect()->route('courriers.show', $courrier)
                            ->with('error', 'Ce dossier ne peut plus être modifié (statut: ' . $courrier->libelle_statut . ')');
        }
        
        $services = Service::all();
        
        return view('courriers.edit', [
            'courrier' => $courrier,
            'types' => Courrier::getTypes(),
            'services' => $services
        ]);
    }
    
    /**
     * Mettre à jour un dossier
     */
    public function update(Request $request, Courrier $courrier)
    {
        // Vérifier les permissions
        if (!Auth::user()->can('update', $courrier)) {
            abort(403, 'Vous ne pouvez pas modifier ce dossier.');
        }
        
        // Vérifier que le dossier est encore modifiable
        if ($courrier->statut != Courrier::STATUT_ANALYSE) {
            return redirect()->route('courriers.show', $courrier)
                            ->with('error', 'Ce dossier ne peut plus être modifié.');
        }
        
        // Validation
        $validated = $request->validate([
            'type_dossier' => 'required|in:exoneration,dispense_tva,rejet,autre',
            'contribuable_nom' => 'required|string|max:255',
            'contribuable_id_fiscal' => 'required|string|max:50',
            'secteur_activite' => 'nullable|string|max:100',
            'montant_impact' => 'nullable|numeric|min:0',
            'sujet' => 'required|string|max:500',
            'description' => 'required|string',
            'date_limite' => 'nullable|date',
            'service_id' => 'required|exists:services,id',
            'pieces_jointes.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png'
        ]);
        
        DB::beginTransaction();
        
        try {
            // Enregistrer l'ancien statut pour l'historique
            $ancienStatut = $courrier->statut;
            
            // Mettre à jour
            $courrier->update($validated);
            
            // Gérer les nouvelles pièces jointes
            if ($request->hasFile('pieces_jointes')) {
                foreach ($request->file('pieces_jointes') as $file) {
                    $path = $file->store('pieces_jointes/' . $courrier->id, 'public');
                    
                    PieceJointe::create([
                        'courrier_id' => $courrier->id,
                        'nom_fichier' => $file->getClientOriginalName(),
                        'chemin_fichier' => $path,
                        'taille' => $file->getSize(),
                        'mime_type' => $file->getMimeType()
                    ]);
                }
            }
            
            // Enregistrer dans l'historique
            HistoriqueCourrier::create([
                'courrier_id' => $courrier->id,
                'user_id' => Auth::id(),
                'action' => 'modification',
                'details' => 'Mise à jour du dossier',
                'commentaire' => 'Modification des informations du dossier'
            ]);
            
            DB::commit();
            
            return redirect()->route('courriers.show', $courrier)
                            ->with('success', 'Dossier mis à jour avec succès.');
                            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage())
                        ->withInput();
        }
    }
    
    /**
     * Supprimer un dossier
     */
    public function destroy(Courrier $courrier)
    {
        // Vérifier les permissions
        if (!Auth::user()->can('delete', $courrier)) {
            abort(403, 'Vous ne pouvez pas supprimer ce dossier.');
        }
        
        DB::beginTransaction();
        
        try {
            // Enregistrer dans l'historique avant suppression
            HistoriqueCourrier::create([
                'courrier_id' => $courrier->id,
                'user_id' => Auth::id(),
                'action' => 'suppression',
                'details' => 'Suppression du dossier',
                'commentaire' => 'Dossier supprimé définitivement'
            ]);
            
            $courrier->delete();
            
            DB::commit();
            
            return redirect()->route('courriers.index')
                            ->with('success', 'Dossier supprimé avec succès.');
                            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }
    
    /**
     * Transmettre un dossier pour validation
     */
    public function transmettreValidation(Request $request, Courrier $courrier)
    {
        // Vérifier les permissions
        if (!Auth::user()->can('transmettre', $courrier)) {
            abort(403, 'Vous ne pouvez pas transmettre ce dossier.');
        }
        
        // Vérifier que le dossier est en analyse
        if ($courrier->statut != Courrier::STATUT_ANALYSE) {
            return back()->with('error', 'Le dossier doit être en analyse pour transmission.');
        }
        
        DB::beginTransaction();
        
        try {
            // Trouver le chef de service concerné
            $chefService = User::where('service_id', $courrier->service_id)
                              ->where('role', 'chef_service')
                              ->first();
            
            if (!$chefService) {
                return back()->with('error', 'Aucun chef de service trouvé pour ce service.');
            }
            
            // Mettre à jour le statut du courrier
            $courrier->update([
                'statut' => Courrier::STATUT_VALIDATION
            ]);
            
            // Créer la validation pour le chef de service
            Validation::create([
                'courrier_id' => $courrier->id,
                'user_id' => $chefService->id,
                'role_validation' => 'chef_service',
                'statut' => Validation::STATUT_EN_ATTENTE,
                'ordre' => 1
            ]);
            
            // Enregistrer dans l'historique
            HistoriqueCourrier::create([
                'courrier_id' => $courrier->id,
                'user_id' => Auth::id(),
                'action' => 'transmission_validation',
                'details' => 'Transmission pour validation',
                'commentaire' => 'Dossier transmis au chef de service ' . $chefService->name
            ]);
            
            DB::commit();
            
            return redirect()->route('courriers.show', $courrier)
                            ->with('success', 'Dossier transmis pour validation au chef de service.');
                            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la transmission: ' . $e->getMessage());
        }
    }
    
    /**
     * Valider un dossier (action du chef de service)
     */
    public function valider(Request $request, Courrier $courrier)
    {
        // Vérifier que l'utilisateur est chef de service du service concerné
        $user = Auth::user();
        if ($user->role !== 'chef_service' || $user->service_id !== $courrier->service_id) {
            abort(403, 'Action non autorisée.');
        }
        
        // Vérifier que le dossier est en validation
        if ($courrier->statut !== Courrier::STATUT_VALIDATION) {
            return back()->with('error', 'Le dossier n\'est pas en phase de validation.');
        }
        
        $request->validate([
            'commentaire' => 'nullable|string|max:1000'
        ]);
        
        DB::beginTransaction();
        
        try {
            // Marquer la validation comme effectuée
            $validation = Validation::where('courrier_id', $courrier->id)
                                  ->where('user_id', $user->id)
                                  ->where('statut', Validation::STATUT_EN_ATTENTE)
                                  ->first();
            
            if (!$validation) {
                return back()->with('error', 'Validation non trouvée.');
            }
            
            $validation->update([
                'statut' => Validation::STATUT_VALIDE,
                'date_validation' => Carbon::now(),
                'commentaire' => $request->commentaire
            ]);
            
            // Trouver le directeur pour la prochaine validation
            $directeur = User::where('role', 'directeur')->first();
            
            if ($directeur) {
                // Créer la validation pour le directeur
                Validation::create([
                    'courrier_id' => $courrier->id,
                    'user_id' => $directeur->id,
                    'role_validation' => 'directeur',
                    'statut' => Validation::STATUT_EN_ATTENTE,
                    'ordre' => 2
                ]);
                
                // Historique
                HistoriqueCourrier::create([
                    'courrier_id' => $courrier->id,
                    'user_id' => $user->id,
                    'action' => 'validation_chef_service',
                    'details' => 'Validation par le chef de service',
                    'commentaire' => $request->commentaire ?: 'Dossier validé par le chef de service'
                ]);
                
                $message = 'Dossier validé. Transmis au directeur pour signature.';
                
            } else {
                // Pas de directeur → passer directement à signé
                $courrier->update([
                    'statut' => Courrier::STATUT_SIGNE,
                    'date_decision' => Carbon::now()
                ]);
                
                // Historique
                HistoriqueCourrier::create([
                    'courrier_id' => $courrier->id,
                    'user_id' => $user->id,
                    'action' => 'finalisation',
                    'details' => 'Dossier finalisé (sans signature directeur)',
                    'commentaire' => 'Validation complétée par le chef de service'
                ]);
                
                $message = 'Dossier validé et marqué comme signé (pas de directeur configuré).';
            }
            
            DB::commit();
            
            return redirect()->route('courriers.show', $courrier)
                            ->with('success', $message);
                            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la validation: ' . $e->getMessage());
        }
    }
    
    /**
     * Signer un dossier (action du directeur)
     */
    public function signer(Request $request, Courrier $courrier)
    {
        // Vérifier que l'utilisateur est directeur
        $user = Auth::user();
        if ($user->role !== 'directeur') {
            abort(403, 'Action non autorisée.');
        }
        
        // Vérifier que le dossier est en validation
        if ($courrier->statut !== Courrier::STATUT_VALIDATION) {
            return back()->with('error', 'Le dossier n\'est pas en phase de validation.');
        }
        
        $request->validate([
            'commentaire' => 'nullable|string|max:1000'
        ]);
        
        DB::beginTransaction();
        
        try {
            // Marquer la validation comme signée
            $validation = Validation::where('courrier_id', $courrier->id)
                                  ->where('user_id', $user->id)
                                  ->where('statut', Validation::STATUT_EN_ATTENTE)
                                  ->first();
            
            if (!$validation) {
                return back()->with('error', 'Validation non trouvée.');
            }
            
            $validation->update([
                'statut' => Validation::STATUT_SIGNE,
                'date_validation' => Carbon::now(),
                'commentaire' => $request->commentaire
            ]);
            
            // Mettre à jour le statut du courrier
            $courrier->update([
                'statut' => Courrier::STATUT_SIGNE,
                'date_decision' => Carbon::now()
            ]);
            
            // Historique
            HistoriqueCourrier::create([
                'courrier_id' => $courrier->id,
                'user_id' => $user->id,
                'action' => 'signature_directeur',
                'details' => 'Signature par le directeur',
                'commentaire' => $request->commentaire ?: 'Dossier signé par le directeur'
            ]);
            
            DB::commit();
            
            return redirect()->route('courriers.show', $courrier)
                            ->with('success', 'Dossier signé avec succès.');
                            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la signature: ' . $e->getMessage());
        }
    }
    
    /**
     * Rejeter un dossier
     */
    public function rejeter(Request $request, Courrier $courrier)
    {
        // Vérifier les permissions (chef de service ou directeur)
        $user = Auth::user();
        if (!in_array($user->role, ['chef_service', 'directeur'])) {
            abort(403, 'Action non autorisée.');
        }
        
        // Si chef de service, vérifier que c'est son service
        if ($user->role == 'chef_service' && $user->service_id !== $courrier->service_id) {
            abort(403, 'Vous ne pouvez pas rejeter un dossier d\'un autre service.');
        }
        
        $request->validate([
            'motif_rejet' => 'required|string|max:1000'
        ]);
        
        DB::beginTransaction();
        
        try {
            // Mettre à jour le statut du courrier
            $courrier->update([
                'statut' => Courrier::STATUT_REJETE,
                'motif_rejet' => $request->motif_rejet
            ]);
            
            // Marquer les validations en attente comme annulées
            Validation::where('courrier_id', $courrier->id)
                     ->where('statut', Validation::STATUT_EN_ATTENTE)
                     ->update(['statut' => Validation::STATUT_ANNULE]);
            
            // Marquer la validation actuelle comme rejetée
            $validation = Validation::where('courrier_id', $courrier->id)
                                  ->where('user_id', $user->id)
                                  ->where('statut', Validation::STATUT_EN_ATTENTE)
                                  ->first();
            
            if ($validation) {
                $validation->update([
                    'statut' => Validation::STATUT_REJETE,
                    'date_validation' => Carbon::now(),
                    'commentaire' => $request->motif_rejet
                ]);
            }
            
            // Historique
            HistoriqueCourrier::create([
                'courrier_id' => $courrier->id,
                'user_id' => $user->id,
                'action' => 'rejet',
                'details' => 'Rejet du dossier',
                'commentaire' => 'Motif : ' . $request->motif_rejet
            ]);
            
            DB::commit();
            
            return redirect()->route('courriers.show', $courrier)
                            ->with('success', 'Dossier rejeté avec succès.');
                            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors du rejet: ' . $e->getMessage());
        }
    }
    
    /**
     * Archiver un dossier
     */
    public function archive(Courrier $courrier)
    {
        // Vérifier que le dossier peut être archivé
        if ($courrier->statut != Courrier::STATUT_SIGNE) {
            return back()->with('error', 
                'Seuls les dossiers signés peuvent être archivés. Statut actuel : ' . 
                $courrier->libelle_statut);
        }
        
        DB::beginTransaction();
        
        try {
            // Archiver
            $courrier->update([
                'statut' => Courrier::STATUT_ARCHIVE,
                'date_archive' => Carbon::now()
            ]);
            
            // Enregistrer dans l'historique
            HistoriqueCourrier::create([
                'courrier_id' => $courrier->id,
                'user_id' => Auth::id(),
                'action' => 'archivage',
                'details' => 'Archivage du dossier',
                'commentaire' => 'Dossier archivé avec succès'
            ]);
            
            DB::commit();
            
            return redirect()->route('courriers.archives')
                            ->with('success', 'Dossier archivé avec succès.');
                            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de l\'archivage: ' . $e->getMessage());
        }
    }
    
    /**
     * Afficher l'historique d'un dossier
     */
    public function historique(Courrier $courrier)
    {
        // Vérifier les permissions
        if (!Auth::user()->can('view', $courrier)) {
            abort(403, 'Accès non autorisé.');
        }
        
        $historiques = $courrier->historiques()
                               ->with('user')
                               ->orderBy('created_at', 'desc')
                               ->get();
        
        return view('courriers.historique', [
            'courrier' => $courrier,
            'historiques' => $historiques
        ]);
    }
    
    /**
     * Afficher les archives
     */
    public function archives(Request $request)
    {
        $query = Courrier::where('statut', Courrier::STATUT_ARCHIVE)
                        ->with(['createur', 'service'])
                        ->orderBy('date_archive', 'desc');
        
        // Filtres
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reference', 'LIKE', "%{$search}%")
                  ->orWhere('contribuable_nom', 'LIKE', "%{$search}%")
                  ->orWhere('sujet', 'LIKE', "%{$search}%");
            });
        }
        
        $archives = $query->paginate(20);
        
        return view('courriers.archives', [
            'archives' => $archives
        ]);
    }
    
    /**
     * Afficher le parapheur électronique
     */
    public function parapheur(Request $request)
    {
        $user = Auth::user();
        
        // Récupérer les validations en attente pour l'utilisateur
        $validationsEnAttente = Validation::with(['courrier', 'courrier.createur', 'courrier.service'])
                                         ->where('user_id', $user->id)
                                         ->where('statut', Validation::STATUT_EN_ATTENTE)
                                         ->orderBy('ordre', 'asc')
                                         ->orderBy('created_at', 'asc')
                                         ->get();
        
        // Récupérer les validations effectuées récemment
        $validationsEffectuees = Validation::with(['courrier', 'courrier.createur'])
                                          ->where('user_id', $user->id)
                                          ->whereIn('statut', [
                                              Validation::STATUT_VALIDE, 
                                              Validation::STATUT_SIGNE, 
                                              Validation::STATUT_REJETE
                                          ])
                                          ->orderBy('date_validation', 'desc')
                                          ->limit(20)
                                          ->get();
        
        // Statistiques
        $stats = [
            'en_attente' => $validationsEnAttente->count(),
            'en_retard' => $this->compterValidationsEnRetard($validationsEnAttente),
            'effectuees_mois' => Validation::where('user_id', $user->id)
                                          ->whereIn('statut', [Validation::STATUT_VALIDE, Validation::STATUT_SIGNE])
                                          ->whereMonth('date_validation', Carbon::now()->month)
                                          ->count()
        ];
        
        return view('courriers.parapheur', [
            'validationsEnAttente' => $validationsEnAttente,
            'validationsEffectuees' => $validationsEffectuees,
            'stats' => $stats
        ]);
    }
    
    /**
     * Générer un rapport d'activité
     */
    public function rapport(Request $request)
    {
        // Vérifier les permissions (admin ou directeur)
        if (!in_array(Auth::user()->role, ['admin', 'directeur'])) {
            abort(403, 'Accès non autorisé.');
        }
        
        $dateDebut = $request->input('date_debut', Carbon::now()->startOfMonth());
        $dateFin = $request->input('date_fin', Carbon::now()->endOfMonth());
        
        $rapport = [
            'periode' => [
                'debut' => Carbon::parse($dateDebut)->format('d/m/Y'),
                'fin' => Carbon::parse($dateFin)->format('d/m/Y')
            ],
            'statistiques' => [
                'total' => Courrier::whereBetween('created_at', [$dateDebut, $dateFin])->count(),
                'par_type' => Courrier::whereBetween('created_at', [$dateDebut, $dateFin])
                                     ->select('type_dossier', DB::raw('count(*) as total'))
                                     ->groupBy('type_dossier')
                                     ->get()
                                     ->pluck('total', 'type_dossier'),
                'par_statut' => Courrier::whereBetween('created_at', [$dateDebut, $dateFin])
                                       ->select('statut', DB::raw('count(*) as total'))
                                       ->groupBy('statut')
                                       ->get()
                                       ->pluck('total', 'statut'),
                'delai_moyen' => $this->calculerDelaiMoyen($dateDebut, $dateFin),
                'taux_rejet' => $this->calculerTauxRejet($dateDebut, $dateFin)
            ],
            'courriers_recentes' => Courrier::with(['createur', 'service'])
                                           ->whereBetween('created_at', [$dateDebut, $dateFin])
                                           ->orderBy('created_at', 'desc')
                                           ->limit(10)
                                           ->get()
        ];
        
        return view('courriers.rapport', compact('rapport'));
    }
    
    /**
     * Générer une référence unique
     */
    private function generateReference(string $typeDossier): string
    {
        $prefixes = [
            Courrier::TYPE_EXONERATION => 'EXO',
            Courrier::TYPE_DISPENSE_TVA => 'DTVA',
            Courrier::TYPE_REJET => 'REJ',
            Courrier::TYPE_AUTRE => 'DOS'
        ];
        
        $prefix = $prefixes[$typeDossier] ?? 'DOS';
        $year = date('Y');
        $month = date('m');
        
        // Compter les dossiers de ce type pour l'année/mois
        $count = Courrier::where('type_dossier', $typeDossier)
                        ->whereYear('created_at', $year)
                        ->whereMonth('created_at', $month)
                        ->count();
        
        $sequence = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
        
        return "{$prefix}-{$year}{$month}-{$sequence}";
    }
    
    /**
     * Calculer le délai moyen de traitement
     */
    private function calculerDelaiMoyen($dateDebut, $dateFin)
    {
        $courriers = Courrier::whereBetween('created_at', [$dateDebut, $dateFin])
                            ->whereNotNull('date_decision')
                            ->get();
        
        if ($courriers->isEmpty()) {
            return 0;
        }
        
        $totalJours = 0;
        foreach ($courriers as $courrier) {
            $totalJours += $courrier->created_at->diffInDays($courrier->date_decision);
        }
        
        return round($totalJours / $courriers->count(), 1);
    }
    
    /**
     * Calculer le taux de rejet
     */
    private function calculerTauxRejet($dateDebut, $dateFin)
    {
        $total = Courrier::whereBetween('created_at', [$dateDebut, $dateFin])->count();
        $rejetes = Courrier::whereBetween('created_at', [$dateDebut, $dateFin])
                          ->where('statut', Courrier::STATUT_REJETE)
                          ->count();
        
        if ($total == 0) {
            return 0;
        }
        
        return round(($rejetes / $total) * 100, 2);
    }
    
    /**
     * Compter les validations en retard
     */
    private function compterValidationsEnRetard($validations)
    {
        $count = 0;
        foreach ($validations as $validation) {
            if ($validation->courrier && $validation->courrier->date_limite < Carbon::now()) {
                $count++;
            }
        }
        return $count;
    }
}