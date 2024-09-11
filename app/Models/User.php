<?php

namespace App\Models;

use Filament\Models\Contracts\HasName;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasName
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
        'uid',
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
        'remember_token',
    ];

    public function addToFavorites($id)
    {
        try {
            $this->favorites()->syncWithoutDetaching($id);
            Notification::make()
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->iconColor('success')
                ->title('Projet ajouté à vos favoris.')
                ->send()
                ->seconds(5);
        } catch (\Exception $e) {
            Notification::make()
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->iconColor('danger')
                ->title('Impossible d\'ajouter le projet en favori. Veuillez réessayer plus tard.')
                ->send()
                ->seconds(5);
        }


    }

    public function removeFromFavorites($id)
    {
        try {
            $this->favorites()->detach($id);
            Notification::make()
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->iconColor('success')
                ->title('Projet retiré de vos favoris.')
                ->send()
                ->seconds(5);
        } catch (\Exception $e) {
            Notification::make()
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->iconColor('danger')
                ->title('Impossible de retirer le projet de vos favoris. Veuillez réessayer plus tard.')
                ->send()
                ->seconds(5);
        }
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
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

    public function last_update_projects() : HasMany
    {
        return $this->hasMany(Project::class, "last_update_user_id");
    }

    public function favorites(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'users_favorite_projects', "user_id", "project_id");
    }

    public function rate_mail(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, "visits_rate_mail", "user_id", "project_id")->withPivot('date_consult');
    }

    public function drafts(): BelongsToMany
    {
        return $this->belongsToMany(Draft::class, 'users_drafts');
    }

    public function full_name(): string
    {
        return strtoupper($this->last_name) . ' ' . $this->first_name;
    }

    public function getFilamentName(): string
    {
        return $this->full_name();
    }

    public function getFullNameAttribute(): string
    {
        return $this->full_name();
    }

    public function getRoleAttribute(): string
    {
        return $this->roles->pluck('name')->implode(', ');
    }

    public function getInfoTypeAttribute(): Collection
    {
        return $this->info_types->pluck('title');
    }

    public function getScientificDomainAttribute(): Collection
    {
        return $this->scientific_domains->pluck('name');
    }

    public function getNameAttribute(): string
    {
        return $this->full_name();
    }

    public function getAvatarInitials(): string
    {
        return substr($this->first_name, 0, 1) . '+' . substr($this->last_name, 0, 1);
    }

    public function reassignAndDelete($newUserId = 1)
    {

        $this->projects()->update(['poster_id' => $newUserId]);
        $this->last_update_projects()->update(['last_update_user_id' => $newUserId]);

        $this->searches()->update(['user_id' => $newUserId]);

        $this->drafts()->detach($this->id);
        $this->scientific_domains()->detach($this->id);
        $this->info_types()->detach($this->id);
        $this->favorites()->detach($this->id);
        $this->rate_mail()->detach($this->id);

        \DB::table('users_scientific_domains')->where('user_id', $this->id)->delete();
        \DB::table('users_favorite_projects')->where('user_id', $this->id)->delete();
        \DB::table('users_info_types')->where('user_id', $this->id)->delete();
        \DB::table('visits_rate_mail')->where('user_id', $this->id)->delete();
        \DB::table('users_drafts')->where('user_id', $this->id)->delete();

        $this->delete();
    }
}
