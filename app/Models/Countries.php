<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Countries extends Model
{
    protected $table = 'pays';
    public $timestamps = false; // Spécifier car pas de colonnes timestamp
    protected $primaryKey = 'codePays';
    protected $fillable = ['nomPaysUK', 'nomPays', 'alpha2', 'alpha3', 'numIso', 'stvnatn_code', /* Add other fillable fields */];

    public function projects() : HasMany
    {
        return $this->hasMany(Project::class, 'country_id', 'codePays');
    }
}
