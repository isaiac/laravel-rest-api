<?php

namespace App\Http\Controllers;

use App\Http\Requests\Role\DestroyBatchRequest;
use App\Http\Requests\Role\StoreBatchRequest;
use App\Http\Requests\Role\StoreRequest;
use App\Http\Requests\Role\UpdateBatchRequest;
use App\Http\Requests\Role\UpdateRequest;
use App\Http\Resources\Role\RoleResource;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Spatie\Activitylog\Facades\LogBatch;

class RoleController extends Controller
{
    /**
     * Create a new RoleController instance.
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
        $this->middleware('abilities:add-roles')->only('store');
        $this->middleware('abilities:edit-roles')->only('update');
        $this->middleware('abilities:delete-roles')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $roles = $this->getCollectionFromQueryString($request, Role::class);

        return $this->isQueryParamFalse($request->query('resource', 'true'))
            ? $roles
            : RoleResource::collection($roles);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Role\StoreRequest  $request
     * @return \Dingo\Api\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $role = Role::create(Arr::except($request->post(), 'permissions'));

        $permissions = $request->post('permissions');

        if (isset($permissions)) {
            $role->syncPermissions($permissions);
        }

        return $this->response
            ->array((RoleResource::make($role))->toArray($request))
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return \App\Http\Resources\Role\RoleResource
     */
    public function show(string $id)
    {
        return RoleResource::make(Role::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Role\UpdateRequest  $request
     * @param  string  $id
     * @return \App\Http\Resources\Role\RoleResource
     */
    public function update(UpdateRequest $request, string $id)
    {
        $role = Role::findOrFail($id);
        $role->update(Arr::except($request->post(), 'permissions'));

        $permissions = $request->post('permissions');

        if (isset($permissions)) {
            $role->syncPermissions($permissions);
        }

        return RoleResource::make($role);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $id
     * @return \Dingo\Api\Http\Response
     */
    public function destroy(string $id)
    {
        Role::findOrFail($id)->delete();

        return $this->response->noContent();
    }

    /**
     * Store newly created resources in storage.
     *
     * @param  \App\Http\Requests\Role\StoreBatchRequest  $request
     * @return \Dingo\Api\Http\Response
     */
    public function storeBatch(StoreBatchRequest $request)
    {
        $data = $request->post('data', []);
        $roles = [];

        LogBatch::startBatch();

        foreach ($data as $role_data) {
            $role = Role::create(Arr::except($role_data, 'permissions'));

            if (isset($role_data['permissions'])) {
                $role->syncPermissions($role_data['permissions']);
            }

            $roles[] = $role;
        }

        LogBatch::endBatch();

        return $this->response
            ->array(RoleResource::collection($roles)->toArray($request))
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Update the specified resources in storage.
     *
     * @param  \App\Http\Requests\Role\UpdateBatchRequest  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function updateBatch(UpdateBatchRequest $request)
    {
        $data = $request->post('data', []);
        $roles = [];

        LogBatch::startBatch();

        foreach ($data as $role_data) {
            $role = Role::findOrFail($role_data['id']);
            $role->update(Arr::except($role_data, 'permissions'));

            if (isset($role_data['permissions'])) {
                $role->syncPermissions($role_data['permissions']);
            }

            $roles[] = $role;
        }

        LogBatch::endBatch();

        return RoleResource::collection($roles);
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param  \App\Http\Requests\Role\DestroyBatchRequest  $request
     * @return \Dingo\Api\Http\Response
     */
    public function destroyBatch(DestroyBatchRequest $request)
    {
        $data = $request->post('data', []);

        LogBatch::startBatch();

        foreach ($data as $role_data) {
            Role::findOrFail($role_data['id'])->delete();
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
            Role::query(),
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
            Role::query(),
            $params['filters']
        )->delete();

        LogBatch::endBatch();

        return $this->response->noContent();
    }
}
