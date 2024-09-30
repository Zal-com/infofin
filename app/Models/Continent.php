<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Continent extends Model
{
    protected $table = 'continents';
    public $timestamps = false;
    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['code', 'name'];

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'projects_continents', 'continent_code', 'project_id');
    }

    public function countries(): HasMany
    {
        return $this->hasMany(Country::class, 'continent_code', 'code');
    }
}
