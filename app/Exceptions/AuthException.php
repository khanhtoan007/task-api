<?php

namespace App\Exceptions;

use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\JsonResponse;

final class AuthException extends Exception
{
    use ApiResponseTrait;

    public function __construct(string $message, int $code = 401)
    {
        parent::__construct($message, $code);
    }

    public function render($request): JsonResponse
    {
        return $this->errorResponse('Authenticate Exception: Unauthorized', $this->getCode(), $this->getMessage());
    }
}
