<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Draft extends Model
{
    use HasFactory;

    protected $fillable = ['content', 'poster_id'];
    public $timestamps = true;


    public function poster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id');
    }
}
