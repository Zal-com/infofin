<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriberAcademicLevel extends Model
{
    protected $table = 'subscribers_academic_levels';
    public $timestamps = false;
    protected $fillable = ['SubscriberID', 'AcademicLevelID'];
}
