<?php

declare(strict_types=1);

namespace App\Repositories\Auth;

use App\Dto\UserDto;
use App\Models\User;

final class AuthSanctumRepository implements AuthRepositoryInterface
{
    public function createAccessToken(string $email): string
    {
        $userEntity = User::query()->firstWhere([
            'email' => $email
        ]);

        return $userEntity->createToken($email.'-AuthToken')->plainTextToken;
    }
}
