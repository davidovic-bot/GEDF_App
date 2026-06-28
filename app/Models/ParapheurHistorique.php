<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParapheurHistorique extends Model
{
    protected $fillable = [
        'parapheur_id',
        'user_id',
        'action',
        'ancien_statut_id',
        'nouveau_statut_id',
        'commentaire',
    ];

    public function parapheur()
    {
        return $this->belongsTo(Parapheur::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ancienStatut()
    {
        return $this->belongsTo(ParapheurStatut::class, 'ancien_statut_id');
    }

    public function nouveauStatut()
    {
        return $this->belongsTo(ParapheurStatut::class, 'nouveau_statut_id');
    }
}