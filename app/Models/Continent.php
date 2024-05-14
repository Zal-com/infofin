<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Continent extends Model
{
    protected $table = 'continent';
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $fillable = ['continent'];
}
