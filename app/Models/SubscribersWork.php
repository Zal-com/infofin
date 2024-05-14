<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscribersWork extends Model
{
    protected $table = 'subscribers_work';
    public $timestamps = false; // No automatic timestamps since 'TimeStamp' is manually handled
    protected $primaryKey = 'SubscriberID';
    protected $fillable = ['Number', 'StudentNumber', 'FirstName', 'LastName', 'Email', 'UserID', 'LangID', 'receveMail'];
}
