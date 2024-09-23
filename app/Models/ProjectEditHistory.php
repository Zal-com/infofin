<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectEditHistory extends Model
{
    // Spécifiez le nom de la table si elle ne suit pas la convention de Laravel
    protected $table = 'projects_edit_history';

    // Définissez les attributs assignables en masse
    protected $fillable = ['date', 'id_project', 'id_user'];

    // Désactivez les timestamps si vous n'utilisez pas les colonnes created_at et updated_at
    public $timestamps = false;

    protected $casts = [
        'date' => 'datetime',
    ];

    /**
     * Relation avec le modèle Project
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'id_project');
    }

    /**
     * Relation avec le modèle User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
