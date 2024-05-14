<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectInfoType extends Model
{
    protected $table = 'projects_info_types';
    public $timestamps = false;
    protected $fillable = ['ProjectID', 'InfoTypeID'];
}
