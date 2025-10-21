<?php

use App\Models\User;
use App\Project\Models\Project;
use App\Timer\Models\Timer;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a user can add existing timers to a project they are participating in', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['name' => 'Project with Existing Timers']);
    $user->participatingProjects()->attach($project->id);
    $this->actingAs($user);

    $timer1 = Timer::factory()->create();
    $timer2 = Timer::factory()->create();

    $response = $this->postJson("/api/projects/{$project->id}/timers", ['timers' => [$timer1->id, $timer2->id]]);

    $response->assertOk();
    $response->assertJsonPath('project.timers.0.id', $timer1->id);
    $response->assertJsonPath('project.timers.1.id', $timer2->id);
});
