<?php

declare(strict_types=1);

namespace App\Dto;

final class UserDto
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
    ) {}
}
