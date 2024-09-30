<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        return $this->belongsToMany(Project::class, 'project_continent', 'continent_code', 'project_id');
    }
}
