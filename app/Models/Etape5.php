<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Etape5 extends Model
{
    protected $table = 'Etape5';
    public $timestamps = false;
    protected $fillable = ['Email', 'ProjectID'];
}
