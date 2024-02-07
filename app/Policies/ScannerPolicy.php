<?php

namespace App\Policies;

use App\Models\Scanner;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ScannerPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    public function viewAnyForEvent(User $user, $eventId): bool
    {
        return $user->events()->where('id', $eventId)->exists();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Scanner $scanner): bool
    {
        return $user->events()->where('id', $scanner->event_id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, $eventId): bool
    {
        return $user->events()->where('id', $eventId)->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Scanner $scanner): bool
    {
        return $user->eventScanners()
            ->where('event_id', $scanner->event_id)
            ->where('scanner.id', $scanner->id)
            ->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Scanner $scanner): bool
    {
        return $user->eventScanners()
            ->where('event_id', $scanner->event_id)
            ->where('scanner.id', $scanner->id)
            ->exists();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Scanner $scanner): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Scanner $scanner): bool
    {
        //
    }
}
