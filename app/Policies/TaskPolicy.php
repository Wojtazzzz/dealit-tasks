<?php

declare(strict_types=1);

namespace App\Policies;

use App\Exceptions\TaskNotFound;
use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    public function show(User $user, Task $task): Response
    {
        return $user->id === $task->user_id
            ? Response::allow()
            : throw new TaskNotFound();
    }

    public function update(User $user, Task $task): Response
    {
        return $user->id === $task->user_id
            ? Response::allow()
            : throw new TaskNotFound();
    }

    public function delete(User $user, Task $task): Response
    {
        return $user->id === $task->user_id
            ? Response::allow()
            : throw new TaskNotFound();
    }

}
