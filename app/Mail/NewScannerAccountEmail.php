<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewScannerAccountEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;
    protected $event;
    protected $userIsNewer;
    protected $password;

    /**
     * Create a new message instance.
     */
    public function __construct(public $mailData)
    {
        $this->user = $mailData['user'];
        $this->event = $mailData['event'];
        $this->userIsNewer = $mailData['user_is_newer'];
        $this->password = $mailData['password'];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Scanneur ajouté à l\'événement '.$this->event->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'scanner-account-mail',
            with: [
                'message' => $this,
                'user' => $this->user,
                'event' => $this->event,
                'userIsNewer' => $this->userIsNewer,
                'password' => $this->password,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
