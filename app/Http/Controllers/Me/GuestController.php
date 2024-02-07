<?php

namespace App\Http\Controllers\Me;

use App\Enums\GuestConfirmationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGuestRequest;
use App\Http\Requests\UpdateGuestRequest;
use App\Jobs\CreateInvitationsFiles;
use App\Models\Guest;
use App\Services\InvitationFileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class GuestController extends Controller
{
    public function getEventGuests($eventId)
    {
        Gate::authorize('viewAnyForEvent', [Guest::class, $eventId]);

        $guests = Guest::apiQuery()
            ->whereHas('event', function ($query) use ($eventId) {
                $query->where('events.id', $eventId)
                    ->where('user_id', auth()->id());
            })
            ->when(!empty(request('search_query')), function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('name', 'like', '%' . request('search_query') . '%')
                        ->orWhere('email', 'like', '%' . request('search_query') . '%')
                        ->orWhere('hint', 'like', '%' . request('search_query') . '%')
                        ->orWhereHas('table', function ($subQuery) {
                            $subQuery->where('name', 'like', '%' . request('search_query') . '%');
                        });
                });
            })
            ->when(!empty(request('seats')), function ($query) {
                $query->whereIn('seats', explode(',', request('seats')));
            });

        $guests = self::handleRetrieve($guests, ['name', 'seats', 'created_at']);

        return response()->json($guests);
    }

    public function getGuest($eventId, $guestId)
    {
        $guest = Guest::apiQuery()
            ->where('event_id', $eventId)
            ->findOrFail($guestId);

        Gate::authorize('view', $guest);

        return response()->json($guest);
    }

    public function confirmGuestPresence($eventId, $guestId)
    {
        $guest = Guest::query()
            ->where('event_id', $eventId)
            ->findOrFail($guestId);

        $guest->fill(['confirmation_status' => GuestConfirmationStatus::CONFIRMED->value]);
        $guest->save();

        return response()->json([
            'message' => 'Guest presence confirmed successfully',
            'guest' => $guest
        ]);
    }

    public function confirmGuestAbsence($eventId, $guestId)
    {
        $guest = Guest::query()
            ->where('event_id', $eventId)
            ->findOrFail($guestId);

        $guest->fill(['confirmation_status' => GuestConfirmationStatus::DECLINED->value]);
        $guest->save();

        return response()->json([
            'message' => 'Guest absence confirmed successfully',
            'guest' => $guest
        ]);
    }

    public function createGuests(StoreGuestRequest $request)
    {
        $guests = $request->validated()['guests'];
        $eventId = $request->route('eventId');
        $newGuests = [];

        foreach ($guests as $guest) {
            $newGuest = Guest::query()
                ->create([
                    'name' => $guest['name'],
                    'hint' => $guest['hint'],
                    'email' => $guest['email'],
                    'whatsapp' => $guest['whatsapp'],
                    'seats' => $guest['seats'],
                    'table_id' => $guest['table_id'],
                    'event_id' => $eventId,
                ]);
            $newGuests[] = $newGuest;
        }
        CreateInvitationsFiles::dispatch($newGuests);

        return response()->json([
            'message' => 'Guests created successfully',
        ], 201);
    }

    public function updateGuest(UpdateGuestRequest $request)
    {
        $guest = $request->get('guest');
        $guest->fill($request->validated());
        if (!empty($request->get('email')) && $guest->email !== $request->get('email')) {
            $guest->fill(['has_send_email_invitation' => false]);
        }
        if (!empty($request->get('whatsapp')) && $guest->whatsapp !== $request->get('whatsapp')) {
            $guest->fill(['has_send_whatsapp_invitation' => false]);
        }

        $guest->save();

        if ((!empty($request->get('name')) && $guest->name !== $request->get('name')) ||
            ((!empty($request->get('seats'))) && $guest->seats !== $request->get('seats'))) {
            CreateInvitationsFiles::dispatch([$guest]);
        }

        return response()->json([
            'message' => 'Guest updated successfully',
            'guest' => $guest
        ]);
    }

    public function deleteGuest(Request $request, InvitationFileService $invitationManagementService)
    {
        $guest = Guest::query()
            ->where('event_id', $request->route('eventId'))
            ->select(['id', 'event_id'])
            ->findOrFail($request->route('guestId'));

        Gate::authorize('delete', $guest);
        $invitationManagementService->deleteInvitationFile($guest);
        $guest->delete();

        return response()->json([
            'message' => 'Guest deleted successfully',
        ]);
    }
}
