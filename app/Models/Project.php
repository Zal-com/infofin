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
    protected $fillable = ['id',
        'title', 'contact_ulb', 'contact_ext', 'admission_requirements', 'funding', 'apply_instructions',
        'poster_id', 'is_view_for_mail', /*'date_lessor',*/
        'info_lessor',
        'visit_count', 'last_update_user_id', 'country_id', 'continent_id',
        'status', 'is_big', 'long_description', 'short_description', 'is_draft',
        'created_at', 'updated_at', 'origin_url', 'deadlines', 'is_in_next_email', 'organisation_id'
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

    public function poster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'poster_id');
    }

    public function continents(): BelongsToMany
    {
        return $this->belongsToMany(Continent::class, 'project_continent', 'project_id', 'continent_code');
    }

    public function countries(): BelongsToMany
    {
        return $this->belongsToMany(Country::class, 'project_country', 'project_id', 'country_id');
    }


    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    public function info_sessions(): BelongsToMany
    {
        return $this->belongsToMany(InfoSession::class, 'projects_info_sessions', 'project_id', 'info_session_id');
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

    public function editHistories(): HasMany
    {
        return $this->hasMany(ProjectEditHistory::class, 'project_id', 'id');
    }

    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(Collection::class, 'projects_collections', 'project_id', 'collection_uid');
    }

    public function getFirstDeadlineAttribute(): string
    {
        $deadlines = $this->attributes['deadlines'] ? json_decode($this->attributes['deadlines'], true) : [];

        if (empty($deadlines)) {
            return 'No deadline';
        }

        usort($deadlines, function ($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });

        $futureDeadlines = array_filter($deadlines, function ($deadline) {
            return Carbon::parse($deadline['date'])->isAfter(today());
        });

        if (!empty($futureDeadlines)) {
            $firstFutureDeadline = reset($futureDeadlines);

            if ($firstFutureDeadline['continuous'] == 1) {
                return 'Continu';
            } else {
                return Carbon::parse($firstFutureDeadline['date'])->format('d/m/Y') . '|' . $firstFutureDeadline['proof'];
            }
        } else {
            $lastDeadline = end($deadlines);

            if ($lastDeadline['continuous'] == 1) {
                return 'Continu';
            } else {
                return Carbon::parse($lastDeadline['date'])->format('d/m/Y');
            }
        }
    }

    public function hasUpcomingDeadline(): bool
    {
        $deadlines = $this->deadlines;
        foreach ($deadlines as $deadline) {
            if ($deadline['continuous'] || \Carbon\Carbon::parse($deadline['date'])->isAfter(now())) {
                return true;
            } else continue;
        }
        return false;
    }

    public function getUpcomingDeadlinesAttribute()
    {
        return collect($this->deadlines)
            ->filter(function ($deadline) {
                return isset($deadline['date']) && $deadline['date'] >= now();
            })
            ->sortBy('date');
    }

    public function getAllDeadlinesSortedAttribute()
    {
        return collect($this->deadlines)->sortBy('date');
    }

    public function getLongDescriptionAttribute($value)
    {
        // Check if the value is already an array or JSON string
        if (is_string($value)) {
            // Try decoding the string as JSON
            $decoded = json_decode($value, true);
            // If decoding succeeds and results in an array, return the array
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }

        // If it's not a string or if decoding fails, return it as is (string)
        return $value;
    }

    public function setLongDescriptionAttribute($value)
    {
        // Check if the value is an array
        if (is_array($value)) {
            // Convert the array to a JSON string
            $this->attributes['long_description'] = json_encode($value);
        } else {
            // Otherwise, just store the value as is
            $this->attributes['long_description'] = $value;
        }
    }

    public function getApplyInstructionsAttribute($value)
    {
        // Check if the value is already an array or JSON string
        if (is_string($value)) {
            // Try decoding the string as JSON
            $decoded = json_decode($value, true);
            // If decoding succeeds and results in an array, return the array
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }

        // If it's not a string or if decoding fails, return it as is (string)
        return $value;
    }

    public function setApplyInstructionsAttribute($value)
    {
        // Check if the value is an array
        if (is_array($value)) {
            // Convert the array to a JSON string
            $this->attributes['apply_instructions'] = json_encode($value);
        } else {
            // Otherwise, just store the value as is
            $this->attributes['apply_instructions'] = $value;
        }
    }

    public function getFundingAttribute($value)
    {
        // Check if the value is already an array or JSON string
        if (is_string($value)) {
            // Try decoding the string as JSON
            $decoded = json_decode($value, true);
            // If decoding succeeds and results in an array, return the array
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }

        // If it's not a string or if decoding fails, return it as is (string)
        return $value;
    }

    public function setFundingAttribute($value)
    {
        // Check if the value is an array
        if (is_array($value)) {
            // Convert the array to a JSON string
            $this->attributes['funding'] = json_encode($value);
        } else {
            // Otherwise, just store the value as is
            $this->attributes['funding'] = $value;
        }
    }

    public function getAdmissionRequirementsAttribute($value)
    {
        // Check if the value is already an array or JSON string
        if (is_string($value)) {
            // Try decoding the string as JSON
            $decoded = json_decode($value, true);
            // If decoding succeeds and results in an array, return the array
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }

        // If it's not a string or if decoding fails, return it as is (string)
        return $value;
    }

    public function setAdmissionRequirementsAttribute($value)
    {
        // Check if the value is an array
        if (is_array($value)) {
            // Convert the array to a JSON string
            $this->attributes['admission_requirements'] = json_encode($value);
        } else {
            // Otherwise, just store the value as is
            $this->attributes['admission_requirements'] = $value;
        }
    }


}
