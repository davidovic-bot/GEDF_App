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
        $user = auth()->user();
        $query = Courrier::query();

        // Filtrage par service (agents et chefs)
        if ($user->hasRole('agent') || $user->hasRole('chef_service')) {
            $query->where('service_emetteur_id', $user->service_id);
        }

        // Filtres de l’utilisateur
        if ($request->filled('statut')) {
            $query->where('statut_general', $request->statut);
        }

        if ($request->filled('type_demande')) {
            $query->where('type_demande', $request->type_demande);
        }

        if ($request->filled('date_debut')) {
            $query->whereDate('date_reception', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('date_reception', '<=', $request->date_fin);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('expediteur', 'like', "%{$search}%")
                  ->orWhere('objet', 'like', "%{$search}%");
            });
        }

        $courriers = $query->orderBy('date_reception', 'desc')->paginate(15);

        // Statistiques réelles
        $stats = [
            'enregistres' => Courrier::where('statut_general', 'enregistre')->count(),
            'en_analyse' => Courrier::where('statut_general', 'en_analyse')->count(),
            'en_validation' => Courrier::whereIn('statut_general', ['en_validation_chef', 'en_validation_directeur'])->count(),
            'signes' => Courrier::where('statut_general', 'signe')->count(),
            'total' => Courrier::count(),
        ];

        return view('courriers.index', compact('courriers', 'stats'));
    }

    /**
     * Enregistrer un nouveau courrier
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'expediteur' => 'required|string|max:255',
            'objet' => 'required|string|max:500',
            'type_demande' => 'required|in:exoneration,dispense_tva,autre',
            'date_reception' => 'required|date',
            'service_destinataire_id' => 'required|exists:services,id',
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'notes' => 'nullable|string',
            'urgent' => 'boolean'
        ]);

        $annee = date('Y');
        $count = Courrier::whereYear('created_at', $annee)->count() + 1;
        $reference = 'DRS-' . $annee . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        $courrier = Courrier::create([
            'reference' => $reference,
            'expediteur' => $validated['expediteur'],
            'objet' => $validated['objet'],
            'type_demande' => $validated['type_demande'],
            'date_reception' => $validated['date_reception'],
            'service_emetteur_id' => $validated['service_destinataire_id'],
            'created_by' => auth()->id(),
            'notes_analyse' => $validated['notes'] ?? null,
            'urgent' => $request->has('urgent'),
            'statut_general' => 'enregistre'
        ]);

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
                'taille' => $file->getSize() / 1024 / 1024,
                'type_document' => 'original',
                'uploaded_by' => auth()->id(),
                'description' => 'Document original du courrier'
            ]);
        }

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
        $courrier->load(['documents', 'historique.utilisateur', 'serviceDestinataire', 'agentAttribue', 'createur']);
        return view('courriers.show', compact('courrier'));
    }

    /**
     * Attribuer un courrier à un agent
     */
    public function attribuer(Request $request, Courrier $courrier)
    {
        if (!auth()->user()->hasAnyRole(['chef_service', 'secretaire', 'superadmin'])) {
            abort(403);
        }

        $request->validate([
            'agent_id' => 'required|exists:users,id'
        ]);

        $courrier->update([
            'agent_attribue_id' => $request->agent_id,
            'statut_general' => 'en_analyse'
        ]);

        HistoriqueAction::create([
            'courrier_id' => $courrier->id,
            'utilisateur_id' => auth()->id(),
            'action' => 'attribution',
            'commentaire' => 'Courrier attribué à l\'agent ' . \App\Models\User::find($request->agent_id)->name
        ]);

        return back()->with('success', 'Courrier attribué avec succès');
    }

    /**
     * Afficher les courriers archivés
     */
    public function archives()
    {
        $courriers = Courrier::where('statut_general', 'archive')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('courriers.archives', compact('courriers'));
    }
}