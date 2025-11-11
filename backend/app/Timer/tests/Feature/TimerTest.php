<?php

use App\Timer\Models\Timer;
use App\Models\User;
use App\Project\Models\Project;
use App\Task\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guests cannot access any timer endpoints', function () {
    $this->getJson('/api/timers')->assertUnauthorized();
    $this->postJson('/api/timers/actions/start')->assertUnauthorized();
    $this->getJson('/api/timers/1')->assertUnauthorized();
    $this->postJson('/api/timers/1/actions/pause')->assertUnauthorized();
    $this->postJson('/api/timers/1/actions/1/resume')->assertUnauthorized();
    $this->postJson('/api/timers/1/actions/stop')->assertUnauthorized();
    $this->postJson('/api/timers/1/actions/reset')->assertUnauthorized();
});

test('users cannot access timers belonging to other users', function () {
    $userA = User::factory()->create();
    $userB = User::factory()->create();

    $timer = Timer::factory()->for($userA, 'owner')->create();
    $activity = $timer->activities()->create(['paused_at' => now()]);

    $this->actingAs($userB);

    $this->getJson("/api/timers/{$timer->id}")->assertForbidden();
    $this->postJson("/api/timers/{$timer->id}/actions/pause")->assertForbidden();
    $this->postJson("/api/timers/{$timer->id}/actions/{$activity->id}/resume")->assertForbidden();
    $this->postJson("/api/timers/{$timer->id}/actions/stop")->assertForbidden();
    $this->postJson("/api/timers/{$timer->id}/actions/reset")->assertForbidden();
});

test('a user can retrieve a list of their own running timers', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $runningTimer1 = Timer::factory()->for($user, 'owner')->create();
    $runningTimer2 = Timer::factory()->for($user, 'owner')->create();
    $stoppedTimer = Timer::factory()->for($user, 'owner')->create(['stopped_at' => now()]);

    $response = $this->getJson('/api/timers');

    $response->assertOk();
    $response->assertJsonCount(2, 'timers');
    $response->assertJsonPath('timers.0.id', $runningTimer1->id);
    $response->assertJsonPath('timers.1.id', $runningTimer2->id);
    $response->assertJsonMissing(['id' => $stoppedTimer->id]);
});

test('a user can view a single timer they own', function () {
    $user = User::factory()->create();
    $timer = Timer::factory()->for($user, 'owner')->create();
    $this->actingAs($user);

    $response = $this->getJson("/api/timers/{$timer->id}");

    $response->assertOk();
    $response->assertJsonPath('timer.id', $timer->id);
});

test('a user can start a new timer', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->postJson('/api/timers/actions/start');

    $response->assertOk();
    $response->assertJsonStructure(['timer' => ['id', 'started_at']]);
    $timerId = $response->json('timer.id');
    $this->assertDatabaseHas('timers', [
        'id' => $timerId,
        'owner_id' => $user->id,
        'stopped_at' => null,
    ]);
});

test('a user can pause a running timer', function () {
    $user = User::factory()->create();
    $timer = Timer::factory()->for($user, 'owner')->create();
    $this->actingAs($user);

    $response = $this->postJson("/api/timers/{$timer->id}/actions/pause", [
        'latest_activity_id' => null,
    ]);

    $response->assertOk();
    $response->assertJsonPath('timer.id', $timer->id);
    $response->assertJsonPath('timer.latestActivity.paused_at', fn (string $pausedAt) => $pausedAt !== null);
    $this->assertDatabaseHas('timer_activities', [
        'timer_id' => $timer->id,
    ]);
});

