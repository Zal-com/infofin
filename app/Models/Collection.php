<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Collection extends Model
{
    // Spécifiez le nom de la table si nécessaire (optionnel)
    // protected $table = 'collections';

    // Indique que la clé primaire est 'uid' de type string
    protected $primaryKey = 'uid';
    public $incrementing = false; // Désactive l'auto-incrémentation
    protected $keyType = 'string'; // Spécifie que la clé primaire est de type string

    protected $fillable = ['uid', 'name', 'description'];

    /**
     * Relation avec les projets (Many-to-Many).
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'projects_collections', 'collection_uid', 'project_id');
    }
}
