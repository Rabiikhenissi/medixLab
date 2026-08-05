<?php

namespace App\Mail;

use App\Models\Invite;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InviteMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $acceptUrl;

    public function __construct(public readonly Invite $invite)
    {
        $this->acceptUrl = route('invite.accept', $this->invite->token);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Vous êtes invité(e) à rejoindre Medix eSanté',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invite',
            with: [
                'url' => $this->acceptUrl,
            ],
        );
    }
}
