<?php

namespace App\Policies;

use App\Models\User;
use App\Project\Models\Project;

class ProjectTaskPolicy
{
    public function addTasks(User $user, Project $project): bool
    {
        return $project->participants()->where('participant_id', $user->id)->exists(); 
    }
}
