<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Dto\Task\StoreTaskDto;
use App\Dto\Task\TaskDto;
use App\Dto\Task\UpdateTaskDto;
use App\Http\Requests\Task\StoreRequest;
use App\Http\Requests\Task\UpdateRequest;
use App\Http\Resources\TaskResource;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    public function __construct(
        private readonly TaskService $taskService = new TaskService()
    ) {}

    public function index(): JsonResource
    {
        $tasks = $this->taskService->getAll();

        return TaskResource::collection($tasks);
    }

    public function show(int $id): JsonResource
    {
        $task = $this->taskService->getById($id);

        return new TaskResource($task);
    }

    public function store(StoreRequest $request): JsonResource
    {
        $taskDto = StoreTaskDto::fromRequest($request->validated());

        $task = $this->taskService->store($taskDto);

        return new TaskResource($task);
    }

    public function update(UpdateRequest $request, int $id): JsonResource
    {
        $taskDto = UpdateTaskDto::fromRequest($id, $request->validated());

        $task = $this->taskService->update($id, $taskDto);

        return new TaskResource($task);
    }

    public function destroy(int $id): JsonResponse
    {
        if ($this->taskService->deleteById($id)) {
            return response()->json(status: 204);
        }

        return response()->json(status: 400);
    }
}
