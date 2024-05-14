<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriberKeyword extends Model
{
    protected $table = 'subscribers_keywords';
    public $timestamps = false;
    protected $fillable = ['SubscriberID', 'KeywordID'];
}
