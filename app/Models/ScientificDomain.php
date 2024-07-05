<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ScientificDomain extends Model
{
    protected $table = 'scientific_domains';
    public $timestamps = false;
    protected $fillable = [ 'title', 'sci_dom_cat_id'];

    public function category() : BelongsTo{
        return $this->belongsTo(ScientificDomainCategory::class, 'sci_dom_cat_id');
    }

    public function users() : BelongsToMany
    {
        return $this->belongsToMany(User::class, 'users_scientific_domains', 'scientific_domain_id', 'user_id');
    }

    public function projects() : BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'projects_scientific_domains', 'scientific_domain_id', 'project_id');
    }

}
