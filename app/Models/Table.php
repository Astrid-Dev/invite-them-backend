<?php

namespace App\Models;

use App\Traits\HasApiQuery;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Table extends Model
{
    use HasFactory, HasUuids, HasApiQuery;

    protected $fillable = [
        'name',
        'capacity',
        'event_id',
    ];

    protected $withCount = [
        'assignedGuests',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function assignedGuests(): HasMany
    {
        return $this->hasMany(Guest::class);
    }
}
