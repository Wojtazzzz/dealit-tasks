<?php

declare(strict_types=1);

namespace App\Dto\Auth;

final class RegisterDto
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    ){}

    public static function fromRequest(array $data): RegisterDto
    {
        return new RegisterDto(
            $data['name'],
            $data['email'],
            $data['password']
        );
    }
}
