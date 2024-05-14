<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectAcademicLevel extends Model
{
    protected $table = 'projects_academic_levels';
    public $timestamps = false;
    protected $fillable = ['ProjectID', 'AcademicLevelID'];
}
