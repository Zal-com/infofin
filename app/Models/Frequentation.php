<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Frequentation extends Model
{
    protected $table = 'frequentation';
    public $timestamps = true; // Spécifier car le timestamp est géré manuellement
    protected $primaryKey = 'id';
    protected $fillable = ['ProjectID'];
}
