<?php

namespace App\Http\Controllers;

use App\Http\Requests\Permission\DestroyBatchRequest;
use App\Http\Requests\Permission\StoreBatchRequest;
use App\Http\Requests\Permission\StoreRequest;
use App\Http\Requests\Permission\UpdateBatchRequest;
use App\Http\Requests\Permission\UpdateRequest;
use App\Http\Resources\Permission\PermissionResource;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\Activitylog\Facades\LogBatch;

class PermissionController extends Controller
{
    /**
     * Create a new PermissionController instance.
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
        $this->middleware('abilities:add-permissions')->only('store');
        $this->middleware('abilities:edit-permissions')->only('update');
        $this->middleware('abilities:delete-permissions')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $permissions = $this->getCollectionFromQueryString($request, Permission::class);

        return $this->isQueryParamFalse($request->query('resource', 'true'))
            ? $permissions
            : PermissionResource::collection($permissions);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Permission\StoreRequest  $request
     * @return \Dingo\Api\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $permission = Permission::create($request->post());

        return $this->response
            ->array((PermissionResource::make($permission))->toArray($request))
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return \App\Http\Resources\Permission\PermissionResource
     */
    public function show(string $id)
    {
        return PermissionResource::make(Permission::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Permission\UpdateRequest  $request
     * @param  string  $id
     * @return \App\Http\Resources\Permission\PermissionResource
     */
    public function update(UpdateRequest $request, string $id)
    {
        $permission = Permission::findOrFail($id);
        $permission->update($request->post());

        return PermissionResource::make($permission);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $id
     * @return \Dingo\Api\Http\Response
     */
    public function destroy(string $id)
    {
        Permission::findOrFail($id)->delete();

        return $this->response->noContent();
    }

    /**
     * Store newly created resources in storage.
     *
     * @param  \App\Http\Requests\Permission\StoreBatchRequest  $request
     * @return \Dingo\Api\Http\Response
     */
    public function storeBatch(StoreBatchRequest $request)
    {
        $data = $request->post('data', []);
        $permissions = [];

        LogBatch::startBatch();

        foreach ($data as $permission_data) {
            $permission = Permission::create($permission_data);

            $permissions[] = $permission;
        }

        LogBatch::endBatch();

        return $this->response
            ->array(PermissionResource::collection($permissions)->toArray($request))
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Update the specified resources in storage.
     *
     * @param  \App\Http\Requests\Permission\UpdateBatchRequest  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function updateBatch(UpdateBatchRequest $request)
    {
        $data = $request->post('data', []);
        $permissions = [];

        LogBatch::startBatch();

        foreach ($data as $permission_data) {
            $permission = Permission::findOrFail($permission_data['id']);
            $permission->update($permission_data);

            $permissions[] = $permission;
        }

        LogBatch::endBatch();

        return PermissionResource::collection($permissions);
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param  \App\Http\Requests\Permission\DestroyBatchRequest  $request
     * @return \Dingo\Api\Http\Response
     */
    public function destroyBatch(DestroyBatchRequest $request)
    {
        $data = $request->post('data', []);

        LogBatch::startBatch();

        foreach ($data as $permission_data) {
            Permission::findOrFail($permission_data['id'])->delete();
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
            Permission::query(),
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
            Permission::query(),
            $params['filters']
        )->delete();

        LogBatch::endBatch();

        return $this->response->noContent();
    }
}
