<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = ['title'];
    public $timestamps = false;

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'projects_activities', 'activity_id', 'project_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'users_activities', 'activity_id', 'user_id');
    }
}
