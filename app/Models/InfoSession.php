<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InfoSession extends Model
{
    use HasFactory;

    protected $fillable = ['session_datetime', 'location', 'url', 'speaker', 'title', 'organisation_id', 'description'];

    public $timestamps = true;

    public function project(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'projects_info_sessions', 'info_session_id', 'project_id');
    }

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }
}
