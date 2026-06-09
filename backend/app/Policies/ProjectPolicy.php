<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function view(User $user, Project $project): bool
    {
        return $user->role === 'admin' || $project->user_id === $user->id || $user->role === 'institution';
    }

    public function update(User $user, Project $project): bool
    {
        return $user->role === 'admin' || $project->user_id === $user->id;
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->role === 'admin' || $project->user_id === $user->id;
    }
}
