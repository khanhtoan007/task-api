<?php

namespace App\Exceptions;

use Exception;
use App\Traits\ApiResponseTrait;

class BusinessException extends Exception
{
    use ApiResponseTrait;

    public function __construct(string $message, int $code = 400)
    {
        parent::__construct($message, $code);
    }
}