<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use App\Services\AuthService;
use Exception;
use Illuminate\Http\Response;

class ForgotPasswordController extends Controller
{
    /**
     * Send a reset password email.
     *
     * @param  \App\Http\Requests\Auth\ResetPasswordRequest  $request
     * @return \Dingo\Api\Http\Response
     */
    public function sendResetPasswordEmail(ResetPasswordRequest $request)
    {
        if ($user = User::where('email', $request->email)->first()) {
            try {
                AuthService::sendResetPasswordEmail($user);

                return $this->response->noContent();
            } catch (Exception $e) {
                return $this->errorSendingResetPasswordEmail();
            }
        }
    }

    /**
     * Get the "sending reset password" error.
     *
     * @return \Dingo\Api\Http\Response
     */
    protected function errorSendingResetPasswordEmail()
    {
        $status_code = Response::HTTP_FAILED_DEPENDENCY;

        return $this->response
            ->array([
                'status_code' => $status_code,
                'message' => Response::$statusTexts[$status_code],
                'errors' => 'There was a problem sending you a reset password email. Please, try again later.'
            ])
            ->setStatusCode($status_code);
    }
}
