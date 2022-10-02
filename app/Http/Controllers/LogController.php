<?php

namespace App\Http\Controllers;

use App\Http\Requests\Log\DestroyBatchRequest;
use App\Http\Resources\Log\LogResource;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity as Log;

class LogController extends Controller
{
    /**
     * Create a new LogController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('abilities:superadmin');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $logs = $this->getCollectionFromQueryString($request, Log::class);

        return $this->isQueryParamFalse($request->query('resource', 'true'))
            ? $logs
            : LogResource::collection($logs);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \App\Http\Resources\Log\LogResource
     */
    public function show(int $id)
    {
        return LogResource::make(Log::findOrFail($id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Dingo\Api\Http\Response
     */
    public function destroy(int $id)
    {
        Log::findOrFail($id)->delete();

        return $this->response->noContent();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param  \App\Http\Requests\Log\DestroyBatchRequest  $request
     * @return \Dingo\Api\Http\Response
     */
    public function destroyBatch(DestroyBatchRequest $request)
    {
        $data = $request->post('data', []);

        foreach ($data as $log_data) {
            Log::findOrFail($log_data['id'])->delete();
        }

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

        $this->setCollectionWhere(
            Log::query(),
            $params['filters']
        )->delete();

        return $this->response->noContent();
    }
}
