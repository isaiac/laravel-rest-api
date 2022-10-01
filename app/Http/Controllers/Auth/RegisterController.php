<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\Me\MeResource;
use App\Models\User;
use App\Services\AuthService;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;

class RegisterController extends Controller
{
    /**
     * Update the authenticated user in storage.
     *
     * @param  \App\Http\Requests\Auth\RegisterRequest  $request
     * @return \Dingo\Api\Http\Response
     */
    public function register(RegisterRequest $request)
    {
        $user = User::create(Arr::except($request->post(), 'status'));

        $user->syncRoles([['id' => 'user']]);

        try {
            AuthService::sendVerificationEmail($user);
        } catch (Exception $e) {
        }

        return $this->response
            ->array((new MeResource($user))->toArray($request))
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
