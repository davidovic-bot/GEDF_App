// app/Models/ParapheWorkflow.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParapheWorkflow extends Model
{
    protected $fillable = [
        'parapheur_id', 'etape_actuelle', 'etapes_config',
        'date_debut', 'date_fin_prevue', 'date_fin_reelle'
    ];

    protected $casts = [
        'etapes_config' => 'array', // JSON des étapes
        'date_debut' => 'datetime',
        'date_fin_prevue' => 'datetime',
        'date_fin_reelle' => 'datetime',
    ];

    // Exemple de structure etapes_config:
    // [
    //     ['ordre' => 1, 'role' => 'agent', 'action' => 'analyse', 'delai' => 2],
    //     ['ordre' => 2, 'role' => 'chef_service', 'action' => 'validation', 'delai' => 1],
    //     ['ordre' => 3, 'role' => 'directeur', 'action' => 'signature', 'delai' => 1],
    // ]

    public function parapheur()
    {
        return $this->belongsTo(Parapheur::class);
    }

    public function getEtapeActuelleAttribute()
    {
        $etapes = $this->etapes_config ?? [];
        foreach ($etapes as $etape) {
            if ($etape['role'] === $this->parapheur->statut_actuel) {
                return $etape;
            }
        }
        return null;
    }

    public function getProchaineEtapeAttribute()
    {
        $etapes = $this->etapes_config ?? [];
        $currentIndex = array_search(
            $this->etape_actuelle,
            array_column($etapes, 'role')
        );
        
        return $currentIndex !== false && isset($etapes[$currentIndex + 1]) 
            ? $etapes[$currentIndex + 1] 
            : null;
    }
}