<?php

use App\Project\Models\Project;
use App\Models\User;
use App\Task\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guests cannot access any project endpoints', function () {
    $this->getJson('/api/projects')->assertUnauthorized();
    $this->postJson('/api/projects')->assertUnauthorized();
    $this->getJson('/api/projects/1')->assertUnauthorized();
    $this->putJson('/api/projects/1')->assertUnauthorized();
    $this->deleteJson('/api/projects/1')->assertUnauthorized();
});

test('users can create a project', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->postJson('/api/projects', ['name' => 'Test Project']);

    $response->assertOk();
    $response->assertJsonStructure(['project' => ['id', 'name']]);
    $this->assertDatabaseHas('projects', [
        'name' => 'Test Project',
    ]);
});

test('a user can retrieve a list of their participating projects', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $project1 = Project::factory()->create(['name' => 'Project 1']);
    $project2 = Project::factory()->create(['name' => 'Project 2']);
    $user->participatingProjects()->attach($project1->id);
    $user->participatingProjects()->attach($project2->id);

    $response = $this->getJson('/api/projects');

    $response->assertOk();
    $response->assertJsonCount(2, 'projects');
    $response->assertJsonPath('projects.0.name', 'Project 1');
    $response->assertJsonPath('projects.1.name', 'Project 2');
});

test('a user can view a single project they participating', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['name' => 'Owned Project']);
    $user->participatingProjects()->attach($project->id);
    $this->actingAs($user);

    $response = $this->getJson("/api/projects/{$project->id}");

    $response->assertOk();
    $response->assertJsonPath('project.name', 'Owned Project');
});

test('users cannot view non participating projects', function () {
    $userA = User::factory()->create();
    $userB = User::factory()->create();
    $project = Project::factory()->create(['name' => 'Other User Project']);
    $project->participants()->attach($userA->id);
    $this->actingAs($userB);

    $this->getJson("/api/projects/{$project->id}")->assertForbidden();
});

test('a user can update a project they participating', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['name' => 'Old Project Name']);
    $project->participants()->attach($user->id);
    $this->actingAs($user);

    $response = $this->putJson("/api/projects/{$project->id}", ['name' => 'New Project Name']);

    $response->assertOk();
    $response->assertJsonPath('project.name', 'New Project Name');
    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
        'name' => 'New Project Name',
    ]);
});

test('users cannot update non participating projects', function () {
    $userA = User::factory()->create();
    $userB = User::factory()->create();
    $project = Project::factory()->create(['name' => 'Other User Project']);
    $userA->participatingProjects()->attach($project->id);
    $this->actingAs($userB);

    $this->putJson("/api/projects/{$project->id}", ['name' => 'Attempted Update'])->assertForbidden();
});

test('a user can delete a project they participating', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['name' => 'Project to Delete']);
    $user->participatingProjects()->attach($project->id);
    $this->actingAs($user);

    $response = $this->deleteJson("/api/projects/{$project->id}");

    $response->assertOk();
    $this->assertSoftDeleted('projects', ['id' => $project->id]);
});

test('users cannot delete non participating projects', function () {
    $userA = User::factory()->create();
    $userB = User::factory()->create();
    $project = Project::factory()->create(['name' => 'Other User Project']);
    $userA->participatingProjects()->attach($project->id);
    $this->actingAs($userB);

    $this->deleteJson("/api/projects/{$project->id}")->assertForbidden();
});

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
