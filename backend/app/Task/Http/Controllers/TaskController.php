<?php

namespace App\Task\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Task\Http\Requests\TaskRequest;
use App\Task\Models\Task;
use App\Task\Services\TaskService;
use App\Http\Controllers\Controller;

class TaskController extends Controller
{
    public function __construct(private TaskService $taskService){}

    public function index(): ApiResponse{
        $tasks = $this->taskService->getPaginated(page: request()->get('page', 1), limit: request()->get('limit', 15));
        return new ApiResponse(data: ['tasks' => $tasks->toResourceCollection()]);
    }

    public function store(TaskRequest $request): ApiResponse{
        $task = $this->taskService->create(data: $request->validated(), participant: auth()->user());
        return new ApiResponse(data: ['task' => $task->toResource()]);
    }

    public function show(Task $task): ApiResponse{
        return new ApiResponse(data: ['task' => $task->toResource()]);
    }

    public function update(TaskRequest $request, Task $task): ApiResponse{
        $task = $this->taskService->update(task: $task, data: $request->validated());
        return new ApiResponse(data: ['task' => $task->toResource()]);
    }

    public function destroy(Task $task): ApiResponse{
        $this->taskService->delete(task: $task);
        return new ApiResponse();
    }
}