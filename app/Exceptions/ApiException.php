<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
final class ApiException extends Exception
{
    protected $status;

    public function __construct(string $message, int $status)
    {
        parrent::__construct($message, $status);
        $this->status = $status;
    }

    public function render($request): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
            'data' => null,
        ]);
    }
}
