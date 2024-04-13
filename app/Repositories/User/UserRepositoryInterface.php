<?php


declare(strict_types=1);

namespace App\Repositories\User;

use App\Dto\Auth\LoginDto;
use App\Dto\Auth\RegisterDto;
use App\Dto\UserDto;
use App\Models\User;

interface UserRepositoryInterface
{
    public function register(RegisterDto $registerDto): UserDto;

    public function login(LoginDto $loginDto): UserDto;

    public function findByEmail(string $email): UserDto;
}
