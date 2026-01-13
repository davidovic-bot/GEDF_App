<?php

namespace App\Http\Controllers;

use App\Models\Courrier;
use App\Models\User;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $query = Courrier::with(['createur'])
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
        // Récupérer la liste des services pour les administrateurs
        $services = [];
        if (auth()->user()->hasRole(['admin', 'chef_service'])) {
            $services = Service::all();
        }
        
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
            'service_id' => 'nullable|exists:services,id',
            'pieces_jointes.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png'
        ]);
        
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
            'statut' => 'en_analyse',
            'createur_id' => auth()->id()
        ]);
        
        // Gérer les pièces jointes
        if ($request->hasFile('pieces_jointes')) {
            foreach ($request->file('pieces_jointes') as $file) {
                $path = $file->store('pieces_jointes/' . $courrier->id, 'public');
                
                $courrier->piecesJointes()->create([
                    'nom' => $file->getClientOriginalName(),
                    'chemin' => $path,
                    'type' => $file->getMimeType(),
                    'taille' => $file->getSize(),
                    'user_id' => auth()->id()
                ]);
            }
        }
        
        // Enregistrer dans l'historique
        $courrier->historiques()->create([
            'user_id' => auth()->id(),
            'action' => 'creation',
            'details' => 'Création du dossier fiscal',
            'commentaire' => 'Dossier créé avec la référence ' . $reference
        ]);
        
        return redirect()->route('courriers.show', $courrier)
                        ->with('success', 'Dossier fiscal créé avec succès. Référence : ' . $reference);
    }
    
    /**
     * Afficher un dossier spécifique
     */
    public function show(Courrier $courrier)
    {
        // Charger les relations
        $courrier->load([
            'createur',
            'service',
            'valideurs.user',
            'historiques.user',
            'piecesJointes'
        ]);
        
        // Vérifier les permissions
        $this->authorize('view', $courrier);
        
        return view('courriers.show', compact('courrier'));
    }
    
    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Courrier $courrier)
    {
        // Vérifier les permissions
        $this->authorize('update', $courrier);
        
        $services = Service::all();
        
        return view('courriers.edit', [
            'courrier' => $courrier,
            'types' => Courrier::getTypes(),
            'statuts' => Courrier::getStatuts(),
            'services' => $services
        ]);
    }
    
    /**
     * Mettre à jour un dossier
     */
    public function update(Request $request, Courrier $courrier)
    {
        // Vérifier les permissions
        $this->authorize('update', $courrier);
        
        // Validation
        $validated = $request->validate([
            'type_dossier' => 'required|in:exoneration,dispense_tva,rejet,autre',
            'contribuable_nom' => 'required|string|max:255',
            'contribuable_id_fiscal' => 'required|string|max:50',
            'secteur_activite' => 'nullable|string|max:100',
            'montant_impact' => 'nullable|numeric|min:0',
            'sujet' => 'required|string|max:500',
            'description' => 'required|string',
            'statut' => 'required|in:en_analyse,en_validation,signe,archive',
            'date_limite' => 'nullable|date',
            'service_id' => 'nullable|exists:services,id',
            'motif_rejet' => 'nullable|string|max:1000'
        ]);
        
        // Enregistrer l'ancien statut pour l'historique
        $ancienStatut = $courrier->statut;
        
        // Mettre à jour
        $courrier->update($validated);
        
        // Si le statut passe à "signé", enregistrer la date de décision
        if ($courrier->statut == 'signe' && !$courrier->date_decision) {
            $courrier->update(['date_decision' => Carbon::now()]);
        }
        
        // Enregistrer dans l'historique
        $courrier->historiques()->create([
            'user_id' => auth()->id(),
            'action' => 'modification',
            'details' => 'Mise à jour du dossier',
            'commentaire' => $ancienStatut != $courrier->statut 
                ? "Changement de statut : {$ancienStatut} → {$courrier->statut}"
                : 'Modification des informations'
        ]);
        
        return redirect()->route('courriers.show', $courrier)
                        ->with('success', 'Dossier mis à jour avec succès.');
    }
    
    /**
     * Supprimer un dossier
     */
    public function destroy(Courrier $courrier)
    {
        // Vérifier les permissions
        $this->authorize('delete', $courrier);
        
        // Enregistrer dans l'historique avant suppression
        $courrier->historiques()->create([
            'user_id' => auth()->id(),
            'action' => 'suppression',
            'details' => 'Suppression du dossier',
            'commentaire' => 'Dossier supprimé définitivement'
        ]);
        
        $courrier->delete();
        
        return redirect()->route('courriers.index')
                        ->with('success', 'Dossier supprimé avec succès.');
    }
    
    /**
     * Archiver un dossier
     */
    public function archive(Courrier $courrier)
    {
        // Vérifier que le dossier peut être archivé
        if ($courrier->statut != 'signe') {
            return back()->with('error', 
                'Seuls les dossiers signés peuvent être archivés. Statut actuel : ' . 
                $courrier->libelle_statut);
        }
        
        // Archiver
        $courrier->update([
            'statut' => 'archive',
            'date_archive' => Carbon::now()
        ]);
        
        // Enregistrer dans l'historique
        $courrier->historiques()->create([
            'user_id' => auth()->id(),
            'action' => 'archivage',
            'details' => 'Archivage du dossier',
            'commentaire' => 'Dossier archivé avec succès'
        ]);
        
        return redirect()->route('courriers.archives')
                        ->with('success', 'Dossier archivé avec succès.');
    }
    
    /**
     * Afficher l'historique d'un dossier
     */
    public function historique(Courrier $courrier)
    {
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
        $query = Courrier::where('statut', 'archive')
                        ->with('createur')
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
     * Générer une référence unique
     */
    private function generateReference(string $typeDossier): string
    {
        $prefixes = [
            'exoneration' => 'EXO',
            'dispense_tva' => 'DTVA',
            'rejet' => 'REJ',
            'autre' => 'DOS'
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
}