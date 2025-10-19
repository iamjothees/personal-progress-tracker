<?php

namespace App\Project\Models;

use App\Models\ProjectTask;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Task\Models\Task;
use Database\Factories\ProjectFactory;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected static function newFactory(): ProjectFactory{
        return ProjectFactory::new();
    }

    public function participants(){
        return $this->belongsToMany(User::class,  'project_participant', relatedPivotKey: 'participant_id');
    }

    public function tasks(){
        return $this->belongsToMany(Task::class, 'project_task', relation: ProjectTask::class);
    }
}
