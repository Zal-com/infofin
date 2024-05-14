<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriberStatistiqueSubUnsub extends Model
{
    protected $table = 'subscribers_statistiques_sub_unsub';
    public $timestamps = false; // Les timestamps sont spécifiquement gérés
    protected $fillable = ['SubscriberID', 'action'];
}
