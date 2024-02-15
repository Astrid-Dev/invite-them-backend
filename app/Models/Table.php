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

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function guests(): HasMany
    {
        return $this->hasMany(Guest::class);
    }

    public static function query()
    {
        return parent::query()->withSum('guests as assigned_guests_count', 'seats');
    }
}
