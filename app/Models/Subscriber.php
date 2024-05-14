<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    protected $table = 'subscribers';
    protected $primaryKey = 'SubscriberID';
    protected $fillable = [
        'Number', 'StudentNumber', 'FirstName', 'LastName', 'Email', 'UserID', 'LangID', 'receveMail'
    ];
}
