<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organisation extends Model
{
    protected $table = 'organisations';
    public $timestamps = false; // Spécifier car pas de colonnes timestamp
    protected $fillable = ['id', 'title'];

    public function projects() : HasMany
    {
        return $this->hasMany(Project::class, 'organisation_id');
    }
}
