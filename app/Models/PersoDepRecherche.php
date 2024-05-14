<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersoDepRecherche extends Model
{
    protected $table = 'perso_DepRecherche';
    public $timestamps = false;
    protected $fillable = ['nom', 'prenom', 'mail'];
}
