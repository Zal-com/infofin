<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faculties extends Model
{
    protected $table = 'faculties';
    public $timestamps = false;
    protected $primaryKey = 'FacultyID';
    protected $fillable = ['Name', 'LangID'];
}
