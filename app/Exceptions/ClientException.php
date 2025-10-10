<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class ClientException extends Exception
{
    protected $message = '';
    protected int $statusCode = 400;

    public function __construct() {}

    public function context(): array
    {
        return [];
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'error' => class_basename($this),
            'message' => $this->getMessage(),
        ], $this->statusCode);
    }
}
