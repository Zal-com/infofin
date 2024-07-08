<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectOrganisation extends Model
{
    use HasFactory;

    protected $table = 'projects_organisations';
    protected $fillable = ['project_id', 'organisation_id'];
    public $timestamps = false;

    public function project() : BelongsTo {
        return $this->belongsTo(Project::class);
    }

    public function organisation() : BelongsTo {
        return $this->belongsTo(Organisation::class);
    }
}
