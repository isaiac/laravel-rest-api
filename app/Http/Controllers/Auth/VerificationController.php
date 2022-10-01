<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\VerificationRequest;
use App\Http\Requests\Auth\VerifyRequest;
use App\Models\User;
use App\Services\AuthService;
use Exception;
use Illuminate\Http\Response;

class VerificationController extends Controller
{
    /**
     * Send a verification email.
     *
     * @param  \App\Http\Requests\Auth\VerificationRequest  $request
     * @return \Dingo\Api\Http\Response
     */
    public function sendVerificationEmail(VerificationRequest $request)
    {
        if ($user = User::where('email', $request->email)->first()) {
            if (! $user->isVerified()) {
                try {
                    AuthService::sendVerificationEmail($user);

                    return $this->response->noContent();
                } catch (Exception $e) {
                    return $this->errorSendingVerificationEmail();
                }
            }
        }

        return $this->errorEmailIsAlreadyVerified();
    }

    /**
     * Verify the user's email.
     *
     * @param  \App\Http\Requests\Auth\VerifyRequest  $request
     * @return \Dingo\Api\Http\Response
     */
    public function verify(VerifyRequest $request)
    {
        try {
            $payload = AuthService::getJWTPayload($request->token);
        } catch (Exception $e) {
            return $this->errorBadToken($e->getMessage());
        }

        if ($user = User::where('email', $payload['email'])->first()) {
            if (! $user->isVerified()) {
                $user->email_verified_at = now();
                $user->save();

                return $this->response->noContent();
            }
        }

        return $this->errorEmailIsAlreadyVerified();
    }

    /**
     * Get the "email is already verified" error.
     *
     * @return \Dingo\Api\Http\Response
     */
    protected function errorEmailIsAlreadyVerified()
    {
        $status_code = Response::HTTP_UNPROCESSABLE_ENTITY;

        return $this->response
            ->array([
                'status_code' => $status_code,
                'message' => Response::$statusTexts[$status_code],
                'errors' => [
                    'email' => [
                        'The email is already verified.'
                    ]
                ]
            ])
            ->setStatusCode($status_code);
    }

    /**
     * Get the "bad token" error.
     *
     * @param  string  $error
     * @return \Dingo\Api\Http\Response
     */
    protected function errorBadToken(string $error = '')
    {
        $status_code = Response::HTTP_UNPROCESSABLE_ENTITY;

        return $this->response
            ->array([
                'status_code' => $status_code,
                'message' => Response::$statusTexts[$status_code],
                'errors' => [
                    'token' => [
                        $error
                    ]
                ]
            ])
            ->setStatusCode($status_code);
    }

    /**
     * Get the "sending verification email" error.
     *
     * @return \Dingo\Api\Http\Response
     */
    protected function errorSendingVerificationEmail()
    {
        $status_code = Response::HTTP_FAILED_DEPENDENCY;

        return $this->response
            ->array([
                'status_code' => $status_code,
                'message' => Response::$statusTexts[$status_code],
                'errors' => 'There was a problem sending you a verification email. Please, try again later.'
            ])
            ->setStatusCode($status_code);
    }
}
