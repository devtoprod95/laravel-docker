<?php

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

if (!function_exists("apiRes")) {
    function apiRes(int $status, array $params = [], string $message = ""): JsonResponse
    {
        $result = [
            "status" => $status,
            "meta"   => [
                "timestamp" => Carbon::now()->format('Y-m-d H:i:s'),
                "api_type"  => 'apiRes',
            ]
        ];
        if( $status == Response::HTTP_OK ){
            unset($params["isSuccess"]);
            $result = array_merge($result, $params);
            if( trim($message) != "" ){
                $result["message"] = $message;
            }
        }else{
            if( !empty($params['msg']) ){
                $message = $params['msg'];
            }
            $message = empty($message) ? '잘못 된 접근입니다.' : $message;
            $error   = [
                "error" => [
                    "code"    => $status,
                    "message" => trim($message),
                ]
            ];
            if( isset($params["error_code"]) && !empty($params["error_code"]) ){
                $error["error"]["code"] = $params["error_code"];
            }
            if( isset($params["data"]) && !empty($params["data"]) ){
                $error["data"] = $params["data"];
            }
            $result = array_merge($result, $error);
        }

        return response()->json($result, $status);
    }
}

if (!function_exists("helpersDefaultMessage")) {
    function helpersDefaultMessage( $message = "잘못 된 접근입니다.")
    {
        return [
            "isSuccess" => false,
            "msg"       => $message,
        ];
    }
}

if (!function_exists("helpersFailMessage")) {
    function helpersFailMessage($message = "변경 사항이 없거나 처리가 실패하였습니다. 관리자에 문의 바랍니다.")
    {
        return [
            "isSuccess" => false,
            "msg"       => $message,
        ];
    }
}

if (!function_exists("helpersSuccessMessage")) {
    function helpersSuccessMessage($message = "정상 처리 되었습니다.")
    {
        return [
            "isSuccess" => true,
            "msg"       => $message,
        ];
    }
}

if (!function_exists("helpersCustomArrayMessage")) {
    function helpersCustomArrayMessage(bool $isSuccess, array $body)
    {
        $result["isSuccess"] = $isSuccess;
		return array_merge($result, $body);
    }
}

if (!function_exists("routeList")) {
    function routeList(): array
    {
        $routes            = Route::getRoutes();
        $permittedPrefixes = ['admin.', 'settings.', 'dashboard'];
        $filteredRoutes    = [];

        foreach ($routes as $route) {
            $name = $route->getName();
            if ($name && in_array('GET', $route->methods()) && Str::startsWith($name, $permittedPrefixes)) {
                $filteredRoutes[] = [
                    'name' => $name,
                    'uri'  => '/' . $route->uri(),
                ];
            }
        }

        return $filteredRoutes;
    }
}
