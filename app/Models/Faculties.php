<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Znck\Eloquent\Relations\BelongsToThrough;

class Faculties extends Model
{
    protected $table = 'faculties';
    public $timestamps = false;
    protected $fillable = ['title'];


    public function users() : BelongsToMany
    {
        return $this->belongsToMany(User::class, 'users_faculties', 'faculty_id', 'user_id');
    }
}
