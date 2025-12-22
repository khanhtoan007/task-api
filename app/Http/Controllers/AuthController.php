<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

final readonly class AuthController
{
    public function register(Request $request)
    {
        return response()->json(['message' => 'Hello, World!']);
    }
}
