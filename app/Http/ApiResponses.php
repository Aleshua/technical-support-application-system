<?php

namespace App\Http;

use Illuminate\Http\JsonResponse;

class ApiResponses
{
    public static function message(string $message, int $status): JsonResponse
    {
        return response()->json([
            'message' => $message,
        ], $status);
    }

    public static function errors(string $message, int $status, array $errors): JsonResponse
    {
        $response = [];

        if ($message !== '') {
            $response['message'] = $message;
        }

        $response['errors'] = $errors;

        return response()->json($response, $status);
    }

    public static function data(string $message, int $status, array $data): JsonResponse
    {
        $response = [];

        if ($message !== '') {
            $response['message'] = $message;
        }

        $response['data'] = $data;

        return response()->json($response, $status);
    }
}
