<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisitsRate extends Model
{
    protected $table = 'visits_rate';
    public $timestamps = true;
    protected $fillable = ['project_id', 'date_visit'];

    public function project() : belongsTo {
        return $this->belongsTo(Project::class, 'project_id');
    }
}
