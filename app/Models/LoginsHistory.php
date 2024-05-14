<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginsHistory extends Model
{
    protected $table = 'logins_history';
    public $timestamps = false; // Spécifier car le timestamp est géré manuellement
    protected $primaryKey = 'LoginID';
    protected $fillable = ['Number', 'Xml', 'SubscriberID', 'Success'];
}
