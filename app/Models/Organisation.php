<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organisation extends Model
{
    protected $table = 'organisations';
    public $timestamps = false; // Spécifier car pas de colonnes timestamp
    protected $fillable = ['title'];

    public function projects() : BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'projects_organisations', 'organisation_id', 'project_id');
    }
}
