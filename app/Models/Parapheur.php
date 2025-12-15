<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Parapheur extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'parapheurs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'reference',
        'objet',
        'description',
        'statut',
        'priorite',
        'confidentialite',
        'date_creation',
        'date_echeance',
        'date_validation',
        'date_rejet',
        'createur_id',
        'service_id',
        'direction_id',
        'responsable_actuel_id',
        'etape_actuelle',
        'etapes_total',
        'workflow',
        'motif_rejet',
        'notes_internes',
        'notifier_createur',
        'notifier_responsable',
        'en_retard'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_creation' => 'datetime',
        'date_echeance' => 'datetime',
        'date_validation' => 'datetime',
        'date_rejet' => 'datetime',
        'en_retard' => 'boolean',
        'notifier_createur' => 'boolean',
        'notifier_responsable' => 'boolean',
        'etape_actuelle' => 'integer',
        'etapes_total' => 'integer',
    ];

    /**
     * Get the creator of the parapheur.
     */
    public function createur()
    {
        return $this->belongsTo(User::class, 'createur_id');
    }

    /**
     * Get the current responsible.
     */
    public function responsableActuel()
    {
        return $this->belongsTo(User::class, 'responsable_actuel_id');
    }

    /**
     * Get the service.
     */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /**
     * Get the direction.
     */
    public function direction()
    {
        return $this->belongsTo(Direction::class, 'direction_id');
    }

    /**
     * Get the files attached to the parapheur.
     */
    public function fichiers()
    {
        return $this->hasMany(FichierParapheur::class, 'parapheur_id');
    }

    /**
     * Get the history of the parapheur.
     */
    public function historiques()
    {
        return $this->hasMany(HistoriqueParapheur::class, 'parapheur_id');
    }

    /**
     * Scope a query to only include parapheurs in a given status.
     */
    public function scopeWhereStatut($query, $statut)
    {
        return $query->where('statut', $statut);
    }

    /**
     * Scope a query to only include parapheurs with high priority.
     */
    public function scopeWherePriorite($query, $priorite)
    {
        return $query->where('priorite', $priorite);
    }

    /**
     * Check if the parapheur is late.
     */
    public function getEstEnRetardAttribute()
    {
        if (!$this->date_echeance) {
            return false;
        }
        
        return now()->greaterThan($this->date_echeance) && 
               !in_array($this->statut, ['valide', 'rejete', 'archive']);
    }

    /**
     * Get the human-readable status.
     */
    public function getStatutLibelleAttribute()
    {
        $statuts = [
            'brouillon' => 'Brouillon',
            'en_attente' => 'En attente',
            'en_cours' => 'En cours de traitement',
            'valide' => 'Validé',
            'rejete' => 'Rejeté',
            'en_retard' => 'En retard',
            'archive' => 'Archivé'
        ];
        
        return $statuts[$this->statut] ?? $this->statut;
    }

    /**
     * Get the human-readable priority.
     */
    public function getPrioriteLibelleAttribute()
    {
        $priorites = [
            'basse' => 'Basse',
            'normale' => 'Normale',
            'haute' => 'Haute',
            'urgente' => 'Urgente'
        ];
        
        return $priorites[$this->priorite] ?? $this->priorite;
    }

    /**
     * Check if the parapheur can be validated by the given user.
     */
    public function peutEtreValidePar($user)
    {
        return $this->responsable_actuel_id === $user->id && 
               in_array($this->statut, ['en_attente', 'en_cours']);
    }

    /**
     * Check if the parapheur can be rejected by the given user.
     */
    public function peutEtreRejetePar($user)
    {
        return $this->responsable_actuel_id === $user->id && 
               in_array($this->statut, ['en_attente', 'en_cours']);
    }

    /**
     * Get the next step in the workflow.
     */
    public function getProchaineEtapeAttribute()
    {
        if ($this->etape_actuelle >= $this->etapes_total) {
            return null;
        }
        
        return $this->etape_actuelle + 1;
    }

    /**
     * Get the progress percentage.
     */
    public function getProgressionAttribute()
    {
        if ($this->etapes_total === 0) {
            return 0;
        }
        
        return round(($this->etape_actuelle / $this->etapes_total) * 100);
    }
}