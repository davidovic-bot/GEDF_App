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

    public function create()
    {
    $services = \App\Models\Service::all();
    return view('courriers.create', compact('services'));
    }

    /**
     * Enregistrer un nouveau courrier
     */
    public function store(Request $request)
{
    // Validation
    $request->validate([
        'beneficiaire' => 'required|string|max:255',
        'nif' => 'nullable|string|max:50',
        'date_reception' => 'required|date',
        'fichier' => 'nullable|file|mimes:pdf|max:5120', // 5MB max
    ]);

    // Générer un numéro unique
    $annee = date('Y');
    $mois = date('m');
    $count = Courrier::whereYear('created_at', $annee)
                     ->whereMonth('created_at', $mois)
                     ->count() + 1;
    $numero = 'CR-' . $annee . $mois . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

    // Créer le courrier
    $courrier = Courrier::create([
        'numero' => $numero,
        'beneficiaire' => $request->beneficiaire,
        'nif' => $request->nif,
        'date_reception' => $request->date_reception,
        'statut_general' => 'enregistre',
        'created_by' => auth()->id(),
    ]);

    // Gestion du fichier joint
    if ($request->hasFile('fichier')) {
        $file = $request->file('fichier');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('courriers', $filename, 'public');

        \App\Models\Document::create([
            'courrier_id' => $courrier->id,
            'nom_fichier' => $file->getClientOriginalName(),
            'chemin_fichier' => $path,
            'extension' => $file->getClientOriginalExtension(),
            'taille' => $file->getSize(),
            'uploaded_by' => auth()->id(),
        ]);
    }

    return redirect()->route('courriers.index')->with('success', 'Courrier enregistré. Référence : ' . $numero);
}
    /**
     * Afficher un courrier spécifique
     */
    public function show(Courrier $courrier)
    {
        $courrier->load(['documents', 'createur']);
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

    public function edit(Courrier $courrier)
    {
        $services = \App\Models\Service::all();
        return view('courriers.edit', compact('courrier', 'services'));
    }

    public function update(Request $request, Courrier $courrier)
    {
       $courrier->update($request->only(['beneficiaire', 'nif', 'objet', 'type_demande', 'service_emetteur_id', 'date_reception', 'motif']));
       return redirect()->route('courriers.show', $courrier)->with('success', 'Courrier modifié.');
    }

    public function transmettre(Courrier $courrier)
    {
       $courrier->update(['statut_general' => 'en_analyse']);
       return redirect()->route('courriers.index')->with('success', 'Courrier transmis à l’agent.');
    }

    public function analyse(Courrier $courrier)
    {
    return view('courriers.analyse', compact('courrier'));
    }

    public function transmettreChef(Request $request, Courrier $courrier)
    {
    $courrier->update([
        'statut_general' => 'en_validation_chef',
        'motif' => $request->motif,
        'type_exoneration' => $request->type_exoneration,
    ]);

    return redirect()->route('courriers.index')->with('success', 'Dossier transmis au chef de service.');
    }

    public function valider(Courrier $courrier)
    {
    $courrier->update(['statut_general' => 'en_validation_directeur']);
    return redirect()->route('courriers.index')->with('success', 'Dossier validé.');
    }

    public function instructionDrs(Courrier $courrier)
{
    $services = Service::all();
    return view('courriers.instruction_drs', compact('courrier', 'services'));
}

public function storeInstructionDrs(Request $request, Courrier $courrier)
{

    $courrier->update([
        'objet' => $request->objet,
        'type_demande' => $request->type_demande,
        'secteur' => $request->secteur,
        'service_emetteur_id' => $request->service_emetteur_id,
        'instruction_drs' => $request->instruction_drs,
        'statut_general' => 'en_analyse'
    ]);
    return redirect()->route('courriers.index')->with('success', 'Instruction envoyée au chef de service.');
}

public function approuver(Courrier $courrier)
{
    $courrier->update(['statut_general' => 'valide']);
    return redirect()->route('courriers.index')->with('success', 'Dossier approuvé par le DRS.');
}

}