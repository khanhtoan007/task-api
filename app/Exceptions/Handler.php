<?php

namespace App\Exceptions;

use App\Traits\ApiResponseTrait;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

final class Handler extends ExceptionHandler
{
    use ApiResponseTrait;

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e): Response
    {
        if (method_exists($e, 'render')) {
            $render = call_user_func([$e, 'render'], $request);
            if ($render) {
                return $render;
            }
        }

        if ($e instanceof ApiException) {
            return $e->render($request);
        }

        if ($e instanceof BusinessException) {
            return $e->render($request);
        }

        if ($e instanceof AuthException) {
            return $e->render($request);
        }
        
        if ($e instanceof AuthenticationException) {
            return $this->errorResponse('Unauthenticated', 401);
        }

        // Nếu không tạo file exception để custom thì có thể viết nhanh như này - dùng chung format response từ ApiResponseTrait
        if ($e instanceof ModelNotFoundException) {
            return $this->errorResponse('Resource not found.', 404);
        }

        return parent::render($request, $e);
    }
}
