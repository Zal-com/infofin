<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Urldescription2 extends Model
{
    protected $table = 'urldescription2';
    public $timestamps = false;
    protected $fillable = ['urls', 'location', 'numinfo', 'nomtable', 'date'];
}
