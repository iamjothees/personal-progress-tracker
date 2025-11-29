<?php

namespace App\Timer\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TimerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $projects = $this->whenLoaded(
            'projects', 
            function (){
                $projects = $this->projects;
                $taskProjects = $this->whenLoaded(
                    'tasks', 
                    fn() => $this->tasks->pluck('projects')->flatten(), 
                    collect()
                );
                return $projects->merge($taskProjects)->unique('id')->toResourceCollection();
            }
        );

        $tasks = $this->whenLoaded('tasks', $this->tasks->toResourceCollection());
        return [
            'id' => $this->id,
            'owner' => $this->owner,
            'started_at' => $this->started_at,
            'stopped_at' => $this->stopped_at,
            'elapsed_seconds' => $this->elapsed_seconds,
            'running' => $this->running,
            'latest_activity' => $this->latestActivity,
            'activities' => $this->whenLoaded('activities', $this->activities),
            'projects' => $projects,
            'tasks' => $tasks,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
