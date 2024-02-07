<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Znck\Eloquent\Relations\BelongsToThrough;

class Scan extends Pivot
{
    use HasUuids, \Znck\Eloquent\Traits\BelongsToThrough;

    protected $fillable = [
        'scanner_id',
        'guest_id',
    ];

    public function scanner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scanner_id');
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function scannerUser(): BelongsToThrough
    {
        return $this->belongsToThrough(User::class, Scanner::class);
    }

    public function event(): BelongsToThrough
    {
        return $this->belongsToThrough(Event::class, Guest::class);
    }
}
