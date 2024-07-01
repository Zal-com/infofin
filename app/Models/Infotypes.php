<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Znck\Eloquent\Relations\BelongsToThrough;

class InfoTypes extends Model
{
    protected $table = 'info_types';
    public $timestamps = false;
    protected $primaryKey = 'InfoTypeID';
    protected $fillable = ['Name', 'CategoryID', 'LangID', 'Order'];

    public function projects() : BelongsToThrough
    {
        return $this->belongsToThrough(Project::class, ProjectInfoType::class);
    }
}


