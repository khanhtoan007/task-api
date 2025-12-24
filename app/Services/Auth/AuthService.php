<?php

namespace App\Services\Auth;

use App\Exceptions\ApiException;
use App\Exceptions\AuthException;
use App\Models\RefreshToken;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

final class AuthService
{
    public function login(string $email, string $password): array
    {
        $user = User::where('email', $email)->first();
        if (! $user || ! Hash::check($password, $user->password)) {
            throw new AuthException('Invalid credentials', 401);
        }
        $access_token = JWTAuth::fromUser($user);
        $refresh_token = $this->createRefreshToken($user);

        return [
            'access_token' => $access_token,
            'refresh_token' => $refresh_token->plain_token,
            'token_type' => 'Bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ];
    }

    public function register(string $name, string $email, string $password): User
    {
        return User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);
    }

    public function refresh(string $refreshToken): array
    {
        $hashedToken = hash('sha256', $refreshToken);
        $token = RefreshToken::where('token', $hashedToken)->first();
        if (! $token || $token->revoked) {
            throw new ApiException('Invalid refresh token', 400);
        }

        if ($token->expires_at->isPast()) {
            throw new ApiException('Invalid refresh token', 400);
        }
        $user = $token->user;
        $token->update(['revoked' => true]);
        $newRefreshToken = $this->createRefreshToken($user);
        $accessToken = JWTAuth::fromUser($user);

        return [
            'access_token' => $accessToken,
            'refresh_token' => $newRefreshToken->plain_token,
            'token_type' => 'Bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ];
    }

    public function logout(string $refreshToken): void
    {
        $hashedToken = hash('sha256', $refreshToken);
        $token = RefreshToken::where('token', $hashedToken)->first();

        if ($token) {
            $token->update(['revoked' => true]);
        }
    }

    private function createRefreshToken(User $user): RefreshToken
    {
        $plainToken = Str::random(64);
        $hashedToken = hash('sha256', $plainToken);
        $refreshToken = RefreshToken::create([
            'user_id' => $user->id,
            'token' => $hashedToken,
            'expires_at' => Carbon::now()->addDays(10),
        ]);
        $refreshToken->plain_token = $plainToken;

        return $refreshToken;
    }
}
