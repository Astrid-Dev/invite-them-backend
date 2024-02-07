<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Ilovepdf\Editpdf\ImageElement;
use Ilovepdf\Editpdf\TextElement;
use Ilovepdf\EditpdfTask;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class InvitationFileService
{
    public function generateInvitationFile($guest)
    {
        set_time_limit(0);
        $template = public_path('template.pdf');
        $folder = 'events/'.$guest->event_id.'/invitations';
        Storage::makeDirectory('public/'.$folder);
        $filePath = public_path('/storage/'.$guest->invitation_file_relative_path);
        file_put_contents($filePath, file_get_contents($template));
        self::fillInvitation($guest, $filePath, public_path('/storage/'.$folder));
    }

    private function fillInvitation($guest, $filePath, $folder)
    {
        // Create a new task
        $editpdfTask = new EditpdfTask(env('ILOVEPDF_PUBLIC_KEY'), env('ILONEPDF_SECRET_KEY'));
        // Add files to task for upload
        $pdfFile = $editpdfTask->addFile($filePath);
        // Create an element
        $guestNameElement = new TextElement();
        $guestName = $guest->name;
        // Set your element options
        $guestNameElement->setText(
            str_pad(strtoupper($guestName), (strlen($guestName) > 25 ? 34 : 44), ' ', STR_PAD_BOTH));
        $guestNameElement->setPages(2);
        $guestNameElement->setFontSize(10);
        $guestNameElement->setBold();
        $guestNameElement->setFontColor('#003566');
        $guestNameElement->setCoordinates(65, 295);
        // Add text element to task
        $editpdfTask->addElement($guestNameElement);

        $guestSeatsElement = new TextElement();
        $guestSeatsElement->setText(str_pad($guest->seats.'', 30, ' ', STR_PAD_BOTH));
        $guestSeatsElement->setPages(2);
        $guestSeatsElement->setFontSize(10);
        $guestSeatsElement->setBold();
        $guestSeatsElement->setFontColor('#003566');
        $guestSeatsElement->setCoordinates(140, 263);
        $editpdfTask->addElement($guestSeatsElement);

        $qrCode = QrCode::gradient(192, 142, 63, 7, 2, 1, 'diagonal')
            ->size(90)
            ->format('png')
            ->generate(
                json_encode([
                    'gId' => $guest->id,
                    'eId' => $guest->event_id,
                ]),
                public_path('qrcode.png')
            );

        $qrCodeFile = $editpdfTask->addFile(public_path('qrcode.png'));
        $qrCodeElement = new ImageElement();
        $qrCodeElement->setFile($qrCodeFile);
        $qrCodeElement->setPages(4);
        $qrCodeElement->setCoordinates(420, 60);
        $editpdfTask->addElement($qrCodeElement);

        // Execute the task
        $editpdfTask->execute();
        // Download the package files
        $editpdfTask->download($folder);

        Storage::delete(public_path('qrcode.png'));
    }

    public function deleteInvitationFile($guest)
    {
        $filename = '/storage/'.$guest->invitation_file_relative_path;
        if (file_exists($filename)) {
            unlink(public_path());
        }
    }

    public function hasInvitationFile($guest)
    {
        $folder = 'events/'.$guest->event_id.'/invitations';
        $filename = '/storage/'.$guest->invitation_file_relative_path;
        return file_exists(public_path($filename));
    }
}
