<?php

namespace App\Exceptions;

use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\JsonResponse;

final class ApiException extends Exception
{
    use ApiResponseTrait;

    protected $status;

    public function __construct(string $message, int $status)
    {
        parent::__construct($message, $status);
        $this->status = $status;
    }

    public function render($request): JsonResponse
    {
        return $this->errorResponse($this->getMessage(), $this->status);
    }
}
