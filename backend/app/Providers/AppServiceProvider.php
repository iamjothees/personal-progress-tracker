<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Context::add('now', now()->startOfSecond());
        Relation::morphMap([
            'project' => \App\Project\Models\Project::class,
            'task' => \App\Task\Models\Task::class,
        ]);
    }
}
