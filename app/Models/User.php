<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'matricule',
        'service_id',
        'direction_id',
        'poste',
        'telephone',
        'actif',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relation avec le service
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Validations en attente pour cet utilisateur
     */
    public function validationsEnAttente(): HasMany
    {
        return $this->hasMany(Validation::class)
                    ->where('statut', Validation::STATUT_EN_ATTENTE)
                    ->orderBy('ordre');
    }

    /**
     * Toutes les validations de cet utilisateur
     */
    public function validations(): HasMany
    {
        return $this->hasMany(Validation::class);
    }

    /**
     * Pièces jointes uploadées par cet utilisateur
     */
    public function piecesJointes(): HasMany
    {
        return $this->hasMany(PieceJointe::class);
    }

    // ============================================
    // ACCESSEURS SIMPLIFIÉS POUR TES ROUTES
    // ============================================

    /**
     * Vérifie si l'utilisateur est superadmin (par email)
     */
    public function isSuperAdmin(): bool
    {
        return $this->email === 'superadmin@gedf.com';
    }

    /**
     * Vérifie si l'utilisateur est admin (par email)
     */
    public function isAdmin(): bool
    {
        return in_array($this->email, ['superadmin@gedf.com', 'admin@gedf.com']);
    }

    /**
     * Vérifie si l'utilisateur est secrétaire (par email)
     */
    public function isSecretaire(): bool
    {
        return in_array($this->email, ['superadmin@gedf.com', 'admin@gedf.com', 'secretaire@gedf.com']);
    }

    /**
     * Vérifie si l'utilisateur est gestionnaire (par email)
     */
    public function isGestionnaire(): bool
    {
        return in_array($this->email, ['superadmin@gedf.com', 'admin@gedf.com', 'gestionnaire@gedf.com']);
    }

    /**
     * Vérifie si l'utilisateur est chef de service (par email)
     */
    public function isChefService(): bool
    {
        return in_array($this->email, ['superadmin@gedf.com', 'admin@gedf.com', 'chefservice@gedf.com']);
    }

    /**
     * Vérifie si l'utilisateur est directeur (par email)
     */
    public function isDirecteur(): bool
    {
        return in_array($this->email, ['superadmin@gedf.com', 'admin@gedf.com', 'directeur@gedf.com']);
    }

    /**
     * Vérifie si l'utilisateur est careerinns (par email)
     */
    public function isCareerinns(): bool
    {
        return in_array($this->email, ['superadmin@gedf.com', 'admin@gedf.com', 'careerinns@gedf.com']);
    }

    // ============================================
    // MÉTHODES MÉTIER
    // ============================================

    /**
     * Vérifie si l'utilisateur peut valider un courrier
     */
    public function peutValider(Courrier $courrier): bool
    {
        if ($this->isChefService()) {
            return $courrier->service_id === $this->service_id;
        }
        
        if ($this->isDirecteur()) {
            return true; // Le directeur peut valider tous les dossiers
        }
        
        return false;
    }

    /**
     * Récupère le rôle principal de l'utilisateur
     * (Pour compatibilité avec l'ancien système si besoin)
     */
    public function getRolePrincipalAttribute(): ?string
    {
        // Si tu utilises Spatie plus tard
        $roles = $this->getRoleNames();
        if ($roles->isNotEmpty()) {
            return $roles->first();
        }
        
        // Sinon détermine par email
        if ($this->isSuperAdmin()) return 'superadmin';
        if ($this->isAdmin()) return 'admin';
        if ($this->isSecretaire()) return 'secretaire';
        if ($this->isGestionnaire()) return 'gestionnaire';
        if ($this->isChefService()) return 'chef_service';
        if ($this->isDirecteur()) return 'directeur';
        if ($this->isCareerinns()) return 'careerinns';
        
        return null;
    }

    /**
     * Vérifie si l'utilisateur a au moins un des rôles donnés
     */
    public function hasAnyRoles(array $roles): bool
    {
        foreach ($roles as $role) {
            $method = 'is' . str_replace(' ', '', ucwords(str_replace('_', ' ', $role)));
            if (method_exists($this, $method) && $this->$method()) {
                return true;
            }
        }
        return false;
    }
}