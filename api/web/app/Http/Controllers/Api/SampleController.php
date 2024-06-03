<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Http\Controllers\Controller;

class SampleController extends Controller
{
    public function test()
    {
        return response()->json([
            'message' => 'Hello World!',
        ]);
    }
}
