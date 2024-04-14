<?php

declare(strict_types=1);

namespace App\Repositories\Task;

use App\Dto\Task\StoreTaskDto;
use App\Dto\Task\TaskDto;
use App\Dto\Task\UpdateTaskDto;
use App\Enums\TaskStatus;
use App\Events\TaskStatusChanged;
use App\Models\Task;
use Illuminate\Support\Collection;

final class TaskEloquentRepository implements TaskRepositoryInterface
{
    public function getAllForUser(int $userId): Collection
    {
        $tasks = Task::query()
            ->where([
                'user_id' => $userId
            ])
            ->get();

        return $tasks->map(fn (Task $task) => new TaskDto(
            $task->id,
            $task->title,
            $task->description,
            $task->status,
            $task->user_id,
            (string) $task->created_at,
            (string) $task->updated_at,
        ));
    }

    public function getById(int $id): TaskDto
    {
        $task = Task::query()->find($id);

        return new TaskDto(
            $task->id,
            $task->title,
            $task->description,
            $task->status,
            $task->user_id,
            (string) $task->created_at,
            (string) $task->updated_at,
        );
    }

    public function store(StoreTaskDto $taskDto): TaskDto
    {
        $taskEntity = Task::query()->create([
            'title' => $taskDto->title,
            'description' => $taskDto->description,
            'status' => $taskDto->status,
            'user_id' => $taskDto->user_id,
        ]);

        return $this->getById($taskEntity->id);
    }

    public function update(int $id, UpdateTaskDto $taskDto): TaskDto
    {
        Task::query()
            ->where([
                'id' => $id
            ])
            ->update([
                'title' => $taskDto->title,
                'description' => $taskDto->description,
                'status' => $taskDto->status,
            ]);

        return $this->getById($id);
    }

    public function deleteById(int $id): bool
    {
        return (bool) Task::query()
            ->where([
                'id' => $id
            ])
            ->delete($id);
    }

    public function exists(int $id): bool
    {
        return Task::query()
            ->where([
                'id' => $id
            ])
            ->exists();
    }

    public function getStatus(int $id): TaskStatus
    {
        return Task::query()
            ->where([
                'id' => $id
            ])
            ->value('status');
    }
}
