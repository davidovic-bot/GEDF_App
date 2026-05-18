<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Courrier extends Model
{
    // Renommez éventuellement la table si nécessaire
    // protected $table = 'dossiers_fiscaux';
    
   protected $fillable = [
    'numero',
    'reference',
    'beneficiaire',
    'nif',
    'objet',
    'type_demande',
    'service_emetteur_id',
    'date_reception',
    'statut_general',
    'created_by',
];
    protected $casts = [
        'date_limite' => 'date',
        'date_decision' => 'datetime',
        'date_archive' => 'datetime',
        'montant_impact' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    // Constantes pour les types de dossier
    const TYPE_EXONERATION = 'exoneration';
    const TYPE_DISPENSE_TVA = 'dispense_tva';
    const TYPE_REJET = 'rejet';
    const TYPE_AUTRE = 'autre';
    
    public static function getTypes(): array
    {
        return [
            self::TYPE_EXONERATION => 'Demande d\'exonération',
            self::TYPE_DISPENSE_TVA => 'Demande de dispense TVA',
            self::TYPE_REJET => 'Proposition de rejet',
            self::TYPE_AUTRE => 'Autre dossier fiscal'
        ];
    }
    
    // Constantes pour les statuts
    const STATUT_ANALYSE = 'en_analyse';
    const STATUT_VALIDATION = 'en_validation';
    const STATUT_SIGNE = 'signe';
    const STATUT_ARCHIVE = 'archive';
    
    public static function getStatuts(): array
    {
        return [
            self::STATUT_ANALYSE => 'En analyse',
            self::STATUT_VALIDATION => 'En validation',
            self::STATUT_SIGNE => 'Signé',
            self::STATUT_ARCHIVE => 'Archivé'
        ];
    }
    
    // Relations
    public function createur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'createur_id');
    }
    
    public function valideurs(): HasMany
    {
        return $this->hasMany(Validation::class, 'courrier_id');
    }
    
    public function historiques(): HasMany
    {
        return $this->hasMany(HistoriqueCourrier::class, 'courrier_id')
                    ->orderBy('created_at', 'desc');
    }
    
    public function piecesJointes(): HasMany
    {
        return $this->hasMany(PieceJointe::class, 'courrier_id');
    }
    
    // Scopes utiles pour les filtres
    public function scopeEnRetard($query)
    {
        return $query->where('date_limite', '<', now())
                    ->whereIn('statut', [self::STATUT_ANALYSE, self::STATUT_VALIDATION]);
    }
    
    public function scopeParType($query, $type)
    {
        return $query->where('type_dossier', $type);
    }
    
    public function scopeParStatut($query, $statut)
    {
        return $query->where('statut', $statut);
    }
    
    public function scopeArchives($query)
    {
        return $query->where('statut', self::STATUT_ARCHIVE);
    }
    
    // Accesseurs
    public function getEstEnRetardAttribute(): bool
    {
        return $this->date_limite && 
               now()->greaterThan($this->date_limite) &&
               in_array($this->statut, [self::STATUT_ANALYSE, self::STATUT_VALIDATION]);
    }
    
    public function getLibelleTypeAttribute(): string
    {
        return self::getTypes()[$this->type_dossier] ?? $this->type_dossier;
    }
    
    public function getLibelleStatutAttribute(): string
    {
        return self::getStatuts()[$this->statut] ?? $this->statut;
    }
    
    public function getCouleurStatutAttribute(): string
    {
        return match($this->statut) {
            self::STATUT_ANALYSE => 'primary',
            self::STATUT_VALIDATION => 'warning',
            self::STATUT_SIGNE => 'success',
            self::STATUT_ARCHIVE => 'secondary',
            default => 'light'
        };
    }

    public function parapheurs(): HasMany
{
    return $this->hasMany(Parapheur::class);
}

// Relation avec les fichiers de parapheur
public function fichiersParapheur(): HasMany
{
    return $this->hasManyThrough(
        FichierParapheur::class,
        Parapheur::class,
        'courrier_id', // Clé étrangère sur parapheurs
        'parapheur_id', // Clé étrangère sur fichier_parapheurs
        'id', // Clé locale sur courriers
        'id' // Clé locale sur parapheurs
    );
}

public function peutEtreValidePar($user)
{
    // Seul un chef de service ou un directeur peut valider
    return $user->hasRole(['chef_service', 'directeur']);
}

public function documents()
{
    return $this->hasMany(\App\Models\Document::class);
}

public function service_emetteur()
{
    return $this->belongsTo(Service::class, 'service_emetteur_id');
}
}