<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Project\Models\Project;
use App\Task\Models\Task;
use App\Timer\Models\Timer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;
    
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array{
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function timers(){
        return $this->hasMany(Timer::class, 'owner_id');
    }

    public function participatingProjects(){
        return $this->belongsToMany(Project::class, 'project_participant', foreignPivotKey: 'participant_id');
    }

    public function participatingTasks(){
        return $this->belongsToMany(Task::class, 'task_participant', foreignPivotKey: 'participant_id');
    }
}
