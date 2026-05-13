<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Affiche la liste des services
     */
    public function index()
    {
    $services = Service::with(['users', 'courriers'])
        ->orderBy('code') // ou orderBy('nom') si tu préfères
        ->get();
    
    return view('administration.services.index', compact('services'));
    }
    /**
     * Affiche le formulaire de création
     */
    public function create()
    {
        $chefs = User::where('role', 'chef_service')->get();
        return view('administration.services.create', compact('chefs'));
    }

    /**
     * Enregistre un nouveau service
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:services',
            'nom' => 'required|string|max:255',
            'sigle' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'email' => 'nullable|email',
            'telephone' => 'nullable|string|max:20',
            'responsable_nom' => 'nullable|string|max:255',
            'responsable_email' => 'nullable|email',
            'responsable_telephone' => 'nullable|string|max:20',
            'est_actif' => 'boolean',
            'ordre_affichage' => 'integer'
        ]);

        $service = Service::create($validated);

        return redirect()
            ->route('admin.services.index')
            ->with('success', 'Service créé avec succès.');
    }

    /**
     * Affiche les détails d'un service
     */
    public function show(Service $service)
    {
        $service->load(['users', 'courriers' => function($q) {
            $q->latest()->limit(10);
        }]);
        
        return view('administration.services.show', compact('service'));
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit(Service $service)
    {
        $chefs = User::where('role', 'chef_service')->get();
        return view('administration.services.edit', compact('service', 'chefs'));
    }

    /**
     * Met à jour un service
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:services,code,' . $service->id,
            'nom' => 'required|string|max:255',
            'sigle' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'email' => 'nullable|email',
            'telephone' => 'nullable|string|max:20',
            'responsable_nom' => 'nullable|string|max:255',
            'responsable_email' => 'nullable|email',
            'responsable_telephone' => 'nullable|string|max:20',
            'est_actif' => 'boolean',
            'ordre_affichage' => 'integer'
        ]);

        $service->update($validated);

        return redirect()
            ->route('admin.services.index')
            ->with('success', 'Service modifié avec succès.');
    }

    /**
     * Supprime un service
     */
    public function destroy(Service $service)
    {
        if (!$service->peutEtreSupprime()) {
            return back()->with('error', 
                'Ce service ne peut pas être supprimé car il contient des courriers ou des utilisateurs.');
        }

        $service->delete();

        return redirect()
            ->route('admin.services.index')
            ->with('success', 'Service supprimé avec succès.');
    }

    /**
     * Active un service
     */
    public function activer(Service $service)
    {
        $service->activer();
        
        return back()->with('success', 'Service activé avec succès.');
    }

    /**
     * Désactive un service
     */
    public function desactiver(Service $service)
    {
        $service->desactiver();
        
        return back()->with('success', 'Service désactivé avec succès.');
    }
}