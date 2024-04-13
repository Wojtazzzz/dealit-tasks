<?php

declare(strict_types=1);

namespace App\Dto\Task;

use App\Enums\TaskStatus;

final class StoreTaskDto
{
    public function __construct(
        public string $title,
        public string $description,
        public TaskStatus $status,
        public int $user_id,
    ) {}

    public static function fromRequest(int $userId, array $data): StoreTaskDto
    {
        return new StoreTaskDto(
            $data['title'],
            $data['description'],
            TaskStatus::from($data['status']),
            $userId
        );
    }
}
