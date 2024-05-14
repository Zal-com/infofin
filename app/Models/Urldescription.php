<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Urldescription extends Model
{
    protected $table = 'urldescription';
    public $timestamps = false;
    protected $fillable = ['urls', 'location', 'numinfo', 'nomtable', 'date'];
}
