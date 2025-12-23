<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class Handler extends ExceptionHandler
{
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

        return parent::render($request, $e);
    }
}