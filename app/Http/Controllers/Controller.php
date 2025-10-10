<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

abstract class Controller
{
    public function sendResponse(
        $data = null,
        int $status = 200,
        array $headers = []
    ): JsonResponse {
        return response()->json($data, $status, $headers);
    }
}
