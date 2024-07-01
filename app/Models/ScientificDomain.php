<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use PhpParser\Node\Expr\Array_;

class ScientificDomain extends Model
{
    protected $table = 'scientific_domains';
    public $timestamps = false;
    protected $fillable = [ 'title', 'sci_dom_cat_id'];

    public function category() : BelongsTo{
        return $this->belongsTo(ScientificDomainCategory::class, 'sci_dom_cat_id', 'id');
    }

}
