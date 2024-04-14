<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\Task\StoreTaskDto;
use App\Dto\Task\TaskDto;
use App\Dto\Task\UpdateTaskDto;
use App\Events\TaskStatusChanged;
use App\Exceptions\TaskNotFound;
use App\Repositories\Task\TaskEloquentRepository;
use App\Repositories\Task\TaskRepositoryInterface;
use App\Repositories\User\UserEloquentRepository;
use App\Repositories\User\UserRepositoryInterface;

final readonly class TaskService
{
    public function __construct(
        private readonly TaskRepositoryInterface $taskRepository = new TaskEloquentRepository(),
        private readonly UserRepositoryInterface $userRepository = new UserEloquentRepository(),
    ){}

    public function getAllForUser(int $userId): \Illuminate\Support\Collection
    {
        return $this->taskRepository->getAllForUser($userId);
    }

    public function getById(int $id): TaskDto
    {
        if (!$this->taskRepository->exists($id)) {
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
        if (!$this->taskRepository->exists($id)) {
            throw new TaskNotFound();
        }

        $oldStatus = $this->taskRepository->getStatus($id);
        $updatedTask = $this->taskRepository->update($id, $taskDto);

        $user = $this->userRepository->findById($updatedTask->user_id);

        TaskStatusChanged::dispatchIf($oldStatus !== $updatedTask->status, $user);

        return $updatedTask;
    }

    public function deleteById(int $id): bool
    {
        if (!$this->taskRepository->exists($id)) {
            throw new TaskNotFound();
        }

        return $this->taskRepository->deleteById($id);
    }
}
