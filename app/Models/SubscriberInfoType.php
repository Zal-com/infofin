<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriberInfoType extends Model
{
    protected $table = 'subscribers_info_types';
    public $timestamps = false;
    protected $fillable = ['SubscriberID', 'InfoTypeID'];
}
