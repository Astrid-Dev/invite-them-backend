<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    private $guest;

    /**
     * Create a new message instance.
     */
    public function __construct(public $mailData)
    {
        $this->guest = $mailData['guest'];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invitation au marriage de Simon et Prisca',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail-invitation',
            with: [
                'message' => $this,
                'guest' => $this->guest,
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
        return [
            Attachment::fromPath(
                public_path('storage/'.$this->guest->invitation_file_relative_path)
            )
            ->as(preg_replace('/[^a-zA-Z0-9_ -]/s','', $this->guest->name) . ' - invitation au mariage de Simon et Prisca.pdf')
            ->withMime('application/pdf')
        ];
    }
}
