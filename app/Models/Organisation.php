<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organisation extends Model
{
    protected $table = 'organisation';
    public $timestamps = false; // Spécifier car pas de colonnes timestamp
    protected $fillable = ['title'];
}
