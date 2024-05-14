<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriberScientificDomain extends Model
{
    protected $table = 'subscribers_scientific_domains';
    public $timestamps = false;
    protected $fillable = ['SubscriberID', 'ScientificDomainID'];
}
