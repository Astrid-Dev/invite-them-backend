<?php

namespace App\Jobs;

use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWhatsappEventReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected $guest, protected $eventName)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        set_time_limit(0);
        $whatsAppService = new WhatsAppService();
        foreach ($this->guests as $guest) {
            $whatsAppService->sendWhatsAppEventReminder($guest, $this->eventName);
        }

    }
}
