<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'current_role_id'
    ];

    protected $casts = [
        'date_reception' => 'date',
        'date_limite' => 'date',
    ];

    // Relations
    public function statut()
    {
        return $this->belongsTo(ParapheurStatut::class, 'statut_id');
    }

    public function typeCourrier()
    {
        return $this->belongsTo(TypeCourrier::class, 'type_courrier_id');
    }

    public function createur()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function currentRole()
    {
        return $this->belongsTo(Role::class, 'current_role_id');
    }

    public function fichiers()
    {
        return $this->hasMany(ParapheurFichier::class, 'parapheur_id');
    }

    public function historique()
    {
        return $this->hasMany(ParapheurHistorique::class, 'parapheur_id');
    }
}