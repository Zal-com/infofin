<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScientificDomainCategory extends Model
{
    protected $table = 'scientific_domains_categories';
    public $timestamps = false;
    protected $fillable = ['title'];

    public function domains() : HasMany {
        return $this->hasMany(ScientificDomain::class, 'sci_dom_cat_id');
    }
}
