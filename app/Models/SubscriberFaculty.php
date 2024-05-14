<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriberFaculty extends Model
{
    protected $table = 'subscribers_faculties';
    public $timestamps = false;
    protected $fillable = ['SubscriberID', 'FacultyID'];
}
