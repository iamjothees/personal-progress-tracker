<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddExistingTasksRequest;
use App\Http\Responses\ApiResponse;
use App\Project\Models\Project;
use App\Services\ProjectTaskService;

class ProjectTaskController extends Controller
{
    
    public function __construct(private ProjectTaskService $projectTaskService){}

    public function addExistingTasks(AddExistingTasksRequest $request, Project $project): ApiResponse{
        $this->projectTaskService->addExistingTasks(project: $project, taskIds: $request->validated()['tasks']);
        return new ApiResponse(data: ['project' => $project->toResource()]);
    }
}
