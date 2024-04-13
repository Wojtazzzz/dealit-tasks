<?php

declare(strict_types=1);

namespace App\Dto\Task;

use App\Enums\TaskStatus;

final class TaskDto
{
    public function __construct(
        public int|null $id,
        public string $title,
        public string $description,
        public TaskStatus $status,
        public string|null $createdAt,
        public string|null $updatedAt,
    ) {}

    public static function fromUpdateRequest(int $id, array $data): TaskDto
    {
        return new TaskDto(
            $id,
            $data['title'],
            $data['description'],
            $data['status'],
            null,
            null
        );
    }
}
