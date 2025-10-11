<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;

class HttpClientException extends ServerException
{
    public function __construct(string $message, ?string $code = null)
    {
        parent::__construct($message);
        $this->code = $code;
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'error' => class_basename($this),
            'message' => $this->getMessage(),
            'code' => $this->code,
        ], $this->statusCode);
    }
}
