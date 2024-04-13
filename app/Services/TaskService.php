<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\Task\StoreTaskDto;
use App\Dto\Task\TaskDto;
use App\Dto\Task\UpdateTaskDto;
use App\Exceptions\TaskNotFound;
use App\Repositories\Task\TaskEloquentRepository;
use App\Repositories\Task\TaskRepositoryInterface;

final readonly class TaskService
{
    public function __construct(
        private readonly TaskRepositoryInterface $taskRepository = new TaskEloquentRepository()
    ){}

    public function getAll(): \Illuminate\Support\Collection
    {
        return $this->taskRepository->getAll();
    }

    public function getById(int $id): TaskDto
    {
        if (!$this->taskRepository->existsById($id)) {
            throw new TaskNotFound();
        }

        return $this->taskRepository->getById($id);
    }

    public function store(StoreTaskDto $taskDto): TaskDto
    {
        return $this->taskRepository->store($taskDto);
    }

    public function update(int $id, UpdateTaskDto $taskDto): TaskDto
    {
        if (!$this->taskRepository->existsById($id)) {
            throw new TaskNotFound();
        }

        return $this->taskRepository->update($id, $taskDto);
    }

    public function deleteById(int $id): bool
    {
        if (!$this->taskRepository->existsById($id)) {
            throw new TaskNotFound();
        }

        return $this->taskRepository->deleteById($id);
    }
}
