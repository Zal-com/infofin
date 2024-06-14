<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create projects.
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create projects');
    }

    /**
     * Determine whether the user can edit their own project.
     */
    public function edit(User $user, Project $project)
    {
        if($user->can('edit own project')){
            return $user->id === $project->user_id;
        }
    }

    /**
     * Determine whether the user can delete their own project.
     */
    public function delete(User $user, Project $project)
    {
        if($user->can('delete own project')){
            return $user->id === $project->user_id;
        }
    }
}