test('a user cannot pause an already paused timer', function () {
    $user = User::factory()->create();
    $timer = Timer::factory()->for($user, 'owner')->create();
    $this->actingAs($user);

    // First pause
    $this->postJson("/api/timers/{$timer->id}/actions/pause", [
        'latest_activity_id' => null,
    ]);

    // Second pause attempt
    $response = $this->postJson("/api/timers/{$timer->id}/actions/pause", [
        'latest_activity_id' => $timer->fresh()->latestActivity->id,
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors('timer');
});

test('a user cannot pause a stopped timer', function () {
    $user = User::factory()->create();
    $timer = Timer::factory()->for($user, 'owner')->create(['stopped_at' => now()]);
    $this->actingAs($user);

    $response = $this->postJson("/api/timers/{$timer->id}/actions/pause", [
        'latest_activity_id' => null,
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors('timer');
});

test('a user can resume a paused timer', function () {
    $user = User::factory()->create();
    $timer = Timer::factory()->for($user, 'owner')->create();
    $activity = $timer->activities()->create(['paused_at' => now()]);
    $this->actingAs($user);

    $response = $this->postJson("/api/timers/{$timer->id}/actions/{$activity->id}/resume");

    $response->assertOk();
    $this->assertNotNull($activity->fresh()->resumed_at);
});

test('a user cannot resume an already resumed activity', function () {
    $user = User::factory()->create();
    $timer = Timer::factory()->for($user, 'owner')->create();
    $activity = $timer->activities()->create(['paused_at' => now(), 'resumed_at' => now()]);
    $this->actingAs($user);

    $response = $this->postJson("/api/timers/{$timer->id}/actions/{$activity->id}/resume");

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors('timer');
});

test('a user cannot resume an activity that is not the latest one', function () {
    $user = User::factory()->create();
    $timer = Timer::factory()->for($user, 'owner')->create();
    $oldActivity = $timer->activities()->create(['paused_at' => now()->subMinute(), 'resumed_at' => now()]);
    $latestActivity = $timer->activities()->create(['paused_at' => now()]);
    $this->actingAs($user);

    $response = $this->postJson("/api/timers/{$timer->id}/actions/{$oldActivity->id}/resume");

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors('same_latest_activity');
});

test('a user can stop a running timer', function () {
    $user = User::factory()->create();
    $timer = Timer::factory()->for($user, 'owner')->create();
    $this->actingAs($user);

    $response = $this->postJson("/api/timers/{$timer->id}/actions/stop");

    $response->assertOk();
    $this->assertNotNull($timer->fresh()->stopped_at);
});

test('a user can stop a paused timer', function () {
    $user = User::factory()->create();
    $timer = Timer::factory()->for($user, 'owner')->create();
    $timer->activities()->create(['paused_at' => now()]);
    $this->actingAs($user);

    $response = $this->postJson("/api/timers/{$timer->id}/actions/stop");

    $response->assertOk();
    $this->assertNotNull($timer->fresh()->stopped_at);
});

test('a user cannot stop an already stopped timer', function () {
    $user = User::factory()->create();
    $timer = Timer::factory()->for($user, 'owner')->create(['stopped_at' => now()]);
    $this->actingAs($user);

    $response = $this->postJson("/api/timers/{$timer->id}/actions/stop");

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors('timer');
});

test('a user can add trackables to a timer', function () {
    $user = User::factory()->create();
    $timer = Timer::factory()->for($user, 'owner')->create();
    $project = Project::factory()->create();
    $task = Task::factory()->create();

    $this->actingAs($user);

    $response = $this->postJson("/api/timers/{$timer->id}/time-trackables", [
        'time_trackables' => [
            ['type' => 'project', 'id' => $project->id],
            ['type' => 'task', 'id' => $task->id],
        ],
    ]);

    $response->assertOk();

    $this->assertDatabaseHas('timer_matrix', [
        'timer_id' => $timer->id,
        'time_trackable_type' => $project->getMorphClass(),
        'time_trackable_id' => $project->id,
    ]);

    $this->assertDatabaseHas('timer_matrix', [
        'timer_id' => $timer->id,
        'time_trackable_type' => $task->getMorphClass(),
        'time_trackable_id' => $task->id,
    ]);
});

test('a user cannot add time-trackables with an invalid type', function () {
    $user = User::factory()->create();
    $timer = Timer::factory()->for($user, 'owner')->create();
    $this->actingAs($user);

    $response = $this->postJson("/api/timers/{$timer->id}/time-trackables", [
        'time_trackables' => [
            ['type' => 'invalid-type', 'id' => 1],
        ],
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['time_trackables.0.type']);
});

test('a user cannot add time-trackables with a non-existent id', function () {
    $user = User::factory()->create();
    $timer = Timer::factory()->for($user, 'owner')->create();
    $this->actingAs($user);

    $response = $this->postJson("/api/timers/{$timer->id}/time-trackables", [
        'time_trackables' => [
            ['type' => 'project', 'id' => 999],
        ],
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['time_trackables.0.id']);
});

test('timer related models are loaded', function () {
    $user = User::factory()->create();
    $timer = Timer::factory()->for($user, 'owner')->create();
    $project = Project::factory()->create();
    $task = Task::factory()->create();

    $this->actingAs($user);

    $response = $this->postJson("/api/timers/{$timer->id}/time-trackables", [
        'time_trackables' => [
            ['type' => 'project', 'id' => $project->id],
            ['type' => 'task', 'id' => $task->id],
        ],
    ]);

    $response->assertOk();

    $timer->load('matrices.timeTrackable');

    $this->assertTrue($timer->matrices->contains('timeTrackable.id', $project->id));
    $this->assertTrue($timer->matrices->contains('timeTrackable.id', $task->id));
});

test('timer resource includes related projects and tasks', function () {
    $user = User::factory()->create();
    $timer = Timer::factory()->for($user, 'owner')->create();
    $project1 = Project::factory()->create();
    $project2 = Project::factory()->create();
    $task = Task::factory()->create();

    $timer->projects()->attach($project1);
    $timer->tasks()->attach($task);
    $task->projects()->attach($project2);

    $this->actingAs($user);

    $response = $this->getJson("/api/timers/{$timer->id}");

    $response->assertOk();
    $response->assertJsonCount(2, 'timer.projects');
    $response->assertJsonPath('timer.projects.0.id', $project1->id);
    $response->assertJsonPath('timer.projects.1.id', $project2->id);
    $response->assertJsonCount(1, 'timer.tasks');
    $response->assertJsonPath('timer.tasks.0.id', $task->id);
});

test('timer resource includes unique related projects and tasks', function () {
    $user = User::factory()->create();
    $timer = Timer::factory()->for($user, 'owner')->create();
    $project = Project::factory()->create();
    $task = Task::factory()->create();

    $timer->projects()->attach($project);
    $timer->tasks()->attach($task);
    $task->projects()->attach($project);

    $this->actingAs($user);

    $response = $this->getJson("/api/timers/{$timer->id}");

    $response->assertOk();
    $response->assertJsonCount(1, 'timer.projects');
    $response->assertJsonPath('timer.projects.0.id', $project->id);
    $response->assertJsonCount(1, 'timer.tasks');
    $response->assertJsonPath('timer.tasks.0.id', $task->id);
});

test('timer elapsed seconds calculation without activities', function () {
    $user = User::factory()->create();
    $timer = Timer::factory()->for($user, 'owner')->create([
        'started_at' => now()->subSeconds(30),
    ]);

    // With no activities, elapsed time should be the difference between now and started_at
    $expectedElapsed = 30;
    $this->assertEquals($expectedElapsed, $timer->elapsed_seconds);
});

test('timer elapsed seconds calculation with stopped timer', function () {
    $user = User::factory()->create();
    $timer = Timer::factory()->for($user, 'owner')->create([
        'started_at' => now()->subSeconds(60),
        'stopped_at' => now()->subSeconds(30),
    ]);

    // When timer is stopped, elapsed time is the difference between stopped_at and started_at
    $expectedElapsed = 30;
    $this->assertEquals($expectedElapsed, $timer->elapsed_seconds);
});

test('timer elapsed seconds calculation with single activity', function () {
    $user = User::factory()->create();
    $timer = Timer::factory()->for($user, 'owner')->create([
        'started_at' => now()->subSeconds(60),
    ]);

    // Create an activity that pauses after 20 seconds
    $timer->activities()->create([
        'paused_at' => $timer->started_at->copy()->addSeconds(20),
        'resumed_at' => $timer->started_at->copy()->addSeconds(40),
    ]);

    // Total time is 60 seconds, with 20 seconds of break (pause to resume)
    // So elapsed time should be 60 - 20 = 40 seconds
    $expectedElapsed = 40;
    $this->assertEquals($expectedElapsed, $timer->elapsed_seconds);
});

test('timer elapsed seconds calculation with multiple activities', function () {
    $user = User::factory()->create();
    $timer = Timer::factory()->for($user, 'owner')->create([
        'started_at' => now()->subSeconds(100),
    ]);

    // First activity: pause after 20s and resume after 10s break (resumes at 30s mark)
    $timer->activities()->create([
        'paused_at' => $timer->started_at->copy()->addSeconds(20),
        'resumed_at' => $timer->started_at->copy()->addSeconds(30),
    ]);

    // Second activity: pause after another 30s (at 60s mark) and still paused (now is at 100s mark)
    $timer->activities()->create([
        'paused_at' => $timer->started_at->copy()->addSeconds(60),
        // resumed_at is null, so this break continues until now
    ]);

    // Total time: 100 seconds
    // First break: 10 seconds (from 20s to 30s mark)
    // Second break: 40 seconds (from 60s mark to now (100s))
    // Elapsed time: 100 - 10 - 40 = 50 seconds
    $expectedElapsed = 50;
    $this->assertEquals($expectedElapsed, $timer->elapsed_seconds);
});

test('timer elapsed seconds calculation with stopped timer and activities', function () {
    $user = User::factory()->create();
    $timer = Timer::factory()->for($user, 'owner')->create([
        'started_at' => now()->subSeconds(100),
        'stopped_at' => now()->subSeconds(20), // Stopped 20 seconds ago, so ran for 80 seconds total
    ]);

    // Create an activity that pauses after 20 seconds from start and resumes after 10 seconds break
    $timer->activities()->create([
        'paused_at' => $timer->started_at->copy()->addSeconds(20),
        'resumed_at' => $timer->started_at->copy()->addSeconds(30),
    ]);

    // Total time: 80 seconds (from start to stop)
    // Break time: 10 seconds (from 20s to 30s mark)
    // Elapsed time: 80 - 10 = 70 seconds
    $expectedElapsed = 70;
    $this->assertEquals($expectedElapsed, $timer->elapsed_seconds);
});

test('a user can reset a timer', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    // Create a timer with start time
    $timer = Timer::factory()->for($user, 'owner')->create([
        'started_at' => now()->subSeconds(60),
    ]);

    // Add some activities to the timer
    $timer->activities()->create([
        'paused_at' => $timer->started_at->copy()->addSeconds(20),
        'resumed_at' => $timer->started_at->copy()->addSeconds(40),
    ]);

    // Reset the timer
    $response = $this->postJson("/api/timers/{$timer->id}/actions/reset");

    $response->assertOk();
    
    // Check that the timer no longer exists in the database (deleted)
    $this->assertModelMissing($timer);
});

test('a user can reset a timer that is currently running', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    // Create a running timer
    $timer = Timer::factory()->for($user, 'owner')->create([
        'started_at' => now()->subSeconds(30),
    ]);

    // Reset the timer
    $response = $this->postJson("/api/timers/{$timer->id}/actions/reset");

    $response->assertOk();
    
    // Check that the timer no longer exists in the database (deleted)
    $this->assertModelMissing($timer);
});

test('trying to reset a timer that was already deleted returns not found', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    // Create a timer and reset it (deleting it)
    $timer = Timer::factory()->for($user, 'owner')->create([
        'started_at' => now()->subSeconds(30),
    ]);

    // First reset - this should delete the timer
    $this->postJson("/api/timers/{$timer->id}/actions/reset")->assertOk();
    
    // Verify timer is deleted
    $this->assertModelMissing($timer);

    // Try to reset again - this should return 404 since the timer doesn't exist
    $response = $this->postJson("/api/timers/{$timer->id}/actions/reset");
    
    // Expecting a 404 response since the timer no longer exists
    $response->assertNotFound();
});
