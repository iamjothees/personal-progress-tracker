<?php

namespace App\Project\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Project\Models\Project;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'tasks' => $this->whenLoaded('tasks', $this->tasks),
            'participants' => $this->whenLoaded('participants', $this->participants),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
