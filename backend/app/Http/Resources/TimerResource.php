<?php

namespace App\Http\Resources;

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
        return [
            'id' => $this->id,
            'owner' => $this->owner,
            'started_at' => $this->started_at,
            'completed_at' => $this->completed_at,
            'latestActivity' => $this->latestActivity,
            'activities' => $this->whenLoaded('activities', $this->activities),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
