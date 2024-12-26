<?php

namespace App\Helpers;

class ApiResponse
{
    public static function success($data = [], $message = 'Request successful', $status = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $status);
    }

    public static function error($message = 'Request failed', $status = 400, $data = [])
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => $data
        ], $status);
    }
}
