<?php

declare(strict_types=1);

namespace Tests\Feature\Tasks;

use App\Mail\TaskStatusChanged;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateTaskTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Task $task;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'name' => 'test_user',
            'email' => 'test@gmail.com',
            'password' => 'test_password',
        ]);

        $this->task = Task::factory()->create([
            'title' => 'My custom task',
            'description' => 'Lorem ipsum dolor sit amet consectetur.',
            'status' => 'open',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_update_task(): void
    {
        Sanctum::actingAs($this->user);

        $updateData = [
            'title' => 'Updated title',
            'description' => 'Updated description',
            'status' => 'closed',
        ];

        $response = $this->putJson(route('tasks.update', $this->task), $updateData);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'title' => $updateData['title'],
            'description' => $updateData['description'],
            'status' => $updateData['status'],
        ]);

        $this->assertDatabaseCount(Task::class, 1);
        $this->assertDatabaseHas(Task::class, [
            'title' => $updateData['title'],
            'description' => $updateData['description'],
            'status' => $updateData['status'],
            'user_id' => $this->user->id,
        ]);
    }

    public function test_update_task_status_send_notification(): void
    {
        Sanctum::actingAs($this->user);

        $updateData = [
            'title' => 'Updated title',
            'description' => 'Updated description',
            'status' => 'closed',
        ];

        $response = $this->putJson(route('tasks.update', $this->task), $updateData);

        $response->assertStatus(200);

        Mail::assertNothingSent();
        Mail::assertQueued(TaskStatusChanged::class);
        Mail::assertQueuedCount(1);
    }

    public function test_update_task_without_update_status_not_send_notification(): void
    {
        Sanctum::actingAs($this->user);

        $updateData = [
            'title' => 'Updated title',
            'description' => 'Updated description',
            'status' => 'open',
        ];

        $response = $this->putJson(route('tasks.update', $this->task), $updateData);

        $response->assertStatus(200);

        Mail::assertNothingSent();
        Mail::assertNothingQueued();
    }

    public function test_update_task_statuses(): void
    {
        Sanctum::actingAs($this->user);

        $updateData = [
            'title' => 'Updated title',
            'description' => 'Updated description',
        ];

        /* From 'open' to 'during' */
        $response = $this->putJson(route('tasks.update', $this->task), [
            ...$updateData,
            'status' => 'during',
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'title' => $updateData['title'],
            'description' => $updateData['description'],
            'status' => 'during',
        ]);

        $this->assertDatabaseCount(Task::class, 1);
        $this->assertDatabaseHas(Task::class, [
            'title' => $updateData['title'],
            'description' => $updateData['description'],
            'status' => 'during',
            'user_id' => $this->user->id,
        ]);

        /* From 'during' to 'closed' */
        $response = $this->putJson(route('tasks.update', $this->task), [
            ...$updateData,
            'status' => 'closed',
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'title' => $updateData['title'],
            'description' => $updateData['description'],
            'status' => 'closed',
        ]);

        $this->assertDatabaseCount(Task::class, 1);
        $this->assertDatabaseHas(Task::class, [
            'title' => $updateData['title'],
            'description' => $updateData['description'],
            'status' => 'closed',
            'user_id' => $this->user->id,
        ]);

        /* From 'closed' to 'open' */
        $response = $this->putJson(route('tasks.update', $this->task), [
            ...$updateData,
            'status' => 'open',
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'title' => $updateData['title'],
            'description' => $updateData['description'],
            'status' => 'open',
        ]);

        $this->assertDatabaseCount(Task::class, 1);
        $this->assertDatabaseHas(Task::class, [
            'title' => $updateData['title'],
            'description' => $updateData['description'],
            'status' => 'open',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_cannot_update_task_for_foreign_user(): void
    {
        $foreignUser = User::factory()->create();

        $foreignTask = Task::factory()->create([
            'title' => 'Foreign task',
            'description' => 'Lorem ipsum dolor sit amet consectetur.',
            'status' => 'open',
            'user_id' => $foreignUser->id,
        ]);

        Sanctum::actingAs($this->user);

        $updateData = [
            'title' => 'My new task',
            'description' => 'Lorem ipsum dolor sit amet',
            'status' => 'open',
            'user_id' => $foreignUser->id,
        ];

        $response = $this->putJson(route('tasks.update', $foreignTask), $updateData);

        $response->assertStatus(404);

        $this->assertDatabaseCount(Task::class, 2);
        $this->assertDatabaseHas(Task::class, [
            'title' => $foreignTask->title,
            'description' => $foreignTask->description,
            'status' => $foreignTask->status,
            'user_id' => $foreignUser->id,
        ]);

        $this->assertDatabaseMissing(Task::class, [
            'title' => $updateData['title'],
            'description' => $updateData['description'],
            'status' => $updateData['status'],
        ]);
    }

    public function test_cannot_update_task_as_unauthorized(): void
    {
        $updateData = [
            'title' => 'Updated title',
            'description' => 'Updated description',
            'status' => 'closed',
        ];

        $response = $this->putJson(route('tasks.update', $this->task), $updateData);

        $response->assertStatus(401);

        $this->assertDatabaseCount(Task::class, 1);
        $this->assertDatabaseMissing(Task::class, [
            'title' => $updateData['title'],
            'description' => $updateData['description'],
            'status' => $updateData['status'],
            'user_id' => $this->user->id,
        ]);
    }

    public function test_cannot_update_task_with_invalid_status(): void
    {
        Sanctum::actingAs($this->user);

        $taskData = [
            'title' => 'My new task',
            'description' => 'Lorem ipsum dolor sit amet',
            'status' => 'invalid_for_test_purposes',
        ];

        $response = $this->putJson(route('tasks.update', $this->task), $taskData);

        $response->assertStatus(422);

        $this->assertDatabaseCount(Task::class, 1);
    }

    public function test_cannot_update_task_without_title(): void
    {
        Sanctum::actingAs($this->user);

        $updateData = [
            'description' => 'Updated description',
            'status' => 'closed',
        ];

        $response = $this->putJson(route('tasks.update', $this->task), $updateData);

        $response->assertStatus(422);

        $this->assertDatabaseCount(Task::class, 1);
        $this->assertDatabaseHas(Task::class, [
            'title' => $this->task->title,
            'description' => $this->task->description,
            'status' => $this->task->status,
            'user_id' => $this->user->id,
        ]);
    }

    public function test_cannot_update_task_without_description(): void
    {
        Sanctum::actingAs($this->user);

        $updateData = [
            'title' => 'Updated title',
            'status' => 'closed',
        ];

        $response = $this->putJson(route('tasks.update', $this->task), $updateData);

        $response->assertStatus(422);

        $this->assertDatabaseCount(Task::class, 1);
        $this->assertDatabaseHas(Task::class, [
            'title' => $this->task->title,
            'description' => $this->task->description,
            'status' => $this->task->status,
            'user_id' => $this->user->id,
        ]);
    }

    public function test_cannot_update_task_without_status(): void
    {
        Sanctum::actingAs($this->user);

        $updateData = [
            'title' => 'Updated title',
            'description' => 'Updated description',
        ];

        $response = $this->putJson(route('tasks.update', $this->task), $updateData);

        $response->assertStatus(422);

        $this->assertDatabaseCount(Task::class, 1);
        $this->assertDatabaseHas(Task::class, [
            'title' => $this->task->title,
            'description' => $this->task->description,
            'status' => $this->task->status,
            'user_id' => $this->user->id,
        ]);
    }
}
