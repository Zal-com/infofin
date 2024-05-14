<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SciAcadMail extends Model
{
    protected $table = 'SciAcadMail';
    public $timestamps = false;
    protected $fillable = ['Matricule', 'Email', 'corps_electoral'];
}
