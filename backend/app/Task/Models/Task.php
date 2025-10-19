<?php

namespace App\Task\Models;

use App\Models\ProjectTask;
use Database\Factories\TaskFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Project\Models\Project;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public static function newFactory(): TaskFactory{
        return TaskFactory::new();
    }

    public function participants(){
        return $this->belongsToMany(User::class, 'task_participant', relatedPivotKey: 'participant_id');
    }

    public function projects(){
        return $this->belongsToMany(Project::class, 'project_task', relation: ProjectTask::class);
    }
}
