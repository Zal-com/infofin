<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $table = 'documents';
    public $timestamps = false; // Spécifier car le timestamp est géré manuellement
    protected $primaryKey = 'DocumentID';
    protected $fillable = ['ProjectID', 'Name', 'Description', 'Filename'];
}
