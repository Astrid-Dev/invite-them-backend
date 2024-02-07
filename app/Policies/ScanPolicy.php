<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\Guest;
use App\Models\Scan;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ScanPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    public function viewAnyForEvent(User $user, $eventId): bool
    {
        return $user->scanners()
            ->where('scanner.event_id', $eventId)
            ->exists();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Scan $scan): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, $eventId, $guestId): bool
    {
        $userIsScanner = $user->scanners()
            ->where('scanner.event_id', $eventId)
            ->exists();
        $guestIsInEvent = Guest::query()
            ->where('event_id', $eventId)
            ->where('id', $guestId)
            ->exists();
        $guestIsAlreadyScanned = Scan::query()
            ->where('guest_id', $guestId)
            ->whereHas('event', function ($query) use ($eventId) {
                $query->where('events.id', $eventId);
            })
            ->exists();

        return $userIsScanner && $guestIsInEvent && !$guestIsAlreadyScanned;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Scan $scan): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Scan $scan): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Scan $scan): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Scan $scan): bool
    {
        //
    }
}
