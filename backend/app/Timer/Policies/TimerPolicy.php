<?php

namespace App\Timer\Policies;

use App\Timer\Models\Timer;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TimerPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Timer $timer): bool
    {
        return $timer->owner_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Timer $timer): bool
    {
        return $timer->owner_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Timer $timer): bool
    {
        return $timer->owner_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Timer $timer): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Timer $timer): bool
    {
        return false;
    }

    public function act(User $user, ?Timer $timer = null): bool
    {
        return $timer === null || $timer->owner_id === $user->id;
    }

    public function addTimeTrackables(User $user, Timer $timer): bool
    {
        return $timer->owner_id === $user->id;
    }
}
