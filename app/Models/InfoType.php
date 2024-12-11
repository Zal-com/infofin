<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Znck\Eloquent\Relations\BelongsToThrough;

class InfoType extends Model
{
    protected $table = 'info_types';
    public $timestamps = false;
    protected $fillable = ['title', 'info_types_cat_id'];

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'projects_info_types', 'info_type_id', 'project_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(InfoTypeCategory::class, 'info_types_cat_id');
    }
}


