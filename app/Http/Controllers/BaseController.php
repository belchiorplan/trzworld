<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class BaseController extends Controller
{
    /**
     * Return a json response of successful
     *
     * @param  string  $message
     * @param  int  $code
     * @return JsonResponse
     */
    protected function sendResponse(string $message, int $code = Response::HTTP_OK): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if (!is_null($message)) {
            $response['message'] = $message;
        }

        return response()->json($response, $code);
    }

    /**
     * Return a json response of error
     *
     * @param  string  $message
     * @param  int  $code
     * @return JsonResponse
     */
    protected function sendError(string $message, int $code = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        return response()->json($response, $code);
    }
}
