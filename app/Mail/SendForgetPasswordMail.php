<?php

declare(strict_types = 1);

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class SendForgetPasswordMail extends Mailable {
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        private string $token,
        private User $user
    ) {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope {
        return new Envelope(
            subject: 'Password Recovery Request SP System 1.0',
        );
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): mixed {
        return $this->markdown('emails.password', [
            'name' => $this->user->name,
            'link' => url('/') . '/resetar-senha?token= ' . $this->token . '&email=' . $this->user->email,
        ]);
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array {
        return [];
    }
}
