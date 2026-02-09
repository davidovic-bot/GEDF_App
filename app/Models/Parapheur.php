<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Parapheur extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'objet',
        'type_courrier_id',
        'expediteur',
        'service_expediteur',
        'date_reception',
        'date_limite',
        'priorite',
        'statut_id',
        'created_by',
        'current_role_id',
        // Ajouts pour correspondre à la vue
        'courrier_id',
        'agent_attribue_id',
        'chef_service_id',
        'directeur_id',
        'date_limite_traitement',
        'numero_parapheur',
    ];

    protected $casts = [
        'date_reception' => 'date',
        'date_limite' => 'date',
        'date_limite_traitement' => 'datetime',
    ];

    // ==================== RELATIONS EXISTANTES ====================
    
    public function statut(): BelongsTo
    {
        return $this->belongsTo(ParapheurStatut::class, 'statut_id');
    }

    public function typeCourrier(): BelongsTo
    {
        return $this->belongsTo(TypeCourrier::class, 'type_courrier_id');
    }

    public function createur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function currentRole(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'current_role_id');
    }

    public function fichiers(): HasMany
    {
        return $this->hasMany(ParapheurFichier::class, 'parapheur_id');
    }

    public function historique(): HasMany
    {
        return $this->hasMany(ParapheurHistorique::class, 'parapheur_id');
    }

    // ==================== NOUVELLES RELATIONS POUR LA VUE ====================
    
    /**
     * Relation avec le courrier (si ta table a courrier_id)
     */
    public function courrier(): BelongsTo
    {
        return $this->belongsTo(Courrier::class, 'courrier_id');
    }

    /**
     * Relation avec l'agent attribué
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_attribue_id');
    }

    /**
     * Relation avec le chef de service
     */
    public function chefService(): BelongsTo
    {
        return $this->belongsTo(User::class, 'chef_service_id');
    }

    /**
     * Relation avec le directeur
     */
    public function directeur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'directeur_id');
    }

    /**
     * Relation avec le service émetteur (si tu as cette table)
     */
    public function serviceEmetteur()
    {
        // À adapter selon ta structure
        // Exemple: return $this->belongsTo(Service::class, 'service_expediteur_id');
        return null;
    }

    // ==================== ACCESSORS POUR LA VUE ====================
    
    /**
     * Accessor pour numero_parapheur (ta vue utilise ce nom)
     */
    public function getNumeroParapheurAttribute()
    {
        return $this->reference ?? $this->attributes['numero_parapheur'] ?? 'N/A';
    }

    /**
     * Accessor pour date_limite_traitement (ta vue utilise ce nom)
     */
    public function getDateLimiteTraitementAttribute()
    {
        if (isset($this->attributes['date_limite_traitement'])) {
            return $this->attributes['date_limite_traitement'];
        }
        
        // Fallback sur date_limite
        return $this->date_limite;
    }

    /**
     * Accessor pour priorite (ta vue utilise 'urgent'/'normal')
     */
    public function getPrioriteAttribute($value)
    {
        // Convertit tes valeurs de priorité si nécessaire
        if ($value === 'haut' || $value === 'urgent') {
            return 'urgent';
        }
        return 'normal';
    }

    // ==================== MÉTHODES POUR LA VUE ====================
    
    /**
     * Vérifie si le parapheur est en retard
     */
    public function estEnRetard(): bool
    {
        $dateLimite = $this->date_limite_traitement ?? $this->date_limite;
        
        if (!$dateLimite) {
            return false;
        }
        
        return now()->gt($dateLimite);
    }

    /**
     * Vérifie si l'utilisateur peut valider ce parapheur
     * À ADAPTER selon tes règles métier
     */
    public function peutEtreValidePar($user): bool
    {
        // Exemple de logique :
        // - Chef de service peut valider si statut = 'en_attente_chef_service'
        // - Directeur peut valider si statut = 'en_attente_directeur'
        
        if (!$user || !$this->statut) {
            return false;
        }
        
        $statutCode = $this->statut->code ?? '';
        
        if ($user->hasRole('chef_service') && $statutCode === 'attente_validation') {
            return true;
        }
        
        if ($user->hasRole('directeur') && $statutCode === 'attente_signature') {
            return true;
        }
        
        return false;
    }

    /**
     * Vérifie si l'utilisateur peut signer ce parapheur
     * À ADAPTER selon tes règles métier
     */
    public function peutEtreSignePar($user): bool
    {
        if (!$user || !$this->statut) {
            return false;
        }
        
        $statutCode = $this->statut->code ?? '';
        
        // Seul le directeur général peut signer
        if ($user->hasRole('directeur') && $statutCode === 'attente_signature') {
            return true;
        }
        
        return false;
    }

    // ==================== SCOPES UTILES ====================
    
    /**
     * Scope pour les parapheurs en attente
     */
    public function scopeEnAttente($query)
    {
        return $query->whereHas('statut', function($q) {
            $q->where('code', 'like', 'attente%');
        });
    }

    /**
     * Scope pour les parapheurs en retard
     */
    public function scopeEnRetard($query)
    {
        return $query->where(function($q) {
            $q->where('date_limite', '<', now())
              ->orWhere('date_limite_traitement', '<', now());
        });
    }

    /**
     * Scope pour les parapheurs urgents
     */
    public function scopeUrgents($query)
    {
        return $query->where('priorite', 'urgent')
                     ->orWhere('priorite', 'haut');
    }

    // ==================== MÉTHODES D'AIDE ====================
    
    /**
     * Génère un numéro de parapheur si vide
     */
    public static function genererNumeroParapheur(): string
    {
        $date = now()->format('Ymd');
        $count = self::whereDate('created_at', today())->count() + 1;
        
        return "PAR-{$date}-" . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Vérifie si le parapheur peut être transmis
     */
    public function peutEtreTransmis(): bool
    {
        $statutCode = $this->statut->code ?? '';
        
        return in_array($statutCode, ['creer', 'analyse', 'valide_cs']);
    }

    /**
     * Récupère le prochain statut possible
     */
    public function getProchainStatut()
    {
        $statutCode = $this->statut->code ?? '';
        
        $transitions = [
            'creer' => 'analyse',
            'analyse' => 'attente_validation',
            'attente_validation' => 'valide_cs',
            'valide_cs' => 'attente_signature',
            'attente_signature' => 'signe',
        ];
        
        return $transitions[$statutCode] ?? null;
    }
}