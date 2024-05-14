<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectKeyword extends Model
{
    protected $table = 'projects_keywords';
    public $timestamps = false;
    protected $fillable = ['ProjectID', 'KeywordID'];
}
