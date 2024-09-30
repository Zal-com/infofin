<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Collection extends Model
{

    use HasUuids;

    // Spécifiez le nom de la table si nécessaire (optionnel)
    // protected $table = 'collections';

    // Indique que la clé primaire est 'uid' de type string
    protected $primaryKey = 'id';
    public $incrementing = false; // Désactive l'auto-incrémentation
    protected $keyType = 'string'; // Spécifie que la clé primaire est de type string

    protected $fillable = ['id', 'name', 'description', 'user_id'];

    /**
     * Relation avec les projets (Many-to-Many).
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'projects_collections', 'collection_id', 'project_id');
    }
}
