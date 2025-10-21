<?php

namespace App\Task\Services;

use App\Models\User;
use App\Task\Models\Task;

class TaskService
{
    /**
     * Create a new class instance.
     */
    public function __construct(){}

    public function getPaginated(int $page = 1, int $limit = 10)
    {
        return Task::paginate(perPage: $limit, page: $page);
    }

    public function create(array $data, User $participant)
    {
        $task = Task::create($data);
        $task->participants()->attach($participant);
        return $task;
    }

    public function update(Task $task, array $data)
    {
        $task->update($data);
        return $task;
    }

    public function delete(Task $task)
    {
        $task->delete();
        return $task;
    }
}
