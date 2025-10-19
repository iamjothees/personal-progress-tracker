<?php

use App\Timer\Models\Timer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guests cannot access any timer endpoints', function () {
    $this->getJson('/api/timers')->assertUnauthorized();
    $this->postJson('/api/timers/actions/start')->assertUnauthorized();
    $this->getJson('/api/timers/1')->assertUnauthorized();
    $this->postJson('/api/timers/1/actions/pause')->assertUnauthorized();
    $this->postJson('/api/timers/1/actions/1/resume')->assertUnauthorized();
    $this->postJson('/api/timers/1/actions/stop')->assertUnauthorized();
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