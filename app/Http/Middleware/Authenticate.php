<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponseTrait;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class Authenticate
{
    use ApiResponseTrait;

    private string $appUrl;

    public function __construct()
    {
        $this->appUrl = config('app.web_url');
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $this->authenticate($request, $guards);

        return $next($request);
    }

    /**
     * Determine if the user is logged in to any of the given guards.
     */
    protected function authenticate(Request $request, array $guards): void
    {
        if (empty($guards)) {
            $guards = [null];
        }

        foreach ($guards as $guard) {
            if (auth($guard)->check()) {
                auth()->shouldUse($guard);

                return;
            }
        }

        $this->unauthenticated($request, $guards);
    }

    /**
     * Handle an unauthenticated user.
     */
    protected function unauthenticated(Request $request, array $guards): void
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            throw new AuthenticationException('Unauthenticated.', $guards);
        }

        throw new AuthenticationException(
            'Unauthenticated.',
            $guards,
            $this->redirectTo()
        );
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(): ?string
    {
        return $this->appUrl . '/login';
    }
}

