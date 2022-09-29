<?php

namespace App\Mail\Auth;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordEmail extends Mailable
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
        $update_password_url = route('auth.updatePassword');
        $reset_password_url = route('auth.sendResetPasswordEmail');

        $this->subject("[$app_name] Reset your password");

        return $this->markdown('emails.auth.reset_password', [
            'app_name' => $app_name,
            'user' => $this->user,
            'token' => $this->token,
            'update_password_url' => $update_password_url,
            'reset_password_url' => $reset_password_url,
        ]);
    }
}
