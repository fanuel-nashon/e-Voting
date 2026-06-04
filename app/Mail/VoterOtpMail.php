<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VoterOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $otp, public string $voterName) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your One-Time Password (OTP) — e-Voting');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.voter-otp');
    }
}
