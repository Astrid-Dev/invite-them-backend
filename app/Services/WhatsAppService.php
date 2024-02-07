<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class WhatsAppService
{
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

        $client = new \GuzzleHttp\Client();

        try {
            $client->request('POST', 'https://gate.whapi.cloud/messages/document', [
                'body' => json_encode([
                    'to' => $guest->whatsapp,
                    'media' => asset('storage/'.$guest->invitation_file_relative_path),
                    'caption' => "Le *samedi 27 juillet 2024* à *13 heures*, deux étoiles vont s’unir sous les cieux de la belle ville de *Makénéné* et de sa *paroisse St-Joseph*. Leur amour brille de mille feux et ils veulent le graver à jamais dans leur cœur et dans leur essence.\n*Simon* et *Prisca* vous invitent à partager cette féerie de l’amour avec eux. Les réjouissances se poursuivront à leur domicile le même jour au quartier *Carrière* à partir de *16h* précises.\nMais avant, ils vont communier avec les ancêtres par la cérémonie de la dot le *vendredi 26 juillet 2024* dès *16h*, au domicile des parents de Prisca au quartier *Hôpital de Makénéné*.\nNous espérons que vous accepterez d’être les témoins de la concrétisation de cet amour, drapé d’une tenue chic et glamour.\nPour la circonstance, veuillez laisser vos appareils photos dans le fond de vos poches et vos sacoches, puisqu’ils ont prévu un photographe pour vous permettre de profiter pleinement de la cérémonie.\nPour que tout soit parfait dans les moindres détails, les amoureux ont besoin d’une réponse avant le 01er juillet.\n\nVous pourrez confirmer votre présence à tout moment via le lien suivant : ".$guest->presence_confirmation_url."\n\nCi-joint votre billet d'invitation !",
                    'filename' => preg_replace('/[^a-zA-Z0-9_ -]/s','', $guest->name) . ' - invitation au mariage de Simon et Prisca.pdf',
                ]),
                'headers' => [
                    'accept' => 'application/json',
                    'authorization' => 'Bearer '.env('WHAPI_TOKEN'),
                    'content-type' => 'application/json',
                ],
            ]);
            $guest->update(['has_send_whatsapp_invitation' => true]);

            Log::info('Invitation sent');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
