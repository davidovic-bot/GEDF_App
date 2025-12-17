<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParapheurStatut extends Model
{
    use HasFactory;

    protected $table = 'parapheur_statuts';
    
    protected $fillable = [
        'code',
        'nom',
        'description',
        'couleur',
        'ordre',
        'actif'
    ];
}