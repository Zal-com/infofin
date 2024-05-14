<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recherche extends Model
{
    protected $table = 'recherche';
    public $timestamps = false; // Le champ 'Time' gère déjà le timestamp
    protected $fillable = ['UserID', 'Recherche'];
}
