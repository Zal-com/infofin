<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class InfoSession extends Model
{
    public $timestamps = true;
    protected $fillable = ['session_datetime', 'location', 'url', 'speaker', 'title', 'organisation_id', 'description', 'session_type', 'status'];

    public function project(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'projects_info_sessions', 'info_session_id', 'project_id');
    }

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    public function scientific_domains(): BelongsToMany
    {
        return $this->belongsToMany(ScientificDomain::class, 'info_sessions_scientific_domains', 'info_session_id', 'scientific_domain_id');
    }

    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(Collection::class, 'info_sessions_collections', 'info_session_id', 'collection_id');
    }

    public function getSessionTypeStringAttribute(): string
    {
        switch ($this->session_type) {
            case 0 :
                return 'Distanciel';
                break;
            case 1 :
                return 'Présentiel';
                break;
            case 2 :
                return 'Hybride';
                break;
            default :
                return 'Non communiqué';
                break;
        }
    }
}
