<?php

namespace App\Models;

use App\Enums\GuestConfirmationStatus;
use App\Traits\HasApiQuery;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Znck\Eloquent\Relations\BelongsToThrough;

class Guest extends Model
{
    use HasFactory, HasUuids, HasApiQuery, \Znck\Eloquent\Traits\BelongsToThrough;

    protected $fillable = [
        'name',
        'hint',
        'email',
        'whatsapp',
        'seats',
        'confirmation_status',
        'has_send_email_invitation',
        'has_send_whatsapp_invitation',
        'event_id',
        'table_id',
    ];

    protected $casts = [
        'has_send_email_invitation' => 'boolean',
        'has_send_whatsapp_invitation' => 'boolean',
        'confirmation_status' => GuestConfirmationStatus::class
    ];

    protected $appends = [
        'invitation_file_relative_path',
        'presence_confirmation_url'
    ];

    public function getInvitationFileRelativePathAttribute(): string
    {
        return 'events/' . $this->event_id . '/invitations/' . $this->id . '.pdf';
    }

    public function getPresenceConfirmationUrlAttribute(): string
    {
        return env('FRONTEND_URL').'/guest/presence-confirmation?eId='.$this->event_id.'&gId='.$this->id;
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function scan(): HasOne
    {
        return $this->hasOne(Scan::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function scanner(): BelongsToThrough
    {
        return $this->belongsToThrough(Scanner::class, Scan::class);
    }
}
