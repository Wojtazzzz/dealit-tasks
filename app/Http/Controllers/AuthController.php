<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Dto\Auth\LoginDto;
use App\Dto\Auth\RegisterDto;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService = new AuthService()
    ){}

    public function register(RegisterRequest $request): JsonResponse
    {
        $registerDto = RegisterDto::fromRequest($request->validated());

        $userDto = $this->authService->register($registerDto);

        return response()->json([
            'name' => $userDto->name,
            'email' => $userDto->email,
        ]);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $loginDto = LoginDto::fromRequest($request->validated());

        return response()->json([
            'access_token' => $this->authService->login($loginDto),
        ]);
    }
}
