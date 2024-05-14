<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FicheDel extends Model
{
    protected $table = 'ficheDel';
    public $timestamps = true; // Spécifier car le timestamp est géré manuellement
    protected $primaryKey = 'id';
    protected $fillable = ['numFiche', 'personne'];
}
