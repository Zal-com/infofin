<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectScientificDomain extends Model
{
    protected $table = 'projects_scientific_domains';
    public $timestamps = false;
    protected $fillable = ['ProjectID', 'ScientificDomainID'];
}
