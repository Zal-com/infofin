<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pays extends Model
{
    protected $table = 'pays';
    public $timestamps = false; // Spécifier car pas de colonnes timestamp
    protected $primaryKey = 'codePays';
    protected $fillable = ['nomPaysUK', 'nomPays', 'alpha2', 'alpha3', 'numIso', 'stvnatn_code', /* Add other fillable fields */];
}
