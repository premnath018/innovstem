<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WebinarRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;


    protected $user;
    protected $webinar;
    /**
     * Create a new message instance.
     */
    public function __construct($user,$webinar)
    {
        $this->user = $user;
        $this->webinar = $webinar;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->webinar->title.' - Webinar Registration Mail | InnovStem',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.webinar-registration',
            with : [
                'user' => $this->user,
                'webinar' => $this->webinar,
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
