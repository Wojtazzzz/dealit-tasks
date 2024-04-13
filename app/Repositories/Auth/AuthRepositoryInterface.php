<?php

declare(strict_types=1);

namespace App\Repositories\Auth;

use App\Dto\UserDto;

interface AuthRepositoryInterface
{
    public function createAccessToken(string $email): string;
}
