<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InfoTypes extends Model
{
    protected $table = 'info_types';
    public $timestamps = false;
    protected $primaryKey = 'InfoTypeID';
    protected $fillable = ['Name', 'CategoryID', 'LangID', 'Order'];
}
