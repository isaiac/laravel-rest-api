<?php

namespace App\Mail\Auth;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    private $user;

    private $token;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, string $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $app_name = config('app.name');

        $verify_url = route(
            'auth.verify',
            ['token' => $this->token]
        );

        $this->subject("[$app_name] Verify your email address");

        return $this->markdown('emails.auth.verification_email', [
            'app_name' => $app_name,
            'user' => $this->user,
            'verify_url' => $verify_url
        ]);
    }
}
