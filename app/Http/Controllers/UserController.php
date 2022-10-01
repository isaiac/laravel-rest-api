<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\DestroyBatchRequest;
use App\Http\Requests\User\StoreBatchRequest;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateBatchRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Spatie\Activitylog\Facades\LogBatch;

class UserController extends Controller
{
    /**
     * Create a new UserController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('abilities:superadmin')->only([
            'storeBatch',
            'updateBatch',
            'destroyBatch',
            'updateQuery',
            'destroyQuery'
        ]);
        $this->middleware('abilities:admin');
        $this->middleware('abilities:add-users')->only('store');
        $this->middleware('abilities:edit-users')->only('update');
        $this->middleware('abilities:delete-users')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $users = $this->getCollectionFromQueryString($request, User::class);

        return $this->isQueryParamFalse($request->query('resource', 'true'))
            ? $users
            : UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\User\StoreRequest  $request
     * @return \Dingo\Api\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $user = User::create(Arr::except($request->post(), ['roles', 'permissions']));

        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        if ($request->has('permissions')) {
            $user->syncPermissions($request->permissions);
        }

        return $this->response
            ->array((UserResource::make($user))->toArray($request))
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return \App\Http\Resources\User\UserResource
     */
    public function show(string $id)
    {
        return UserResource::make(User::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\User\UpdateRequest  $request
     * @param  string  $id
     * @return \App\Http\Resources\User\UserResource
     */
    public function update(UpdateRequest $request, string $id)
    {
        $user = User::findOrFail($id);
        $user->update(Arr::except($request->post(), ['roles', 'permissions']));

        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        if ($request->has('permissions')) {
            $user->syncPermissions($request->permissions);
        }

        return UserResource::make($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $id
     * @return \Dingo\Api\Http\Response
     */
    public function destroy(string $id)
    {
        User::findOrFail($id)->delete();

        return $this->response->noContent();
    }

    /**
     * Store newly created resources in storage.
     *
     * @param  \App\Http\Requests\User\StoreBatchRequest  $request
     * @return \Dingo\Api\Http\Response
     */
    public function storeBatch(StoreBatchRequest $request)
    {
        $data = $request->post('data', []);
        $users = [];

        LogBatch::startBatch();

        foreach ($data as $user_data) {
            $user = User::create(Arr::except($user_data, ['roles', 'permissions']));

            if (isset($user_data['roles'])) {
                $user->syncRoles($user_data['roles']);
            }

            if (isset($user_data['permissions'])) {
                $user->syncPermissions($user_data['permissions']);
            }

            $users[] = $user;
        }

        LogBatch::endBatch();

        return $this->response
            ->array(UserResource::collection($users)->toArray($request))
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Update the specified resources in storage.
     *
     * @param  \App\Http\Requests\User\UpdateBatchRequest  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function updateBatch(UpdateBatchRequest $request)
    {
        $data = $request->post('data', []);
        $users = [];

        LogBatch::startBatch();

        foreach ($data as $user_data) {
            $user = User::findOrFail($user_data['id']);
            $user->update(Arr::except($user_data, ['roles', 'permissions']));

            if (isset($user_data['roles'])) {
                $user->syncRoles($user_data['roles']);
            }

            if (isset($user_data['permissions'])) {
                $user->syncPermissions($user_data['permissions']);
            }

            $users[] = $user;
        }

        LogBatch::endBatch();

        return UserResource::collection($users);
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param  \App\Http\Requests\User\DestroyBatchRequest  $request
     * @return \Dingo\Api\Http\Response
     */
    public function destroyBatch(DestroyBatchRequest $request)
    {
        $data = $request->post('data', []);

        LogBatch::startBatch();

        foreach ($data as $user_data) {
            User::findOrFail($user_data['id'])->delete();
        }

        LogBatch::endBatch();

        return $this->response->noContent();
    }

    /**
     * Update the specified resources in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Dingo\Api\Http\Response
     */
    public function updateQuery(Request $request)
    {
        $params = $this->getQueryBuilderParams($request);

        LogBatch::startBatch();

        $this->setCollectionWhere(
            User::query(),
            $params['filters']
        )->update($request->post());

        LogBatch::endBatch();

        return $this->response->noContent();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Dingo\Api\Http\Response
     */
    public function destroyQuery(Request $request)
    {
        $params = $this->getQueryBuilderParams($request);

        LogBatch::startBatch();

        $this->setCollectionWhere(
            User::query(),
            $params['filters']
        )->delete();

        LogBatch::endBatch();

        return $this->response->noContent();
    }
}
