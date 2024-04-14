<?php

declare(strict_types=1);

namespace App\Repositories\User;

use App\Dto\Auth\LoginDto;
use App\Dto\Auth\RegisterDto;
use App\Dto\UserDto;
use App\Models\User;

final class UserEloquentRepository implements UserRepositoryInterface
{
    public function register(RegisterDto $registerDto): UserDto
    {
        $user = User::query()->create([
            'name' => $registerDto->name,
            'email' => $registerDto->email,
            'password' => $registerDto->password,
        ]);

        return new UserDto($user->id, $user->name, $user->email);
    }

    public function login(LoginDto $loginDto): UserDto
    {
        // TODO: Implement login() method.
    }

    public function findByEmail(string $email): UserDto
    {
        $user = User::query()->where([
            'email' => $email
        ])
        ->first([
            'id',
            'name',
            'email',
        ]);

        return new UserDto($user->id, $user->name, $user->email);
    }

    public function findById(int $id): UserDto
    {
        $user = User::query()->where([
            'id' => $id
        ])
            ->first([
                'id',
                'name',
                'email',
            ]);

        return new UserDto($user->id, $user->name, $user->email);
    }
}
