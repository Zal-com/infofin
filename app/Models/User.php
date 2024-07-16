<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{

    use HasFactory, Notifiable, HasRoles;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'email',
        'password',
        'matricule',
        'first_name',
        'last_name',
        'is_email_subscriber'
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getRoleNamesAttribute()
    {
        return $this->roles->pluck('name')->implode(', ');
    }

    public function getRoleIdAttribute()
    {
        return $this->roles->first()->name ?? null;
    }

    public function setRoleIdAttribute($roleId)
    {
        $this->syncRoles([$roleId]);
    }

    public function faculties(): BelongsToMany
    {
        return $this->belongsToMany(Faculties::class, 'users_faculties', 'user_id', 'faculty_id');
    }

    public function scientific_domains(): BelongsToMany
    {
        return $this->belongsToMany(ScientificDomain::class, 'users_scientific_domains', 'user_id', 'scientific_domain_id');
    }

    public function info_types(): BelongsToMany
    {
        return $this->belongsToMany(InfoType::class, 'users_info_types', 'user_id', 'info_type_id');
    }


    public function searches(): HasMany
    {
        return $this->hasMany(Searches::class, "user_id");
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, "poster_id");
    }

    public function rate_mail(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, "visits_rate_mail", "user_id", "project_id")->withPivot('date_consult');
    }

    public function full_name()
    {
        return "{$this->first_name} {$this->last_name}";
    }

}
