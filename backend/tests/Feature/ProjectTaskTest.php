<?php

use App\Models\User;
use App\Project\Models\Project;
use App\Task\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test("a user can add existing tasks to a project they participating", function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['name' => 'Project with Existing Tasks']);
    $user->participatingProjects()->attach($project->id);
    $this->actingAs($user);

    $task1 = Task::factory()->create(['title' => 'Task 1']);
    $task2 = Task::factory()->create(['title' => 'Task 2']);
    
    $response = $this->postJson("/api/projects/{$project->id}/tasks", ['tasks' => [$task1->id, $task2->id]]);

    $response->assertOk();
    $response->assertJsonPath('project.tasks.0.title', 'Task 1');
    $response->assertJsonPath('project.tasks.1.title', 'Task 2');
});
