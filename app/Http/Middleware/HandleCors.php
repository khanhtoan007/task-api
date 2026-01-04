<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class HandleCors
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $origin = $request->headers->get('Origin');
        $corsConfig = config('cors');

        $allowedOrigins = $corsConfig['allowed_origins'] ?? [];
        $allowedPatterns = $corsConfig['allowed_origins_patterns'] ?? [];
        $isAllowed = false;

        if ($origin) {
            if (in_array($origin, $allowedOrigins, true)) {
                $isAllowed = true;
            } else {
                foreach ($allowedPatterns as $pattern) {
                    if (preg_match($pattern, $origin)) {
                        $isAllowed = true;
                        break;
                    }
                }
            }
        }

        if ($request->getMethod() === 'OPTIONS') {
            $response = response('', 200);
        } else {
            $response = $next($request);
        }

        if ($isAllowed && $origin) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
        }

        $response->headers->set('Access-Control-Allow-Methods', implode(', ', $corsConfig['allowed_methods'] ?? ['*']));
        $response->headers->set('Access-Control-Allow-Headers', implode(', ', $corsConfig['allowed_headers'] ?? ['*']));
        $response->headers->set('Access-Control-Allow-Credentials', $corsConfig['supports_credentials'] ? 'true' : 'false');

        if (isset($corsConfig['max_age']) && $corsConfig['max_age'] > 0) {
            $response->headers->set('Access-Control-Max-Age', (string) $corsConfig['max_age']);
        }

        if (! empty($corsConfig['exposed_headers'])) {
            $response->headers->set('Access-Control-Expose-Headers', implode(', ', $corsConfig['exposed_headers']));
        }

        return $response;
    }
}

