<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FrequentationMail extends Model
{
    protected $table = 'frequentation_mail';
    public $timestamps = false;
    protected $fillable = ['idProject', 'idSubscriber', 'dateConsultation'];
}
