<?php

namespace App\Http\Controllers;

use App\Mail\InvitationMail;
use App\Models\Guest;
use App\Models\Scanner;
use App\Services\InvitationFileService;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;
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
        $whatsAppService->sendNoticeMessage('237680125172');
    }
}
