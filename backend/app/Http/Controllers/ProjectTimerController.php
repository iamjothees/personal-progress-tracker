<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddExistingTimersRequest;
use App\Http\Responses\ApiResponse;
use App\Project\Models\Project;
use App\Services\ProjectTimerService;

class ProjectTimerController extends Controller
{
    public function __construct(private ProjectTimerService $projectTimerService) {}

    public function addExistingTimers(AddExistingTimersRequest $request, Project $project): ApiResponse
    {
        $this->projectTimerService->addExistingTimers(project: $project, timerIds: $request->validated()['timers']);

        return new ApiResponse(data: ['project' => $project->toResource()]);
    }
}
