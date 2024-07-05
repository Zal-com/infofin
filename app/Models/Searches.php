<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Searches extends Model
{
    protected $table = 'recherche';
    public $timestamps = true;

    protected $fillable = ['user_id', 'input'];

    public function user() : BelongsTo{
        return $this->belongsTo(User::class, 'user_id');
    }
}
