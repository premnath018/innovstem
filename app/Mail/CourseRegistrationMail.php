<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CourseRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $course_name;
    protected $user;
    /**
     * Create a new message instance.
     */
    public function __construct($user,$course_name)
    {
        $this->course_name = $course_name;
        $this->user = $user;

    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->course_name.' - Course Registration Mail | Innvostem',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.course-registration',
            with: [
                'user' => $this->user,
                'course_title' => $this->course_name,
                
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
