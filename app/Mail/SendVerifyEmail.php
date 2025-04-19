<?php

declare(strict_types = 1);

namespace App\Mail;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\{Config, URL};

class SendVerifyEmail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(private User $user)
    {

    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmação de cadastro',
        );
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): mixed
    {
        return $this->markdown('emails.sendVerifyEmail', [
            'user' => $this->user,
            'link' => $this->getUrl(),
        ]);
    }

    public function getUrl(): string
    {
        $expire = Config::get('auth.verification.expire', 48);

        $expiration = is_int($expire) || is_string($expire) || is_float($expire)
            ? (int) $expire
            : 48;

        $url = URL::temporarySignedRoute(
            'users.verify',
            Carbon::now()->addHours($expiration),
            [
                'id' => $this->user->getKey(),
                'hash' => sha1($this->user->getEmailForVerification()),
            ]
        );

        return str_replace('api/v1/users/verify', 'verificar-email', $url);
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
