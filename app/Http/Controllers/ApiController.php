<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function ping(Request $request)
    {
        return $this->response->array([
            'name' => config('api.name'),
            'standards_tree' => config('api.standardsTree'),
            'subtype' => config('api.subtype'),
            'version' => config('api.version'),
            'default_format' => config('api.defaultFormat')
        ]);
    }
}
