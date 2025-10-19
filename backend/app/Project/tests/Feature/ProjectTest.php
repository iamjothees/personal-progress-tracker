<?php

use App\Project\Models\Project;
use App\Models\User;
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

test('a user can retrieve a list of their own projects', function () {
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

test('a user can view a single project they own', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['name' => 'Owned Project']);
    $user->participatingProjects()->attach($project->id);
    $this->actingAs($user);

    $response = $this->getJson("/api/projects/{$project->id}");

    $response->assertOk();
    $response->assertJsonPath('project.name', 'Owned Project');
});

test('users cannot view projects belonging to other users', function () {
    $userA = User::factory()->create();
    $userB = User::factory()->create();
    $project = Project::factory()->create(['name' => 'Other User Project']);
    $project->participants()->attach($userA->id);
    $this->actingAs($userB);

    $this->getJson("/api/projects/{$project->id}")->assertForbidden();
});

test('a user can update a project they own', function () {
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

test('users cannot update projects belonging to other users', function () {
    $userA = User::factory()->create();
    $userB = User::factory()->create();
    $project = Project::factory()->create(['name' => 'Other User Project']);
    $userA->participatingProjects()->attach($project->id);
    $this->actingAs($userB);

    $this->putJson("/api/projects/{$project->id}", ['name' => 'Attempted Update'])->assertForbidden();
});

test('a user can delete a project they own', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['name' => 'Project to Delete']);
    $user->participatingProjects()->attach($project->id);
    $this->actingAs($user);

    $response = $this->deleteJson("/api/projects/{$project->id}");

    $response->assertOk();
    $this->assertSoftDeleted('projects', ['id' => $project->id]);
});

test('users cannot delete projects belonging to other users', function () {
    $userA = User::factory()->create();
    $userB = User::factory()->create();
    $project = Project::factory()->create(['name' => 'Other User Project']);
    $userA->participatingProjects()->attach($project->id);
    $this->actingAs($userB);

    $this->deleteJson("/api/projects/{$project->id}")->assertForbidden();
});
