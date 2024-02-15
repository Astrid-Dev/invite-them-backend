<?php

namespace App\Jobs;

use App\Mail\NewScannerAccountEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendNewScannerAccountEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected $user, protected $event, protected $userIsNewer, protected $password = null)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        set_time_limit(0);
        Mail::to($this->user->email)->send(new NewScannerAccountEmail([
            'user' => $this->user,
            'event' => $this->event,
            'user_is_newer' => $this->userIsNewer,
            'password' => $this->password,
        ]));
    }
}
