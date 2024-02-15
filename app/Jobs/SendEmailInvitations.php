<?php

namespace App\Jobs;

use App\Mail\InvitationMail;
use App\Services\InvitationFileService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailInvitations implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected $guests)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        set_time_limit(0);
        $invitationFileService = new InvitationFileService();
        foreach ($this->guests as $guest) {
            if (!$invitationFileService->hasInvitationFile($guest)) {
                $invitationFileService->generateInvitationFile($guest);
            }
            Mail::to($guest->email)->send(new InvitationMail([
                'guest' => $guest,
            ]));

            $guest->update(['has_send_email_invitation' => true]);
        }
    }
}
