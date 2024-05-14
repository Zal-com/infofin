<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScientificDomain extends Model
{
    protected $table = 'scientific_domains';
    public $timestamps = false;
    protected $primaryKey = 'ScientificDomainID';
    protected $fillable = ['Name', 'CategoryID', 'LangID', 'Order'];
}
