<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Project extends Model
{

    protected $table = 'projects';
    protected $fillable = [
        'title', 'deadline', 'deadline_2', 'continuous',
        'continuous_2', 'proof', 'proof_2', 'contact_ulb', 'contact_ext',
        'periodicity', 'admission_requirements', 'funding', 'apply_instructions',
        'poster_id', 'is_view_for_mail', 'date_lessor', 'info_lessor',
        'visit_count', 'last_update_user_id', 'country_id', 'continent_id',
        'status', 'is_big', 'long_description', 'short_description', 'is_draft',
        'created_at', 'updated_at', 'origin_url', 'deadlines'
    ];

    public $timestamps = true;

    protected $casts = [
        'contact_ulb' => 'array',
        'contact_ext' => 'array',
        'deadlines' => 'array',
    ];


    public function scientific_domains(): BelongsToMany
    {
        return $this->belongsToMany(ScientificDomain::class, 'projects_scientific_domains', 'project_id', 'scientific_domain_id');
    }

    public function info_types(): BelongsToMany
    {
        return $this->belongsToMany(InfoType::class, 'projects_info_types', 'project_id', 'info_type_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Countries::class, 'country_id', "codePays");
    }

    public function poster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'poster_id');
    }

    public function continent(): BelongsTo
    {
        return $this->belongsTo(Continent::class, 'continent_id');
    }

    public function organisations(): BelongsToMany
    {
        return $this->belongsToMany(Organisation::class, 'projects_organisations', 'project_id', 'organisation_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'project_id');
    }

    public function visit_rates(): HasMany
    {
        return $this->hasMany(VisitsRate::class, 'project_id');
    }

    public function rate_mail(): BelongsToMany
    {
        return $this->belongsToMany(User::class, "visits_rate_mail", "project_id", "user_id")->withPivot('date_consult');
    }

    public function getFirstDeadlineAttribute()
    {
        $deadlines = $this->attributes['deadlines'] ? json_decode($this->attributes['deadlines'], true) : [];

        if (isset($deadlines[0])) {
            $firstDeadline = $deadlines[0];

            if ($firstDeadline['continuous'] == 1) {
                return 'Continu';
            } else {
                // Format the date, e.g., convert it to a more readable format
                return Carbon::parse($firstDeadline['date'])->format('d-m-Y');
            }
        }

        return 'No deadline';
    }

}
