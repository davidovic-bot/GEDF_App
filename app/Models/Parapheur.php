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
        // Nouveaux champs pour les pièces justificatives
        'tableau_factures',
        'factures',
        'montant_tva',
        'montant_css',
        'montant_total',
        'verifie_par',
        'verifie_le',
        'controle_par',
        'controle_le',
        'visa_final_par',
        'visa_final_le',
        'motif_rejet',
        'type_attestation', 
    ];

    protected $casts = [
        'date_reception' => 'date',
        'date_limite' => 'date',
        'date_limite_traitement' => 'datetime',
        'factures' => 'array', // Pour décoder automatiquement le JSON
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

    // ==================== RELATIONS VERS LES UTILISATEURS POUR LE CIRCUIT ====================

    /**
     * Relation avec l'utilisateur qui a vérifié les factures (Agent)
     */
    public function verifiePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verifie_par');
    }

    /**
     * Relation avec l'utilisateur qui a contrôlé la régularité (Chef)
     */
    public function controlePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'controle_par');
    }

    /**
     * Relation avec l'utilisateur qui a apposé le visa final (Directeur)
     */
    public function visaFinalPar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'visa_final_par');
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

    // ==================== ACCESSORS POUR LES MONTANTS ====================

    /**
     * Accessor pour le montant TVA formaté
     */
    public function getMontantTvaFormateAttribute()
    {
        if (!$this->montant_tva) {
            return '0 FCFA';
        }
        return number_format($this->montant_tva, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Accessor pour le montant CSS formaté
     */
    public function getMontantCssFormateAttribute()
    {
        if (!$this->montant_css) {
            return '0 FCFA';
        }
        return number_format($this->montant_css, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Accessor pour le montant total formaté
     */
    public function getMontantTotalFormateAttribute()
    {
        if (!$this->montant_total) {
            return '0 FCFA';
        }
        return number_format($this->montant_total, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Accessor pour les factures décodées
     */
    public function getFacturesListeAttribute()
    {
        return json_decode($this->factures, true) ?? [];
    }

    /**
     * Accessor pour le nom du vérificateur
     */
    public function getVerifieParNomAttribute()
    {
        return $this->verifiePar ? $this->verifiePar->name : 'Non vérifié';
    }

    /**
     * Accessor pour le nom du contrôleur
     */
    public function getControleParNomAttribute()
    {
        return $this->controlePar ? $this->controlePar->name : 'Non contrôlé';
    }

    /**
     * Accessor pour le nom du signataire du visa final
     */
    public function getVisaFinalParNomAttribute()
    {
        return $this->visaFinalPar ? $this->visaFinalPar->name : 'Non signé';
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

    /**
     * Vérifie si le parapheur a toutes les pièces justificatives
     */
    public function aToutesLesPieces(): bool
    {
        return !empty($this->tableau_factures) && !empty($this->factures);
    }

    /**
     * Vérifie si les factures ont été vérifiées
     */
    public function estVerifie(): bool
    {
        return !empty($this->verifie_par) && !empty($this->verifie_le);
    }

    /**
     * Vérifie si le contrôle de régularité a été fait
     */
    public function estControle(): bool
    {
        return !empty($this->controle_par) && !empty($this->controle_le);
    }

    /**
     * Vérifie si le visa final a été apposé
     */
    public function aVisaFinal(): bool
    {
        return !empty($this->visa_final_par) && !empty($this->visa_final_le);
    }

    public function getTypeAttestationLibelleAttribute()
{
    $types = [
        'exoneration' => 'Exonération ouverte (TVA, CSS, etc.)',
        'dispense'    => 'Dispense ouverte (TVA, CSS, etc.)',
    ];

    return $types[$this->type_attestation] ?? $this->type_attestation;
}
}