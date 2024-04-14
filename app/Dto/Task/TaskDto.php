<?php

declare(strict_types=1);

namespace App\Dto\Task;

use App\Enums\TaskStatus;

final class TaskDto
{
    public function __construct(
        public int $id,
        public string $title,
        public string $description,
        public TaskStatus $status,
        public string $createdAt,
        public string $updatedAt,
    ) {}
}
