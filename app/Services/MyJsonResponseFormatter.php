<?php

namespace App\Services;

class MyJsonResponseFormatter
{   
    private $message;
    private $status_code;
    private $data;

    public static function messageResponse($message, $status_code = 200){
        return response()->json([
            'message' => $message
        ], $status_code);
    }

    public static function dataResponse($data, $message = 'success', $status_code = 200){
        return response()->json([
            'message' => $message,
            'data' => $data
        ], $status_code);
    }
}