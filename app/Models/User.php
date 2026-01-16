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

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // Dans la classe User

// Relation avec le service
public function service(): BelongsTo
{
    return $this->belongsTo(Service::class);
}

// Validations en attente
public function validationsEnAttente(): HasMany
{
    return $this->hasMany(Validation::class)
                ->where('statut', Validation::STATUT_EN_ATTENTE)
                ->orderBy('ordre');
}

// Toutes les validations
public function validations(): HasMany
{
    return $this->hasMany(Validation::class);
}

// Pièces jointes uploadées
public function piecesJointes(): HasMany
{
    return $this->hasMany(PieceJointe::class);
}

// Accesseurs pour les rôles
public function getEstChefServiceAttribute(): bool
{
    return $this->role === 'chef_service';
}

public function getEstDirecteurAttribute(): bool
{
    return $this->role === 'directeur';
}

public function getEstAgentAttribute(): bool
{
    return $this->role === 'agent';
}

public function getEstSecretaireAttribute(): bool
{
    return $this->role === 'secretaire';
}

public function getEstAdminAttribute(): bool
{
    return $this->role === 'admin';
}

// Méthodes de vérification
public function peutValider(Courrier $courrier): bool
{
    if ($this->est_chef_service) {
        return $courrier->service_id === $this->service_id;
    }
    
    if ($this->est_directeur) {
        return true; // Le directeur peut valider tous les dossiers
    }
    
    return false;
}
}