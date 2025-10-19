<?php

use App\Task\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guests cannot access any task endpoints', function () {
    $this->getJson('/api/tasks')->assertUnauthorized();
    $this->postJson('/api/tasks')->assertUnauthorized();
    $this->getJson('/api/tasks/1')->assertUnauthorized();
    $this->putJson('/api/tasks/1')->assertUnauthorized();
    $this->deleteJson('/api/tasks/1')->assertUnauthorized();
});

test('users can create a task', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->postJson('/api/tasks', ['title' => 'Test Task']);

    $response->assertOk();
    $response->assertJsonStructure(['task' => ['id', 'title']]);
    $this->assertDatabaseHas('tasks', [
        'title' => 'Test Task',
    ]);
});

test('a user can retrieve a list of their own tasks', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $task1 = Task::factory()->create(['title' => 'Task 1']);
    $task2 = Task::factory()->create(['title' => 'Task 2']);
    $user->participatingTasks()->attach($task1->id);
    $user->participatingTasks()->attach($task2->id);

    $response = $this->getJson('/api/tasks');

    $response->assertOk();
    $response->assertJsonCount(2, 'tasks');
    $response->assertJsonPath('tasks.0.title', 'Task 1');
    $response->assertJsonPath('tasks.1.title', 'Task 2');
});

test('a user can view a single task they own', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['title' => 'Owned Task']);
    $user->participatingTasks()->attach($task->id);
    $this->actingAs($user);

    $response = $this->getJson("/api/tasks/{$task->id}");

    $response->assertOk();
    $response->assertJsonPath('task.title', 'Owned Task');
});

test('users cannot view tasks belonging to other users', function () {
    $userA = User::factory()->create();
    $userB = User::factory()->create();
    $task = Task::factory()->create(['title' => 'Other User Task']);
    $userA->participatingTasks()->attach($task->id);
    $this->actingAs($userB);

    $this->getJson("/api/tasks/{$task->id}")->assertForbidden();
});

test('a user can update a task they own', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['title' => 'Old Task Title']);
    $user->participatingTasks()->attach($task->id);
    $this->actingAs($user);

    $response = $this->putJson("/api/tasks/{$task->id}", ['title' => 'New Task Title']);

    $response->assertOk();
    $response->assertJsonPath('task.title', 'New Task Title');
    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'title' => 'New Task Title',
    ]);
});

test('users cannot update tasks belonging to other users', function () {
    $userA = User::factory()->create();
    $userB = User::factory()->create();
    $task = Task::factory()->create(['title' => 'Other User Task']);
    $userA->participatingTasks()->attach($task->id);
    $this->actingAs($userB);

    $this->putJson("/api/tasks/{$task->id}", ['title' => 'Attempted Update'])->assertForbidden();
});

test('a user can delete a task they own', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['title' => 'Task to Delete']);
    $user->participatingTasks()->attach($task->id);
    $this->actingAs($user);

    $response = $this->deleteJson("/api/tasks/{$task->id}");

    $response->assertOk();
    $this->assertSoftDeleted('tasks', ['id' => $task->id]);
});

test('users cannot delete tasks belonging to other users', function () {
    $userA = User::factory()->create();
    $userB = User::factory()->create();
    $task = Task::factory()->create(['title' => 'Other User Task']);
    $userA->participatingTasks()->attach($task->id);
    $this->actingAs($userB);

    $this->deleteJson("/api/tasks/{$task->id}")->assertForbidden();
});
