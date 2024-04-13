<?php

declare(strict_types=1);

namespace App\Dto\Auth;

final class LoginDto
{
    public function __construct(
        public string $email,
        public string $password,
    ){}

    public static function fromRequest(array $data): LoginDto
    {
        return new LoginDto(
            $data['email'],
            $data['password']
        );
    }
}
