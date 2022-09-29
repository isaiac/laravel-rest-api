<?php

namespace App\Services;

use App\Mail\Auth\ResetPasswordEmail;
use App\Mail\Auth\VerificationEmail;
use App\Models\User;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;

class AuthService
{
    /**
     * Send a verification email.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public static function sendVerificationEmail(User $user): void
    {
        Mail::to($user)->send(new VerificationEmail($user, static::getJWTFromUser($user)));
    }

    /**
     * Send a reset password email.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public static function sendResetPasswordEmail(User $user): void
    {
        $token = static::getPasswordBroker()->getRepository()->create($user);
        $data = ['token' => $token];

        Mail::to($user)->send(
            new ResetPasswordEmail(
                $user,
                static::getJWTFromUser($user, $data)
            )
        );
    }

    /**
     * Get a JWT via given user.
     *
     * @param  \App\Models\User  $user
     * @param  array  $data
     * @return string
     */
    public static function getJWTFromUser(User $user, $data = []): string
    {
        return JWTAuth::encode(
            JWTFactory::customClaims([
                'sub' => $user->id,
                'email' => $user->email,
                ...$data
            ])->make()
        );
    }

    /**
     * Get the token's payload.
     *
     * @param  string  $token
     * @return array<string, mixed>
     */
    public static function getJWTPayload(string $token): array
    {
        return JWTAuth::setToken($token)
            ->getPayload()
            ->toArray();
    }

    /**
     * Get the password's broker.
     *
     * @return \Illuminate\Auth\Passwords\PasswordBroker
     */
    public static function getPasswordBroker(): PasswordBroker
    {
        return Password::broker();
    }

    /**
     * Get the password reset message.
     *
     * @return string
     */
    public static function getPasswordResetMessage(): string
    {
        return Password::PASSWORD_RESET;
    }
}
