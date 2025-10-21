<?php

namespace App\Services;

use App\Project\Models\Project;

class ProjectTimerService
{
    public function __construct() {}

    public function addExistingTimers(Project $project, array $timerIds)
    {
        $project->timers()->syncWithoutDetaching($timerIds);

        return $project->load('timers');
    }
}
