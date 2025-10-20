<?php

namespace App\Services;

use App\Project\Models\Project;

class ProjectTaskService
{
    public function __construct(){}
    public function addExistingTasks(Project $project, array $taskIds){
        $project->tasks()->syncWithoutDetaching($taskIds);
        return $project;
    }
}
