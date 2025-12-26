<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RefreshTokenRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final readonly class AuthController
{
    use ApiResponseTrait;

    public function __construct(private AuthService $authService) {}

    /**
     * @OA\Post(
     *   path="/api/auth/login",
     *   tags={"Auth"},
     *   @OA\Response(response=200, description="OK")
     * )
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $data = $this->authService->login($request->email, $request->password);

        return $this->successResponse(
            data: $data,
            message: 'Login successful'
        );
    }

    /**
     * @OA\Post(
     *   path="/api/auth/register",
     *   tags={"Auth"},
     *   @OA\Response(response=200, description="OK")
     * )
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $this->authService->register(
            $request->name,
            $request->email,
            $request->password
        );

        return $this->successResponse(
            data: $data,
            message: 'Registration successful'
        );
    }

    /**
     * @OA\Get(
     *   path="/api/auth/me",
     *   tags={"Auth"},
     *   @OA\Response(response=200, description="OK")
     * )
     */
    public function me(Request $request): JsonResponse
    {
        return $this->successResponse(
            data: $request->user(),
            message: 'User info fetched'
        );
    }

    /**
     * @OA\Post(
     *   path="/api/auth/refresh",
     *   tags={"Auth"},
     *    @OA\Response(response=200, description="OK")
     * )
     */
    public function refresh(RefreshTokenRequest $request): JsonResponse
    {
        $data = $this->authService->refresh($request->refresh_token);

        return $this->successResponse(
            data: $data,
            message: 'Token refreshed successfully'
        );
    }

    /**
     * @OA\Post(
     *   path="/api/auth/logout",
     *   tags={"Auth"},
     *    @OA\Response(response=200, description="OK")
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->refresh_token);

        return $this->successResponse(
            data: null,
            message: 'Successfully logged out'
        );
    }
}
