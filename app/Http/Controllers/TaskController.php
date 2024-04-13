<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Dto\Task\StoreTaskDto;
use App\Dto\Task\UpdateTaskDto;
use App\Http\Requests\Task\StoreRequest;
use App\Http\Requests\Task\UpdateRequest;
use App\Http\Requests\Tasks\DestroyRequest;
use App\Http\Requests\Tasks\ShowRequest;
use App\Http\Resources\TaskResource;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskController extends Controller
{
    public function __construct(
        private readonly TaskService $taskService = new TaskService(),
    ) {}

    public function index(Request $request): JsonResource
    {
        $tasks = $this->taskService->getAllForUser($request->user()->id);

        return TaskResource::collection($tasks);
    }

    public function show(ShowRequest $request, int $id): JsonResource
    {
        $task = $this->taskService->getById($id);

        return new TaskResource($task);
    }

    public function store(StoreRequest $request): JsonResource
    {
        $taskDto = StoreTaskDto::fromRequest($request->user()->id, $request->validated());

        $task = $this->taskService->store($taskDto);

        return new TaskResource($task);
    }

    public function update(UpdateRequest $request, int $id): JsonResource
    {
        $taskDto = UpdateTaskDto::fromRequest($id, $request->user()->id, $request->validated());

        $task = $this->taskService->update($id, $taskDto);

        return new TaskResource($task);
    }

    public function destroy(DestroyRequest $request, int $id): JsonResponse
    {
        if ($this->taskService->deleteById($id)) {
            return response()->json(status: 204);
        }

        return response()->json(status: 400);
    }
}
