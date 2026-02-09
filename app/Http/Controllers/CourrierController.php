<?php

namespace App\Http\Controllers;

use App\Models\Courrier;
use App\Models\Service;
use App\Models\Document;
use App\Models\HistoriqueAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CourrierController extends Controller
{
    /**
     * Afficher la liste des courriers
     */
   public function index(Request $request)
{
    // Essaie de récupérer des données
    try {
        // Si ton modèle s'appelle Courier
        $courriers = \App\Models\Courrier::all();
    } catch (\Exception $e) {
        // Sinon tableau vide
        $courriers = [];
    }
    
    $stats = [
        'enregistres' => 0,
        'en_analyse' => 0,
        'en_validation' => 0,
        'signes' => 0,
        'total' => 0,
    ];
    
    return view('courriers.index', compact('courriers', 'stats'));
}   /**
     * Enregistrer un nouveau courrier
     */
    public function store(Request $request)
    {
        // Validation
        $validated = $request->validate([
            'expediteur' => 'required|string|max:255',
            'objet' => 'required|string|max:500',
            'type_demande' => 'required|in:exoneration,dispense_tva,autre',
            'date_reception' => 'required|date',
            'service_destinataire_id' => 'required|exists:services,id',
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
            'notes' => 'nullable|string',
            'urgent' => 'boolean'
        ]);
        
        // Générer référence
        $annee = date('Y');
        $count = Courrier::whereYear('created_at', $annee)->count() + 1;
        $reference = 'DRS-' . $annee . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        
        // Créer le courrier
        $courrier = Courrier::create([
            'reference' => $reference,
            'expediteur' => $validated['expediteur'],
            'objet' => $validated['objet'],
            'type_demande' => $validated['type_demande'],
            'date_reception' => $validated['date_reception'],
            'service_destinataire_id' => $validated['service_destinataire_id'],
            'created_by' => auth()->id(),
            'notes_analyse' => $validated['notes'] ?? null,
            'urgent' => $request->has('urgent'),
            'statut' => 'enregistre'
        ]);
        
        // Gérer le document
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                      . '-' . time() . '.' . $file->getClientOriginalExtension();
            
            $path = $file->storeAs('documents/courriers', $filename, 'public');
            
            Document::create([
                'courrier_id' => $courrier->id,
                'nom_fichier' => $file->getClientOriginalName(),
                'chemin_fichier' => $path,
                'extension' => $file->getClientOriginalExtension(),
                'taille' => $file->getSize() / 1024 / 1024, // Convertir en Mo
                'type_document' => 'original',
                'uploaded_by' => auth()->id(),
                'description' => 'Document original du courrier'
            ]);
        }
        
        // Enregistrer dans l'historique
        HistoriqueAction::create([
            'courrier_id' => $courrier->id,
            'utilisateur_id' => auth()->id(),
            'action' => 'enregistrement',
            'commentaire' => 'Courrier enregistré par ' . auth()->user()->name
        ]);
        
        return redirect()->route('courriers.show', $courrier)
            ->with('success', 'Courrier enregistré avec succès. Référence: ' . $reference);
    }

    /**
     * Afficher un courrier spécifique
     */
    public function show(Courrier $courrier)
    {
        // Vérifier les permissions (à compléter avec une Policy)
        $courrier->load(['documents', 'historique.utilisateur', 'serviceDestinataire', 'agentAttribue', 'createur']);
        
        return view('courriers.show', compact('courrier'));
    }

    /**
     * Attribuer un courrier à un agent
     */
    public function attribuer(Request $request, Courrier $courrier)
    {
        // Seulement chef de service ou secrétaire
        if (!auth()->user()->hasAnyRole(['chef_service', 'secretaire', 'superadmin'])) {
            abort(403);
        }
        
        $request->validate([
            'agent_id' => 'required|exists:users,id'
        ]);
        
        $courrier->update([
            'agent_attribue_id' => $request->agent_id,
            'statut' => 'en_analyse'
        ]);
        
        // Historique
        HistoriqueAction::create([
            'courrier_id' => $courrier->id,
            'utilisateur_id' => auth()->id(),
            'action' => 'attribution',
            'commentaire' => 'Courrier attribué à l\'agent ' . \App\Models\User::find($request->agent_id)->name
        ]);
        
        return back()->with('success', 'Courrier attribué avec succès');
    }

    public function archives()
{
    // Récupérer les courriers archivés
    $courriers = Courrier::where('statut', 'archive')
        ->orderBy('date_archivage', 'desc')
        ->get();
    
    return view('courriers.archives', compact('courriers'));
}

    // ... autres méthodes à implémenter
}