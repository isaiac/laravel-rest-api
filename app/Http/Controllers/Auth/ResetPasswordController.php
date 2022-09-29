<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdatePasswordRequest;
use App\Models\User;
use App\Services\AuthService;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class ResetPasswordController extends Controller
{
    /**
     * Verify the user's email.
     *
     * @param  \App\Http\Requests\Auth\UpdatePasswordRequest  $request
     * @return \Dingo\Api\Http\Response
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        try {
            $payload = AuthService::getJWTPayload($request->token);
        } catch (Exception $e) {
            return $this->errorBadToken($e->getMessage());
        }

        $credentials = [
            'email' => $payload['email'],
            'token' => $payload['token'],
            'password' => $request->password,
            'password_confirmation' => $request->password_confirmation
        ];

        $response = AuthService::getPasswordBroker()->reset(
            $credentials,
            function ($user, $password) {
                $user->update(['password' => $password]);
            }
        );

        if ($response !== AuthService::getPasswordResetMessage()) {
            return $this->errorBadToken('Invalid token.');
        }

        return $this->response->noContent();
    }

    /**
     * Get the "bad token" error.
     *
     * @param  string  $error
     * @return \Dingo\Api\Http\Response
     */
    protected function errorBadToken(string $error = '')
    {
        return $this->response
            ->array([
                'status_code' => 422,
                'message' => Response::$statusTexts[422],
                'errors' => [
                    'token' => [
                        $error
                    ]
                ]
            ])
            ->setStatusCode(422);
    }
}
