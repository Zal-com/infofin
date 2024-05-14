<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemandeAvis extends Model
{
    protected $table = 'demandeAvis';
    public $timestamps = false; // Spécifier car le timestamp est géré manuellement
    protected $primaryKey = 'id';
    protected $fillable = [
        'ProjectID', 'UserID', 'Concerne', 'complet', 'IntroductionDossier',
        'Helpfull', 'Q5', 'Q6', 'Q7', 'Q8', 'Q9', 'Q10', 'Commentaire'
    ];
}
