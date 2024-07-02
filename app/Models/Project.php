<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    protected $table = 'projects';
    protected $fillable = [
        'title', 'organisation_id', 'deadline', 'deadline_2', 'continuous',
        'continuous_2', 'proof', 'proof_2', 'contact_ulb', 'contact_ext',
        'periodicity', 'admission_requirements', 'funding', 'apply_instructions',
        'poster_id', 'is_view_for_mail', 'date_lessor', 'info_lessor',
        'visit_count', 'last_update_user_id', 'country_id', 'continent_id',
        'status', 'is_big', 'full_description', 'short_description', 'is_draft',
        'created_at', 'updated_at'
    ];

    public $timestamps = true;


    public function scientificDomains() : BelongsToMany
    {
        return $this->belongsToMany(ScientificDomain::class, 'projects_scientific_domains', 'project_id', 'scientific_domain_id');
    }

}
