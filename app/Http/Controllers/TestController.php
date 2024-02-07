<?php

namespace App\Http\Controllers;

use App\Mail\InvitationMail;
use App\Models\Guest;
use App\Models\Scanner;
use App\Services\InvitationFileService;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Mail;
use Ilovepdf\Editpdf\ImageElement;
use Ilovepdf\Editpdf\TextElement;
use Ilovepdf\EditpdfTask;
use Ilovepdf\File;
use Ilovepdf\Ilovepdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TestController extends Controller
{
    /**
     * @throws \Exception
     */
    public function index(WhatsAppService $whatsAppService)
    {
        set_time_limit(120);
//        Mail::to('astridjounang@gmail.com')->send(new InvitationMail([
//            'name' => 'Demo',
//        ]));

        $whatsAppService->sendWhatsAppInvitationMessage();

//        $guests = Guest::all();
//        foreach ($guests as $guest) {
//            InvitationManagementService::generateInvitationFile($guest);
//        }


//        // Create a new task
//        $editpdfTask = new EditpdfTask(env('ILOVEPDF_PUBLIC_KEY'), env('ILONEPDF_SECRET_KEY'));
//        // Add files to task for upload
//        $pdfFile = $editpdfTask->addFile(public_path('template.pdf'));
//        // Create an element
//        $guestNameElement = new TextElement();
//        $guestName = "Nom de l'invite";
//        // Set your element options
//        $guestNameElement->setText(
//            str_pad(strtoupper($guestName), (strlen($guestName) > 25 ? 30 : 40), ' ', STR_PAD_BOTH));
//        $guestNameElement->setPages(2);
//        $guestNameElement->setFontSize(11);
//        $guestNameElement->setBold();
//        $guestNameElement->setFontColor('#4E4C4B');
//        $guestNameElement->setCoordinates(65, 295);
//        // Add text element to task
//        $editpdfTask->addElement($guestNameElement);
//
//        $guestSeatsElement = new TextElement();
//        $guestSeatsElement->setText(str_pad('2', 30, ' ', STR_PAD_BOTH));
//        $guestSeatsElement->setPages(2);
//        $guestSeatsElement->setFontSize(11);
//        $guestSeatsElement->setBold();
//        $guestSeatsElement->setFontColor('#4E4C4B');
//        $guestSeatsElement->setCoordinates(140, 263);
//        $editpdfTask->addElement($guestSeatsElement);
//
//        $qrCode = QrCode::gradient(192, 142, 63, 7, 2, 1, 'diagonal')
//            ->size(90)
//            ->format('png')
//            ->generate(
//                'Hello, World!',
//                public_path('qrcode.png')
//            );
//
//        $qrCodeFile = $editpdfTask->addFile(public_path('qrcode.png'));
//        $qrCodeElement = new ImageElement();
//        $qrCodeElement->setFile($qrCodeFile);
//        $qrCodeElement->setPages(4);
//        $qrCodeElement->setCoordinates(420, 60);
//        $editpdfTask->addElement($qrCodeElement);
//
//        // Execute the task
//        $editpdfTask->execute();
//        // Download the package files
//        $editpdfTask->download();
        return view('mail-invitation', [
            'name' => 'John Doe',
            'seats' => 2
        ]);
    }
}
