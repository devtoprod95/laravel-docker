<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function log(): JsonResponse
    {
        channelLog(
            message: $this->request->all(),
            channel: 'api',
            filename: 'logging',
        );

        return apiRes(200, helpersSuccessMessage());
    }
}
