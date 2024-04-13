<?php

declare(strict_types=1);

namespace App\Repositories\Task;

use App\Dto\Task\StoreTaskDto;
use App\Dto\Task\TaskDto;
use App\Dto\Task\UpdateTaskDto;
use Illuminate\Support\Collection;

interface TaskRepositoryInterface
{
    public function getAllForUser(int $userId): Collection;

    public function getById(int $id): TaskDto;

    public function store(StoreTaskDto $taskDto): TaskDto;

    public function update(int $id, UpdateTaskDto $taskDto): TaskDto;

    public function deleteById(int $id): bool;

    public function exists(int $id): bool;
}
