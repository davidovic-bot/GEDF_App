// app/Models/Courrier.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Courrier extends Model
{
    protected $fillable = [
        'numero', 'type_courrier_id', 'objet', 'reference',
        'service_emetteur_id', 'date_reception', 'date_envoi',
        'montant', 'devise', 'beneficiaire', 'motif',
        'urgence', 'confidentialite', 'statut_general'
    ];

    // Relation avec le parapheur (un courrier peut avoir un parapheur)
    public function parapheur()
    {
        return $this->hasOne(Parapheur::class);
    }

    // Relation avec les pièces jointes
    public function piecesJointes()
    {
        return $this->hasMany(PieceJointe::class);
    }

    // Types de courrier : exonération, TVA, décision, etc.
    public function typeCourrier()
    {
        return $this->belongsTo(TypeCourrier::class);
    }

    // Service émetteur
    public function serviceEmetteur()
    {
        return $this->belongsTo(Service::class, 'service_emetteur_id');
    }
}