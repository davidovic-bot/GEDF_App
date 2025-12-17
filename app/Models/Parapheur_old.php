// app/Models/Parapheur.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parapheur extends Model
{
    protected $fillable = [
        'courrier_id', 'numero_parapheur', 'statut_actuel',
        'priorite', 'date_entree_parapheur', 'date_limite_traitement',
        'derniere_action', 'dernier_acteur_id', 'motif_rejet'
    ];

    // Statuts possibles (constants)
    const STATUT_EN_ATTENTE_ANALYSE = 'en_attente_analyse';
    const STATUT_EN_ATTENTE_CHEF = 'en_attente_chef_service';
    const STATUT_EN_ATTENTE_DIRECTEUR = 'en_attente_directeur';
    const STATUT_VALIDE = 'valide';
    const STATUT_SIGNE = 'signe';
    const STATUT_REJETE = 'rejete';
    const STATUT_ARCHIVE = 'archive';

    // Priorités
    const PRIORITE_NORMAL = 'normal';
    const PRIORITE_URGENT = 'urgent';

    // Relations
    public function courrier()
    {
        return $this->belongsTo(Courrier::class);
    }

    public function workflow()
    {
        return $this->hasOne(ParapheWorkflow::class);
    }

    public function actions()
    {
        return $this->hasMany(ParapheAction::class)->orderBy('created_at', 'desc');
    }

    public function dernierActeur()
    {
        return $this->belongsTo(User::class, 'dernier_acteur_id');
    }

    public function observations()
    {
        return $this->hasMany(Observation::class)->orderBy('created_at', 'desc');
    }

    // Méthodes métier
    public function peutEtreValidePar(User $user)
    {
        if ($user->hasRole('chef_service')) {
            return $this->statut_actuel === self::STATUT_EN_ATTENTE_CHEF;
        }
        return false;
    }

    public function peutEtreSignePar(User $user)
    {
        if ($user->hasRole('directeur')) {
            return $this->statut_actuel === self::STATUT_EN_ATTENTE_DIRECTEUR;
        }
        return false;
    }

    public function estEnRetard()
    {
        return $this->date_limite_traitement 
            && now()->greaterThan($this->date_limite_traitement)
            && in_array($this->statut_actuel, [
                self::STATUT_EN_ATTENTE_ANALYSE,
                self::STATUT_EN_ATTENTE_CHEF,
                self::STATUT_EN_ATTENTE_DIRECTEUR
            ]);
    }

    // Scopes
    public function scopePourRole($query, $role)
    {
        switch ($role) {
            case 'agent':
                return $query->where('statut_actuel', self::STATUT_EN_ATTENTE_ANALYSE);
            case 'chef_service':
                return $query->where('statut_actuel', self::STATUT_EN_ATTENTE_CHEF);
            case 'directeur':
                return $query->where('statut_actuel', self::STATUT_EN_ATTENTE_DIRECTEUR);
            case 'secretaire':
                return $query->whereIn('statut_actuel', [
                    self::STATUT_EN_ATTENTE_ANALYSE,
                    self::STATUT_EN_ATTENTE_CHEF
                ]);
            default: // superadmin, admin
                return $query;
        }
    }
}