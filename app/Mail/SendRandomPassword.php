<?php

declare(strict_types = 1);

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class SendRandomPassword extends Mailable {
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(private User $user, private string $password) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope {
        return new Envelope(
            subject: 'Registration in the SP 1.0 System',
        );
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): mixed {
        return $this->markdown('emails.sendRandomPassword', [
            'user' => $this->user,
            'password' => $this->password,
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
