<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaculteUK extends Model
{
    protected $table = 'faculteUK';
    public $timestamps = false;
    protected $primaryKey = 'FacultyID';
    protected $fillable = ['Name', 'LangID'];
}
