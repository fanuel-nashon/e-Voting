<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VoterCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $voterName,
        public string $email,
        public string $plainPassword,
        public string $faculty,
        public string $program,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your Voter Account Has Been Approved');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.voter-credentials');
    }
}
