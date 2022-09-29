<?php

namespace App\Http\Controllers;

use App\Http\Requests\Me\UpdateRequest;
use App\Http\Resources\Me\MeResource;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class MeController extends Controller
{
    /**
     * Create a new MeController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Http\Resources\Me\MeResource
     */
    public function show(Request $request)
    {
        return MeResource::make($request->user());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Me\UpdateRequest  $request
     * @return \App\Http\Resources\Me\MeResource
     */
    public function update(UpdateRequest $request)
    {
        $me = $request->user();
        $me->update(Arr::except($request->post(), 'status'));

        return MeResource::make($me);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Dingo\Api\Http\Response
     */
    public function destroy(Request $request)
    {
        $request->user()->delete();

        return $this->response->noContent();
    }
}
