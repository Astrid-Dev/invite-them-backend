<?php

namespace App\Jobs;

use App\Services\InvitationFileService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CreateInvitationsFiles implements ShouldQueue
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
        try {
            $invitationFileService = new InvitationFileService();
            foreach ($this->guests as $guest) {
                $invitationFileService->generateInvitationFile($guest);
            }
            Log::info('Invitation files created');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
