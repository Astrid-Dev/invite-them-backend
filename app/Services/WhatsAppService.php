<?php

namespace App\Services;

use App\Enums\GuestConfirmationStatus;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class WhatsAppService
{
    private $endingMessage = "\n\n*NB: Si jamais le lien ne fonctionne pas, veuillez répondre à ce message ou enregistrer ce numéro ou alors copier-coller le lien dans votre navigateur.*\n\nMerci de votre attention!";

//    public function sendWhatsAppMessage()
//    {
//        $twilioSid = config('app.twilio.sid');
//        $twilioToken = config('app.twilio.auth_token');
//        $twilioWhatsAppNumber = config('app.twilio.whatsapp_number');
//        $recipientNumber = '+237680125172';
//        $message = "Hello from Twilio WhatsApp API in Laravel! 🚀";
//
//        try {
//            $twilio = new Client($twilioSid, $twilioToken);
//            $twilio->messages->create(
//                $recipientNumber,
//                [
//                    "from" => $twilioWhatsAppNumber,
//                    "body" => $message,
//                ]
//            );
//        } catch (\Exception $e) {
//            dd($e);
//        }
//    }

    public function sendWhatsAppInvitationMessage($guest)
    {
        $invitationFileService = new InvitationFileService();
        if (!$invitationFileService->hasInvitationFile($guest)) {
            $invitationFileService->generateInvitationFile($guest);
        }

        $client = new \GuzzleHttp\Client();

        $client->request('POST', 'https://gate.whapi.cloud/messages/document', [
            'body' => json_encode([
                'to' => $guest->whatsapp,
                'media' => asset('storage/'.$guest->invitation_file_relative_path),
                'caption' => "👆Ci-joint votre billet d'invitation !\n\nVous pourrez confirmer votre présence à tout moment via le lien suivant : ".$guest->presence_confirmation_url."\n👆\n\n".$this->endingMessage,
                'filename' => preg_replace('/[^a-zA-Z0-9_ -]/s','', $guest->name) . ' - invitation au mariage de Simon et Prisca.pdf',
            ]),
            'headers' => [
                'accept' => 'application/json',
                'authorization' => 'Bearer '.env('WHAPI_TOKEN'),
                'content-type' => 'application/json',
            ],
        ]);
    }

    public function sendWhatsAppEventReminder($guest, $eventName)
    {
        $client = new \GuzzleHttp\Client();
        $client->request('POST', 'https://gate.whapi.cloud/messages/text', [
            'body' => json_encode([
                'to' => $guest->whatsapp,
                'body' => "*Rappel* :\n\nVous êtes invité(e) au *Mariage de Simon & Prisca* le *samedi 27 juillet 2024* à *13 heures* à *Makénéné*.\n\n" . ($guest->confirmation_status->value === GuestConfirmationStatus::CONFIRMED->value ? "Vous avez confirmé votre présence." : ($guest->confirmation_status->value === GuestConfirmationStatus::PENDING->value ? "Vous n'avez pas encore confirmé votre présence." : "Vous avez confirmé votre absence.")) . "\n\nPour changer de statut, veuillez cliquer sur le lien suivant : ".$guest->presence_confirmation_url.$this->endingMessage,
            ]),
            'headers' => [
                'accept' => 'application/json',
                'authorization' => 'Bearer '.env('WHAPI_TOKEN'),
                'content-type' => 'application/json',
            ],
        ]);
    }

    public function sendNoticeMessage($whatsappNumber)
    {
        $client = new \GuzzleHttp\Client();
        $client->request('POST', 'https://gate.whapi.cloud/messages/text', [
            'body' => json_encode([
                'to' => $whatsappNumber,
                'body' => "Bonjour!🖐\n\nNous vous informons que vous avez été invité(e) à un événement spécial. Les détails vous seront envoyés sous peu.\n\nMerci de votre attention!",
            ]),
            'headers' => [
                'accept' => 'application/json',
                'authorization' => 'Bearer '.env('WHAPI_TOKEN'),
                'content-type' => 'application/json',
            ],
        ]);
    }
}
