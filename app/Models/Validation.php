<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Validation extends Model
{
    protected $fillable = [
        'courrier_id',
        'user_id',
        'role_validation',
        'statut',
        'ordre',
        'date_validation',
        'commentaire'
    ];
    
    protected $casts = [
        'date_validation' => 'datetime',
        'ordre' => 'integer'
    ];
    
    // Constantes pour les statuts
    const STATUT_EN_ATTENTE = 'en_attente';
    const STATUT_VALIDE = 'valide';
    const STATUT_SIGNE = 'signe';
    const STATUT_REJETE = 'rejete';
    const STATUT_ANNULE = 'annule';
    
    public static function getStatuts(): array
    {
        return [
            self::STATUT_EN_ATTENTE => 'En attente',
            self::STATUT_VALIDE => 'Validé',
            self::STATUT_SIGNE => 'Signé',
            self::STATUT_REJETE => 'Rejeté',
            self::STATUT_ANNULE => 'Annulé'
        ];
    }
    
    // Constantes pour les rôles
    const ROLE_CHEF_SERVICE = 'chef_service';
    const ROLE_DIRECTEUR = 'directeur';
    
    public static function getRoles(): array
    {
        return [
            self::ROLE_CHEF_SERVICE => 'Chef de Service',
            self::ROLE_DIRECTEUR => 'Directeur'
        ];
    }
    
    // Relations
    public function courrier(): BelongsTo
    {
        return $this->belongsTo(Courrier::class);
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    // Scopes
    public function scopeEnAttente($query)
    {
        return $query->where('statut', self::STATUT_EN_ATTENTE);
    }
    
    public function scopeValidees($query)
    {
        return $query->whereIn('statut', [self::STATUT_VALIDE, self::STATUT_SIGNE]);
    }
    
    public function scopeRejetees($query)
    {
        return $query->where('statut', self::STATUT_REJETE);
    }
    
    public function scopePourRole($query, $role)
    {
        return $query->where('role_validation', $role);
    }
    
    // Accesseurs
    public function getLibelleStatutAttribute(): string
    {
        return self::getStatuts()[$this->statut] ?? $this->statut;
    }
    
    public function getLibelleRoleAttribute(): string
    {
        return self::getRoles()[$this->role_validation] ?? $this->role_validation;
    }
    
    public function getCouleurStatutAttribute(): string
    {
        return match($this->statut) {
            self::STATUT_EN_ATTENTE => 'warning',
            self::STATUT_VALIDE => 'info',
            self::STATUT_SIGNE => 'success',
            self::STATUT_REJETE => 'danger',
            self::STATUT_ANNULE => 'secondary',
            default => 'light'
        };
    }
    
    public function getEstEnRetardAttribute(): bool
    {
        if (!$this->courrier || !$this->courrier->date_limite) {
            return false;
        }
        
        return $this->statut === self::STATUT_EN_ATTENTE && 
               now()->greaterThan($this->courrier->date_limite);
    }
}