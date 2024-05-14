<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keywords extends Model
{
    protected $table = 'keywords';
    public $timestamps = false;
    protected $primaryKey = 'KeywordID';
    protected $fillable = ['Name', 'LangID'];
}
