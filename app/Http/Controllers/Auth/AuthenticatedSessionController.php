<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Affiche la page de connexion.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Gère la tentative de connexion.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validation des champs
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Tentative d’authentification
        if (!Auth::attempt($credentials)) {
            return back()->withErrors([
                'email' => 'Identifiants incorrects.',
            ]);
        }

        // Sécurisation de la session
        $request->session()->regenerate();

        // Récupérer l'utilisateur connecté
        $user = Auth::user();

        // 🔥 REDIRECTION SIMPLIFIÉE PAR EMAIL
        // SUPERADMIN
        if ($user->email === 'superadmin@gedf.com') {
            return redirect('/superadmin');
        }
        
        // ADMIN
        if ($user->email === 'admin@gedf.com') {
            return redirect('/admin');
        }
        
        // SECRÉTAIRE
        if ($user->email === 'secretaire@gedf.com') {
            return redirect('/secretaire');
        }
        
        // GESTIONNAIRE
        if ($user->email === 'gestionnaire@gedf.com') {
            return redirect('/gestionnaire');
        }
        
        // CHEF SERVICE
        if ($user->email === 'chefservice@gedf.com') {
            return redirect('/chef-service');
        }
        
        // DIRECTEUR
        if ($user->email === 'directeur@gedf.com') {
            return redirect('/directeur');
        }
        
        // CAREERINNS
        if ($user->email === 'careerinns@gedf.com') {
            // Redirige vers une page spécifique si tu en as une
            return redirect('/dashboard');
        }

        // Par défaut : dashboard Breeze
        return redirect('/dashboard');
    }

    /**
     * Déconnecter l'utilisateur.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}