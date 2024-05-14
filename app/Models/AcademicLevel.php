<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicLevel extends Model
{
    protected $table = 'academic_levels';
    public $timestamps = false;
    protected $primaryKey = 'AcademicLevelID';
    protected $fillable = ['Name', 'LangID', 'Order', 'Test'];
}
