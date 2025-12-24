<?php

namespace App\Exceptions;

use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\JsonResponse;

final class BusinessException extends Exception
{
    use ApiResponseTrait;

    public function __construct(string $message, int $code = 422)
    {
        parent::__construct($message, $code);
    }

    public function render($request): JsonResponse
    {
        return $this->errorResponse('Violate business rules', $this->getCode());
    }
}
