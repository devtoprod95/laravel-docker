<?php

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use Illuminate\Http\UploadedFile;

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

if (!function_exists("channelLog")) {
    /**
     * 커스텀 채널 로거
     *
     * 지정된 채널과 파일명으로 로그를 기록하며, 실행 시간과 호출 위치 정보를 자동으로 추가합니다.
     * Laravel의 daily 드라이버를 사용하여 날짜별로 로그 파일을 생성합니다.
     *
     * @param Throwable|string|array $message 로그 인스턴스
     * @param string $channel 로그 채널명 (폴더명으로도 사용됨)
     * @param string|null $filename 로그 파일명 (null일 경우 채널명 사용, 실제 파일은 filename-yyyy-mm-dd.log 형태로 저장)
     * @param string $level 로그 레벨 (emergency, alert, critical, error, warning, notice, info, debug)
     * @param int $days 로그 파일 보관 일수 (기본: 30일)
     *
     * @example channelLog('시작', 'apple') → logs/apple/apple-2025-06-02.log
     * @example channelLog('에러', 'apple', 'error_log', 'error') → logs/apple/error_log-2025-06-02.log
     * @example channelLog('디버그', 'apple', null, 'debug') → logs/apple/apple-2025-06-02.log
     *
     * @return void
     */
    function channelLog(Throwable|string|array $message, string $channel, ?string $filename = null, string $level = 'info', int $days = 30)
    {
        // 파일명이 없으면 오늘 날짜를 파일명으로 사용
        $driver = 'daily';
        if (!$filename) {
            $logFilename = 'app';
        } else {
            $logFilename = $filename;
        }

        // 채널명을 파일명에 따라 고유하게 생성
        $uniqueChannelName = $channel . '_' . $logFilename;

        // 채널 구성이 없으면 생성
        if (!config()->has('logging.channels.'.$uniqueChannelName)) {
            config(['logging.channels.'.$uniqueChannelName => [
                'driver'     => $driver,
                'path'       => storage_path("logs/{$channel}/{$logFilename}.log"),
                'level'      => env('LOG_LEVEL', 'debug'),
                'days'       => $days,
                'permission' => 0777,
            ]]);
        }

        // Exception 객체인지 확인해서 위치 정보 추출
        if ($message instanceof Throwable) {
            // Exception 객체인 경우 실제 에러 발생 위치 사용
            $runFile     = basename($message->getFile());
            $location    = "{$runFile}:{$message->getLine()}";
            $messageText = $message->getMessage();
        } else {
            // 일반 문자열 또는 배열인 경우 호출한 위치 사용
            $backtrace   = debug_backtrace();
            $caller      = $backtrace[0];
            $runFile     = basename($caller['file']);
            $location    = "{$runFile}:{$caller['line']}";

            // 배열인 경우 JSON으로 인코딩
            if (is_array($message)) {
                $messageText = json_encode($message, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            } else {
                $messageText = $message;
            }
        }

        // 메시지에 추가 정보 붙이기
        $prefixedMessage = sprintf(
            "[location: %s] %s",
            $location,
            $messageText
        );

        // 고유한 채널명으로 로거 가져오기
        $logger = \Illuminate\Support\Facades\Log::channel($uniqueChannelName);

        // 유효한 로그 레벨인지 확인
        $validLevels = ['emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug'];
        if (!in_array(strtolower($level), $validLevels)) {
            $level = 'info'; // 기본값으로 설정
        }

        // 로깅 실행
        $logger->{$level}($prefixedMessage);
    }
}

if (!function_exists('helperCurl')) {
    /**
     * HTTP 요청 처리
     *
     * @param string $method (GET, POST, PUT, DELETE, PATCH)
     * @param string $url
     * @param array|string $header 요청 헤더
     * @param mixed $data 요청 바디 데이터 (배열 또는 JSON 문자열)
     * @param bool $status HTTP 상태코드 포함 여부
     * @param bool $includeHeader 응답 헤더 포함 여부
     * @return array
     */
    function helperCurl(
        string $method,
        string $url,
        array $header        = [],
        mixed $data          = '',
        bool  $status        = false,
        bool  $includeHeader = false
    ) {
        $client = new Client([
            'timeout'         => 30,
            'connect_timeout' => 10,
            'http_errors'     => false,
            'verify'          => env('APP_ENV') === 'production',   // 개발환경에서는 SSL 검증 스킵
        ]);

        $options = [
            'headers' => $header,
        ];

        if (!empty($data)) {
            if (is_array($data)) {
                $options['json'] = $data;
            } else {
                // JSON 문자열인 경우
                $options['body'] = $data;
            }
        }

        // HTTP 요청 실행
        $response        = $client->request(strtoupper($method), $url, $options);
        $statusCode      = $response->getStatusCode();
        $body            = $response->getBody()->getContents();
        $body            = json_decode($body, true);
        $responseHeaders = $response->getHeaders();

        // 반환값 구성
        $result = [];

        if ($includeHeader) {
            $result['headers'] = $responseHeaders;
        }

        $result['body'] = $body;

        if ($status) {
            $result['status'] = $statusCode;
        }

        // 단순 바디만 반환하는 경우
        if (!$status && !$includeHeader) {
            return $body;
        }

        return $result;
    }
}

if (!function_exists('onlyNumber')) {
    function onlyNumber(mixed $value): int
    {
        if (is_numeric($value)) {
            return (int) $value;
        }

        if (is_string($value)) {
            $cleaned = preg_replace('/[^0-9]/', '', $value);
            return $cleaned !== '' ? (int) $cleaned : 0;
        }

        return 0;
    }
};

if (!function_exists('saveTempFile')) {
        /**
     * 임시 파일 저장
     */
    function saveTempFile(UploadedFile $file): string
    {
        $tmpDir = storage_path('app/tmp');

        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0777, true);
        }

        $fileName    = $file->getClientOriginalName();
        $extension   = $file->getClientOriginalExtension();
        $datePart    = date('Ymd_His');
        $uniquePart  = bin2hex(random_bytes(4));
        $tmpFileName = sprintf('%s_%s_%s.%s', pathinfo($fileName, PATHINFO_FILENAME), $datePart, $uniquePart, $extension);
        $tmpPath     = $tmpDir . '/' . $tmpFileName;

        if (!$file->move($tmpDir, $tmpFileName)) {
            throw new Exception('임시 저장 실패');
        }

        return $tmpPath;
    }
}
