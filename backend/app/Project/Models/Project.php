<?php

namespace App\Project\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
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
}
