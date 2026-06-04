<?php

namespace App\Mail;

use App\Models\CandidateAcceptance;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CandidateResultMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public CandidateAcceptance $acceptance) {}

    public function envelope(): Envelope
    {
        $subject = $this->acceptance->won
            ? 'Congratulations — You Won!'
            : 'Election Results Notification';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.candidate-result');
    }
}
