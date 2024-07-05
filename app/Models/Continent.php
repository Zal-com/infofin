<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Continent extends Model
{
    protected $table = 'continent';
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $fillable = ['name'];

    public function projects() : HasMany
    {
        return $this->hasMany(Project::class, 'continent_id');
    }
}
