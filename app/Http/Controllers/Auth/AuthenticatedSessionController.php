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

        // 🔥 Redirection selon le rôle
        switch ($user->role_id) {

            case 1:
                return redirect('/dashboard/superadmin');

            case 2:
                return redirect('/dashboard/admin');

            case 3:
                return redirect('/dashboard/secretaires');

            case 4:
                return redirect('/dashboard/gestionnaire');

            case 5:
                return redirect('/dashboard/chef-service');

            case 6:
                return redirect('/dashboard/directeur');

            default:
                Auth::logout();
                abort(403, 'Rôle inconnu');
        }
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