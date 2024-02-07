<?php

namespace App\Models;

use App\Traits\HasApiQuery;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Event extends Model
{
    use HasFactory, HasUuids, HasApiQuery;

    protected $fillable = [
        'name',
        'code',
        'date',
        'user_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function guests(): HasMany
    {
        return $this->hasMany(Guest::class);
    }

    public function scanners(): HasMany
    {
        return $this->hasMany(Scanner::class);
    }

    public function tables(): HasMany
    {
        return $this->hasMany(Table::class);
    }

    public function scans(): HasManyThrough
    {
        return $this->hasManyThrough(Scan::class, Scanner::class);
    }

}
