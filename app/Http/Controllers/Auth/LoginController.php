<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LoginController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum')->only('loginAs', 'logout');
        $this->middleware('abilities:superadmin')->only('loginAs');
    }

    /**
     * Get a token via given credentials.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Dingo\Api\Http\Response
     */
    public function login(LoginRequest $request)
    {
        $login = $request->login;

        if ($user = User::where(function ($query) use ($login) {
            $query->where('username', $login)->orWhere('email', $login);
        })->first()) {
            if ($user->isActive()
                && $user->isVerified()
                && $user->checkPassword($request->password)
            ) {
                return $this->respondWithToken(
                    $user->createToken(
                        'access_token',
                        $user->getAbilities()
                    )->plainTextToken
                );
            }
        }

        return $this->response->errorUnauthorized();
    }

    /**
     * Get a token via given user's id.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Dingo\Api\Http\Response
     */
    public function loginAs(Request $request, User $user)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->respondWithToken(
            $user->createToken(
                'access_token',
                $user->getAbilities()
            )->plainTextToken
        );
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Dingo\Api\Http\Response
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->response->noContent();
    }

    /**
     * Get the token array structure.
     *
     * @param  string  $token
     * @return \Dingo\Api\Http\Response
     */
    protected function respondWithToken(string $token)
    {
        return $this->response->array([
            'status_code' => Response::HTTP_OK,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('sanctum.expiration') * 60
        ]);
    }
}
