<?php

namespace App\Http\Controllers\Me;

use App\Helpers\FunctionsHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Jobs\SendEmailInvitations;
use App\Jobs\SendWhatsappEventReminder;
use App\Jobs\SendWhatsAppInvitation;
use App\Mail\InvitationMail;
use App\Models\Event;
use App\Services\InvitationFileService;
use App\Services\WhatsAppService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EventController extends Controller
{
    public function getUserCreatedEvents()
    {
        Gate::authorize('viewCreated', Event::class);
        $events = Event::query()
            ->where('user_id', auth('api')->id())
            ->when(!empty(request('name')), function ($query) {
                $query->where('name', 'like', '%' . request('name') . '%');
            })
            ->when(!empty(request('min_date')), function ($query) {
                $query->where('date', '>=', request('min_date'));
            })
            ->when(!empty(request('max_date')), function ($query) {
                $query->where('date', '<=', request('max_date'));
            });
        $events = self::handleRetrieve($events, ['name', 'date', 'created_at']);

        return response()->json($events);
    }

    public function getEvent($eventId)
    {
        $event = Event::apiQuery()
            ->where('user_id', auth('api')->id())
            ->where('id', $eventId)
            ->firstOrFail();
        Gate::authorize('view', $event);

        return response()->json($event);
    }

    public function getAnEventStats($eventId)
    {
        $event = Event::apiQuery()
            ->where('user_id', auth('api')->id())
            ->where('id', $eventId)
            ->select(['id', 'user_id'])
            ->firstOrFail();
        Gate::authorize('view', $event);

        $tablesStats = $event->tables()
            ->select(DB::raw('count(*) as tables_count'))
            ->addSelect(DB::raw('sum(capacity) as total_capacity'))
            ->first();
        $scannersCount = $event->scanners()
            ->where('user_id', '!=', $event->user_id)
            ->count();
        $ticketsCount = $event->guests()->count();
        $guestsCount = $event->guests()->sum('seats');
        $confirmedGuestsCount = $event->guests()
            ->where('confirmation_status', 'confirmed')
            ->sum('seats');
        $pendingGuestsCount = $event->guests()
            ->where('confirmation_status', 'pending')
            ->sum('seats');
        $declinedGuestsCount = $event->guests()
            ->where('confirmation_status', 'declined')
            ->sum('seats');
        $assignedPlacesCount = $event->guests()
            ->whereHas('table')
            ->sum('seats');

        return response()->json([
            'tables' => [
                'count' => intval($tablesStats->tables_count),
                'total_capacity' => intval($tablesStats->total_capacity),
                'assigned_places_count' => intval($assignedPlacesCount)
            ],
            'scanners' => [
                'count' => $scannersCount
            ],
            'guests' => [
                'tickets_count' => intval($ticketsCount),
                'count' => intval($guestsCount),
                'confirmed_count' => intval($confirmedGuestsCount),
                'pending_count' => intval($pendingGuestsCount),
                'declined_count' => intval($declinedGuestsCount)
            ]
        ]);
    }

    private function calculateCreatedEventsStats() {
        $currentDate = now()->format('Y-m-d H:i:s');

        $stats = auth('api')->user()->events()
            ->select(DB::raw('count(*) as events_count'))
            ->addSelect(DB::raw("sum(case when date >= '".$currentDate."' then 1 else 0 end) as upcoming_events_count"))
            ->addSelect(DB::raw("sum(case when date < '".$currentDate."' then 1 else 0 end) as past_events_count"))
            ->first();

        return array_merge(
            $stats->toArray(),
            ['upcoming_events_count' => intval($stats->upcoming_events_count)],
            ['past_events_count' => intval($stats->past_events_count)]
        );
    }

    public function getCreatedEventsStats()
    {
        Gate::authorize('viewCreated', Event::class);
        return response()->json(
            $this->calculateCreatedEventsStats()
        );
    }

    public function createEvent(StoreEventRequest $request)
    {
        Gate::authorize('create', Event::class);
        $event = Event::query()->create(array_merge(
            $request->validated(),
            [
                'user_id' => auth('api')->id(),
                'code' => FunctionsHelper::generateCode(table: 'events', field: 'code')
            ]
        ));

        return response()->json([
            'message' => 'Event created successfully',
            'event' => $event,
            'new_stats' => $this->calculateCreatedEventsStats()
        ], 201);
    }

    public function updateEvent(UpdateEventRequest $request)
    {
        $event = $request->get('event');
        Gate::authorize('update', $event);
        $event->update($request->validated());

        return response()->json([
            'message' => 'Event updated successfully',
            'event' => $event,
            'new_stats' => $this->calculateCreatedEventsStats()
        ]);
    }

    public function deleteEvent()
    {
        $event = Event::query()
            ->where('user_id', auth('api')->id())
            ->select('id')
            ->firstOrFail(self::routeParam('eventId'));
        Gate::authorize('delete', $event);

        $event->delete();

        return response()->json(['message' => 'Event deleted successfully']);
    }

//    public function sendEmailInvitations($eventId)
//    {
//        $event = Event::query()
//            ->select(['id', 'user_id'])
//            ->where('user_id', auth('api')->id())
//            ->where('id', $eventId)
//            ->firstOrFail();
//        Gate::authorize('view', $event);
//
//        $guests = $event->guests()
//            ->whereNotNull('email')
//            ->where('has_send_email_invitation', false)
//            ->get();
//
//        SendEmailInvitations::dispatch($guests);
//
//        return response()->json([
//            'message' => 'Invitations sent successfully',
//        ]);
//    }

    public function sendEmailInvitations($eventId)
    {
        set_time_limit(0);
        $event = Event::query()
            ->select(['id', 'user_id'])
            ->where('user_id', auth('api')->id())
            ->where('id', $eventId)
            ->firstOrFail();
        Gate::authorize('view', $event);

        $guests = $event->guests()
            ->whereNotNull('email')
            ->where('has_send_email_invitation', false)
            ->get();

        $invitationFileService = new InvitationFileService();
        foreach ($guests as $guest) {
            if ($invitationFileService->hasInvitationFile($guest)) {
                $invitationFileService->generateInvitationFile($guest);
            }
            Mail::to($guest->email)->send(new InvitationMail([
                'guest' => $guest,
            ]));

            $guest->update(['has_send_email_invitation' => true]);
        }

        return response()->json([
            'message' => 'Invitations sent successfully',
        ]);
    }

//    public function sendWhatsAppInvitations($eventId)
//    {
//        $event = Event::query()
//            ->select(['id', 'user_id'])
//            ->where('user_id', auth('api')->id())
//            ->where('id', $eventId)
//            ->firstOrFail();
//        Gate::authorize('view', $event);
//
//        $guests = $event->guests()
//            ->whereNotNull('whatsapp')
//            ->where('has_send_whatsapp_invitation', false)
//            ->get();
//
//        SendWhatsAppInvitation::dispatch($guests);
//
//        return response()->json([
//            'message' => 'Invitations sent successfully',
//        ]);
//    }

    public function sendWhatsAppInvitations(WhatsAppService $whatsAppService, $eventId)
    {
        set_time_limit(0);

        $event = Event::query()
            ->select(['id', 'user_id'])
            ->where('user_id', auth('api')->id())
            ->where('id', $eventId)
            ->firstOrFail();
        Gate::authorize('view', $event);

        $event->guests()
            ->whereNotNull('whatsapp')
            ->where('has_send_whatsapp_invitation', false)
            ->chunkById(25, function (Collection $guests) use ($whatsAppService) {
                foreach ($guests as $guest) {
                    try {
                        $this->sendNoticeMessage($guest->whatsapp);
                    } catch (\Exception $e) {
                        Log::error($e->getMessage());
                    }

                    try {
                        $whatsAppService->sendWhatsAppInvitationMessage($guest);
                        $guest->update(['has_send_whatsapp_invitation' => true]);
                    } catch (\Exception $e) {
                        if ($e->getCode() === 524) {
                            $guest->update(['has_send_whatsapp_invitation' => true]);
                            continue;
                        } else {
                            abort(500, 'An error occurred while sending WhatsApp invitations. Please try again later.');
                        }
                    }
                }
            });

        return response()->json([
            'message' => 'Invitations sent successfully',
        ]);
    }

//    public function sendWhatsAppEventReminder($eventId)
//    {
//        $event = Event::query()
//            ->select(['id', 'user_id'])
//            ->where('user_id', auth('api')->id())
//            ->where('id', $eventId)
//            ->firstOrFail();
//        Gate::authorize('view', $event);
//
//        $guests = $event->guests()
//            ->whereNotNull('whatsapp')
//            ->where('has_send_whatsapp_invitation', true)
//            ->get();
//
//        SendWhatsappEventReminder::dispatch($guests, $event->name);
//
//        return response()->json([
//            'message' => 'Reminders sent successfully',
//        ]);
//    }

    public function sendWhatsAppEventReminder(WhatsAppService $whatsAppService, $eventId)
    {
        set_time_limit(0);

        $event = Event::query()
            ->select(['id', 'user_id'])
            ->where('user_id', auth('api')->id())
            ->where('id', $eventId)
            ->firstOrFail();
        Gate::authorize('view', $event);

        $event->guests()
            ->whereNotNull('whatsapp')
            ->where('has_send_whatsapp_invitation', true)
            ->chunkById(25, function (Collection $guests) use ($whatsAppService, $event) {
                foreach ($guests as $guest) {
                    try {
                        $whatsAppService->sendWhatsAppEventReminder($guest, $event->name);
                    } catch (\Exception $e) {
                        if ($e->getCode() === 524) {
                            continue;
                        } else {
                            abort(500, 'An error occurred while sending WhatsApp reminders. Please try again later.');
                        }
                    }
                }
            });

        return response()->json([
            'message' => 'Reminders sent successfully',
        ]);
    }
}
