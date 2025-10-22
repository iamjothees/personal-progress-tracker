<?php

namespace App\Project\Services;

use App\Models\User;
use App\Project\Models\Project;

class ProjectService
{
    /**
     * Create a new class instance.
     */
    public function __construct(){}

    public function getPaginated(int $page = 1, int $limit = 10){
        return Project::paginate(perPage: $limit, page: $page);
    }

    public function create(array $data, User $participant){
        $project = Project::create($data);
        $project->participants()->attach($participant);
        return $project;
    }

    public function update(Project $project, array $data){
        $project->update($data);
        return $project;
    }

    public function delete(Project $project){
        $project->delete();
        return $project;
    }
    
    public function addExistingTasks(Project $project, array $taskIds){
        $project->tasks()->syncWithoutDetaching($taskIds);
        return $project;
    }
}

