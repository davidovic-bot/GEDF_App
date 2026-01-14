<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PieceJointe extends Model
{
    protected $fillable = [
        'courrier_id',
        'user_id',
        'nom_fichier',
        'chemin_fichier',
        'mime_type',
        'taille',
        'description',
        'categorie',
        'est_obligatoire'
    ];
    
    protected $casts = [
        'est_obligatoire' => 'boolean',
        'taille' => 'integer'
    ];
    
    // Constantes pour les catégories
    const CATEGORIE_JUSTIFICATIF = 'justificatif';
    const CATEGORIE_CONTRAT = 'contrat';
    const CATEGORIE_FACTURE = 'facture';
    const CATEGORIE_IDENTITE = 'identite';
    const CATEGORIE_AUTRE = 'autre';
    
    public static function getCategories(): array
    {
        return [
            self::CATEGORIE_JUSTIFICATIF => 'Justificatif',
            self::CATEGORIE_CONTRAT => 'Contrat',
            self::CATEGORIE_FACTURE => 'Facture',
            self::CATEGORIE_IDENTITE => 'Pièce d\'identité',
            self::CATEGORIE_AUTRE => 'Autre'
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
    public function scopeParCategorie($query, $categorie)
    {
        return $query->where('categorie', $categorie);
    }
    
    public function scopeObligatoires($query)
    {
        return $query->where('est_obligatoire', true);
    }
    
    // Accesseurs
    public function getUrlAttribute(): string
    {
        return Storage::url($this->chemin_fichier);
    }
    
    public function getTailleFormatteeAttribute(): string
    {
        $bytes = $this->taille;
        
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
    
    public function getExtensionAttribute(): string
    {
        return pathinfo($this->nom_fichier, PATHINFO_EXTENSION);
    }
    
    public function getIconeAttribute(): string
    {
        return match($this->mime_type) {
            'application/pdf' => 'fas fa-file-pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'fas fa-file-word',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'fas fa-file-excel',
            'image/jpeg',
            'image/png',
            'image/gif' => 'fas fa-file-image',
            default => 'fas fa-file'
        };
    }
    
    public function getCouleurIconeAttribute(): string
    {
        return match($this->mime_type) {
            'application/pdf' => 'text-danger',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'text-primary',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'text-success',
            'image/jpeg',
            'image/png',
            'image/gif' => 'text-warning',
            default => 'text-secondary'
        };
    }
    
    public function getLibelleCategorieAttribute(): string
    {
        return self::getCategories()[$this->categorie] ?? $this->categorie;
    }
    
    public function getEstImageAttribute(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }
    
    public function getEstPdfAttribute(): bool
    {
        return $this->mime_type === 'application/pdf';
    }
    
    // Méthodes
    public function peutEtreSupprimee(): bool
    {
        // Une pièce jointe ne peut être supprimée que si le dossier est encore en analyse
        return $this->courrier && $this->courrier->statut === Courrier::STATUT_ANALYSE;
    }
}