<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\Auth\LoginDto;
use App\Dto\Auth\RegisterDto;
use App\Dto\UserDto;
use App\Exceptions\Auth\InvalidLoginCredentials;
use App\Models\User;
use App\Repositories\Auth\AuthSanctumRepository;
use App\Repositories\User\UserEloquentRepository;
use Illuminate\Support\Facades\Auth;

final readonly class AuthService
{
    public function __construct(
        private UserEloquentRepository $userRepository = new UserEloquentRepository(),
        private AuthSanctumRepository  $authRepository = new AuthSanctumRepository(),
    ){}

    public function register(RegisterDto $registerDto): \App\Dto\UserDto
    {
        return $this->userRepository->register($registerDto);
    }

    public function login(LoginDto $loginDto): string
    {
        $attempt = Auth::attempt([
            'email' => $loginDto->email,
            'password' => $loginDto->password,
        ]);

        if (!$attempt) {
            throw new InvalidLoginCredentials();
        }

        return $this->authRepository->createAccessToken($loginDto->email);
    }
}
