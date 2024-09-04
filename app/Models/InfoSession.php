<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InfoSession extends Model
{
    use HasFactory;

    protected $fillable = ['session_datetime', 'location', 'url', 'speaker', 'title', 'organisation_id', 'description'];

    public $timestamps = true;

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }
}
