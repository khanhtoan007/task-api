<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RefreshTokenRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\ApiResponseResource;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final readonly class AuthController
{
    public function __construct(private AuthService $authService) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $data = $this->authService->login($request->email, $request->password);

        return (new ApiResponseResource([
            'success' => true,
            'message' => 'Login successful',
            'data' => $data,
        ]))->response();
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $this->authService->register(
            $request->name,
            $request->email,
            $request->password
        );

        return (new ApiResponseResource([
            'success' => true,
            'message' => 'Registration successful',
            'data' => $data,
        ]))->response();
    }

    public function me(Request $request): JsonResponse
    {
        return (new ApiResponseResource([
            'success' => true,
            'message' => 'User info fetched',
            'data' => $request->user(),
        ]))->response();
    }

    public function refresh(RefreshTokenRequest $request): JsonResponse
    {
        $data = $this->authService->refresh($request->refresh_token);

        return (new ApiResponseResource([
            'success' => true,
            'message' => 'Token refreshed successfully',
            'data' => $data,
        ]))->response();
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->refresh_token);

        return (new ApiResponseResource([
            'success' => true,
            'message' => 'Successfully logged out',
            'data' => null,
        ]))->response();
    }
}
