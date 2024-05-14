<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScientificDomainCategory extends Model
{
    protected $table = 'scientific_domains_categories';
    public $timestamps = false;
    protected $primaryKey = 'CategoryID';
    protected $fillable = ['Name', 'LangID'];
}
