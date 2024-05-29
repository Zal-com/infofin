<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = 'projects';
    protected $primaryKey = 'ProjectID';
    protected $fillable = [
        'Name', 'Organisation', 'OrganisationReference', 'Deadline', 'Continuous', 'Justificatif', 'Deadline2',
        'Continuous2', 'Justificatif2', 'ShortDescription', 'LongDescription', 'ContactULBName', 'ContactULBAddress',
        'ContactULBEmail', 'ContactULBPhone', 'ContactULBWebPage', 'ContactExtName', 'ContactExtAddress', 'ContactExtEmail',
        'ContactExtPhone', 'ContactExtWebPage', 'Periodicity', 'AdmissionRequirements', 'Financement', 'PourPostuler',
        'Active', 'LangID', 'CreateTimeStamp', 'UserID', 'LastUpdateUserID', 'TimeStamp'
    ];

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
}
