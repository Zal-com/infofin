<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Znck\Eloquent\Relations\BelongsToThrough;

class InfoType extends Model
{
    protected $table = 'info_types';
    public $timestamps = false;
    protected $primaryKey = 'InfoTypeID';
    protected $fillable = ['Name', 'CategoryID', 'LangID', 'Order'];

    public function projects() : BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_info_type', 'project_id', 'info_type_id');
    }
}


