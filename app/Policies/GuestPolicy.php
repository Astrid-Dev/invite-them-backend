<?php

namespace App\Policies;

use App\Models\Guest;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class GuestPolicy
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
    public function view(User $user, Guest $guest): bool
    {
        $userIsOwner = $user->events()->where('id', $guest->event_id)->exists();
        $userIsScanner = $user->scanners()->where('scanner.event_id', $guest->event_id)->exists();
        return $userIsOwner || $userIsScanner;
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
    public function update(User $user, Guest $guest): bool
    {
        return $user->guests()
            ->where('event_id', $guest->event_id)
            ->where('guests.id', $guest->id)
            ->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Guest $guest): bool
    {
        return $user->guests()
            ->where('event_id', $guest->event_id)
            ->where('guests.id', $guest->id)
            ->exists();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Guest $guest): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Guest $guest): bool
    {
        //
    }
}
