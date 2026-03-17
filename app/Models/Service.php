<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Service extends Model
{
    protected $fillable = [
        'code',
        'nom',
        'sigle',
        'description',
        'email',
        'telephone',
        'responsable_nom',
        'responsable_email',
        'responsable_telephone',
        'est_actif',
        'ordre_affichage'
    ];
    
    protected $casts = [
        'est_actif' => 'boolean',
        'ordre_affichage' => 'integer'
    ];
    
    // Relations
    public function courriers(): HasMany
    {
        return $this->hasMany(Courrier::class);
    }
    
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
    
    public function parapheurs()
    {
        return $this->hasMany(Parapheur::class);
    }
    public function chefService(): HasOne
    {
        return $this->hasOne(User::class)
                    ->where('role', 'chef_service');
    }
    
    public function agents(): HasMany
    {
        return $this->hasMany(User::class)
                    ->where('role', 'agent');
    }
    
    public function secretaires(): HasMany
    {
        return $this->hasMany(User::class)
                    ->where('role', 'secretaire');
    }
    
    // Scopes
    public function scopeActifs($query)
    {
        return $query->where('est_actif', true);
    }
    
    public function scopeInactifs($query)
    {
        return $query->where('est_actif', false);
    }
    
    public function scopeTries($query)
    {
        return $query->orderBy('ordre_affichage')
                     ->orderBy('nom');
    }
    
    // Accesseurs
    public function getNomCompletAttribute(): string
    {
        if ($this->sigle) {
            return $this->nom . ' (' . $this->sigle . ')';
        }
        return $this->nom;
    }
    
    public function getStatutAttribute(): string
    {
        return $this->est_actif ? 'Actif' : 'Inactif';
    }
    
    public function getCouleurStatutAttribute(): string
    {
        return $this->est_actif ? 'success' : 'secondary';
    }
    
    public function getNombreCourriersAttribute(): int
    {
        return $this->courriers()->count();
    }
    
    public function getNombreUtilisateursAttribute(): int
    {
        return $this->users()->count();
    }
    
    public function getCourriersEnCoursAttribute(): int
    {
        return $this->courriers()
                    ->whereIn('statut', [Courrier::STATUT_ANALYSE, Courrier::STATUT_VALIDATION])
                    ->count();
    }
    
    public function getCourriersEnRetardAttribute(): int
    {
        return $this->courriers()
                    ->whereIn('statut', [Courrier::STATUT_ANALYSE, Courrier::STATUT_VALIDATION])
                    ->where('date_limite', '<', now())
                    ->count();
    }
    
    // Méthodes
    public function peutEtreSupprime(): bool
    {
        // Un service ne peut être supprimé s'il a des courriers ou des utilisateurs
        return $this->courriers()->count() === 0 && 
               $this->users()->count() === 0;
    }
    
    public function desactiver(): void
    {
        $this->update(['est_actif' => false]);
    }
    
    public function activer(): void
    {
        $this->update(['est_actif' => true]);
    }
}