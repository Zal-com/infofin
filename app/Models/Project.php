<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Project extends Model
{
    protected $table = 'projects';
    protected $fillable = [
        'title', 'organisation_id', 'deadline', 'continuous', 'proof', 'deadline_2',
        'continuous_2', 'proof_2', 'short_description', 'long_description', 'periodicity', 'admission_requirements', 
        'apply_instructions', 'is_active', 'user_id', 'last_update_user_id', 'contact_ulb', 'contact_ext', 'funding'
    ];

    public $timestamps = true;

    public static function getSortedAndPaginatedProjects($orderByColumn = 'TimeStamp', $orderDirection = 'desc', $itemsPerPage = 20, $validColumns = ["Name", "Deadline", "Deadline2", "Organisation", "ShortDescription", "TimeStamp"])
    {
        if (!in_array($orderByColumn, $validColumns)) {
            $orderByColumn = 'TimeStamp';
        }

        if (!in_array($orderDirection, ['asc', 'desc'])) {
            $orderDirection = 'desc';
        }

        return self::orderBy($orderByColumn, $orderDirection)->paginate($itemsPerPage);
    }

    public function infoType() : HasManyThrough
    {
        return $this->hasManyThrough(InfoTypes::class, ProjectInfoType::class, 'project_id', 'id', 'id', 'info_type_id');
    }
}
