<?php

namespace App\Project\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Project\Http\Requests\AddExistingTasksRequest;
use App\Project\Http\Requests\ProjectRequest;
use App\Project\Models\Project;
use App\Project\Services\ProjectService;

class ProjectController extends Controller
{
    public function __construct(private ProjectService $projectService){}

    public function index(): ApiResponse{
        $projects = $this->projectService->getPaginated(page: request()->get('page', 1), limit: request()->get('limit', 15));
        return new ApiResponse(data: ['projects' => $projects->toResourceCollection()]);
    }

    public function store(ProjectRequest $request): ApiResponse{
        $project = $this->projectService->create(data: $request->validated(), participant: auth()->user());
        return new ApiResponse(data: ['project' => $project->toResource()]);
    }

    public function show(Project $project): ApiResponse{
        return new ApiResponse(data: ['project' => $project->toResource()]);
    }

    public function update(ProjectRequest $request, Project $project): ApiResponse{
        $project = $this->projectService->update(project: $project, data: $request->validated());
        return new ApiResponse(data: ['project' => $project->toResource()]);
    }

    public function destroy(Project $project): ApiResponse{
        $this->projectService->delete(project: $project);
        return new ApiResponse();
    }

    public function addExistingTasks(AddExistingTasksRequest $request, Project $project): ApiResponse{
        $this->projectService->addExistingTasks(project: $project, taskIds: $request->validated()['tasks']);
        return new ApiResponse(data: ['project' => $project->toResource()]);
    }
}
