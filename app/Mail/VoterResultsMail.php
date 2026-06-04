<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VoterResultsMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $results, public string $electionTitle) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Election Results — ' . $this->electionTitle);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.voter-results');
    }
}
