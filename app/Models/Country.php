<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Country extends Model
{
    protected $table = 'countries';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'code', 'name', 'full_name', 'iso3', 'numero', 'continent_code', 'show_order'
    ];

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'projects_countries', 'country_id', 'project_id');
    }

    public function continent(): BelongsTo
    {
        return $this->belongsTo(Continent::class, 'continent_code', 'code');
    }
}
