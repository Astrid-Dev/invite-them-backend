<?php

namespace App\Http\Controllers\Me;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTableRequest;
use App\Http\Requests\UpdateTableRequest;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TableController extends Controller
{
    public function getEventTables($eventId)
    {
        Gate::authorize('viewAnyForEvent', [Table::class, $eventId]);

        $tables = Table::apiQuery()
            ->whereHas('event', function ($query) use ($eventId) {
                $query->where('events.id', $eventId)
                    ->where('user_id', auth()->id());
            })
            ->when(!empty(request('name')), function ($query) {
                $query->where('name', 'like', '%' . request('name') . '%');
            })
            ->when((!empty(request('min_capacity')) && intval(request('min_capacity')) > 0), function ($query) {
                $query->where('capacity', '>=', intval(request('min_capacity')));
            })
            ->when((!empty(request('max_capacity')) && intval(request('max_capacity')) > 0), function ($query) {
                $query->where('capacity', '<=', intval(request('max_capacity')));
            });

        $tables = self::handleRetrieve($tables, ['name', 'capacity', 'created_at']);

        return response()->json($tables);
    }

    public function createTables(StoreTableRequest $request)
    {
        $tables = $request->validated()['tables'];
        $eventId = $request->route('eventId');

        foreach ($tables as $table) {
            Table::query()
                ->create([
                    'name' => $table['name'],
                    'capacity' => $table['capacity'],
                    'event_id' => $eventId,
                ]);
        }

        return response()->json([
            'message' => 'Tables created successfully',
        ], 201);
    }

    public function updateTable(UpdateTableRequest $request)
    {
        $table = $request->get('table');
        $table->fill($request->validated());
        $table->save();

        return response()->json([
            'message' => 'Table updated successfully',
            'table' => $table
        ]);
    }

    public function deleteTable(Request $request)
    {
        $table = Table::query()
            ->where('event_id', $request->route('eventId'))
            ->findOrFail($request->route('tableId'));

        Gate::authorize('delete', $table);
        $table->delete();

        return response()->json([
            'message' => 'Table deleted successfully',
        ]);
    }
}
