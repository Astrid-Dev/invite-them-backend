<?php

namespace App\Http\Controllers\Me;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreScannerRequest;
use App\Models\Guest;
use App\Models\Scan;
use App\Models\Scanner;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ScannerController extends Controller
{
    public function getEventScanners($eventId)
    {
        Gate::authorize('viewAnyForEvent', [Scanner::class, $eventId]);

        $scanners = Scanner::apiQuery()
            ->whereHas('event', function ($query) use ($eventId) {
                $query->where('events.id', $eventId)
                    ->where('events.user_id', auth()->id());
            })
            ->when(!empty(request('search_query')), function ($query) {
                $query->whereHas('user', function ($subQuery) {
                    $subQuery->where('users.pseudo', 'like', '%' . request('search_query') . '%');
                });
            })
            ->where('user_id', '!=', auth()->id());

        $scanners = self::handleRetrieve($scanners, ['pseudo', 'created_at']);

        return response()->json($scanners);
    }

    public function searchForNewScanner($eventId)
    {
        Gate::authorize('viewAnyForEvent', [Scanner::class, $eventId]);

        $users = User::query()
            ->where('id', '!=', auth('api')->id())
            ->whereDoesntHave('scanners', function ($query) use ($eventId) {
                $query->where('scanner.event_id', $eventId);
            })
            ->when(!empty(request('search_query')), function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('pseudo', 'like', '%' . request('search_query') . '%')
                        ->orWhere('email', 'like', '%' . request('search_query') . '%');
                });
            })
            ->when(!empty(request('exclude')), function ($query) {
                $query->whereNotIn('id', explode(',', request('exclude')));
            })
            ->orderBy('pseudo')
            ->take(5)
            ->get(['id', 'pseudo', 'email']);

        return response()->json($users);
    }

    public function createScanners(StoreScannerRequest $request, $eventId)
    {
        $userIds = $request->validated()['ids'] ?? null;
        $pseudo = $request->validated()['pseudo'] ?? null;
        $email = $request->validated()['email'] ?? null;

        if (!empty($userIds)) {
            foreach ($userIds as $userId) {
                Scanner::query()
                    ->create([
                        'user_id' => $userId,
                        'event_id' => $eventId,
                    ]);
            }
        } else {
            $user = User::query()
                ->create([
                    'pseudo' => $pseudo,
                    'email' => $email,
                    'password' => bcrypt('password'),
                ]);
            Scanner::query()
                ->create([
                    'user_id' => $user->id,
                    'event_id' => $eventId,
                ]);
        }

        return response()->json([
            'message' => 'Scanners created successfully',
        ], 201);
    }

    public function deleteScanner(Request $request, $eventId, $scannerId)
    {
        $scanner = Scanner::query()
            ->where('event_id', $eventId)
            ->whereHas('event', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->findOrFail($scannerId);

        Gate::authorize('delete', [Scanner::class, $scanner]);

        $user = User::query()
            ->withCount('scanners', 'events')
            ->where('id', $scanner->user_id)
            ->first();
        if ($user->scanners_count === 1 && $user->events_count === 0) {
            $user->delete();
        }
        $scanner->delete();

        return response()->json([
            'message' => 'Scanner deleted successfully',
        ]);
    }

    public function getGuestDetails($eventId, $guestId)
    {
        $guest = Guest::query()
            ->with(['scan.scanner:id,pseudo', 'table:id,name'])
            ->where('guests.event_id', $eventId)
            ->findOrFail($guestId);
        Gate::authorize('view', $guest);

        return response()->json($guest);
    }

    public function saveScan($eventId, $guestId)
    {
        Gate::authorize('create', [Scan::class, $eventId, $guestId]);
        $scan = Scan::query()
            ->create([
                'scanner_id' => auth('api')->id(),
                'guest_id' => $guestId,
            ]);

        $scansStats = Scan::query()
            ->select(DB::raw('count(*) as total_scans'))
            ->addSelect(
                DB::raw("sum(case when scanner_id = '".auth('api')->id()."' then 1 else 0 end) as scanner_scans")
            )->first();

        return response()->json([
            'message' => 'Scan saved successfully',
            'scan' => $scan,
            'scans_stats' => [
                'total' => intval($scansStats->total_scans),
                'perso' => intval($scansStats->scanner_scans),
            ]
        ], 201);
    }

    public function getScannerStats($eventId)
    {
        Gate::authorize('viewAnyForEvent', [Scan::class, $eventId]);

        $recentScans = Scan::query()
            ->with(['guest.table:id,name'])
            ->whereHas('event', function ($query) use ($eventId) {
                $query->where('events.id', $eventId);
            })
            ->where('scan.scanner_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $scansStats = Scan::query()
            ->select(DB::raw('count(*) as total_scans'))
            ->addSelect(
                DB::raw("sum(case when scanner_id = '".auth('api')->id()."' then 1 else 0 end) as scanner_scans")
            )->first();

        return response()->json([
            'recent_scans' => $recentScans,
            'scans_stats' => [
                'total' => intval($scansStats->total_scans),
                'perso' => intval($scansStats->scanner_scans),
            ]
        ]);
    }

}
