<?php

declare(strict_types=1);

namespace App\Dto\Task;

use App\Enums\TaskStatus;

final class UpdateTaskDto
{
    public function __construct(
        public int $id,
        public string $title,
        public string $description,
        public TaskStatus $status,
    ) {}

    public static function fromRequest(int $id, array $data): UpdateTaskDto
    {
        return new UpdateTaskDto(
            $id,
            $data['title'],
            $data['description'],
            TaskStatus::from($data['status']),
        );
    }
}
