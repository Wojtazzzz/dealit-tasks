<?php

declare(strict_types=1);

namespace Tests\Feature\Tasks;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StoreTaskTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'name' => 'test_user',
            'email' => 'test@gmail.com',
            'password' => 'test_password',
        ]);
    }

    public function test_create_task(): void
    {
        Sanctum::actingAs($this->user);

        $taskData = [
            'title' => 'My new task',
            'description' => 'Lorem ipsum dolor sit amet',
            'status' => 'open',
        ];

        $response = $this->postJson(route('tasks.store'), $taskData);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'title' => $taskData['title'],
            'description' => $taskData['description'],
            'status' => $taskData['status'],
        ]);

        $this->assertDatabaseCount(Task::class, 1);
        $this->assertDatabaseHas(Task::class, [
            'title' => $taskData['title'],
            'description' => $taskData['description'],
            'status' => $taskData['status'],
            'user_id' => $this->user->id,
        ]);
    }

    public function test_create_task_with_status_open(): void
    {
        Sanctum::actingAs($this->user);

        $taskData = [
            'title' => 'My new task',
            'description' => 'Lorem ipsum dolor sit amet',
            'status' => 'open',
        ];

        $response = $this->postJson(route('tasks.store'), $taskData);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'title' => $taskData['title'],
            'description' => $taskData['description'],
            'status' => $taskData['status'],
        ]);

        $this->assertDatabaseCount(Task::class, 1);
        $this->assertDatabaseHas(Task::class, [
            'title' => $taskData['title'],
            'description' => $taskData['description'],
            'status' => $taskData['status'],
            'user_id' => $this->user->id,
        ]);
    }

    public function test_create_task_with_status_closed(): void
    {
        Sanctum::actingAs($this->user);

        $taskData = [
            'title' => 'My new task',
            'description' => 'Lorem ipsum dolor sit amet',
            'status' => 'closed',
        ];

        $response = $this->postJson(route('tasks.store'), $taskData);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'title' => $taskData['title'],
            'description' => $taskData['description'],
            'status' => $taskData['status'],
        ]);

        $this->assertDatabaseCount(Task::class, 1);
        $this->assertDatabaseHas(Task::class, [
            'title' => $taskData['title'],
            'description' => $taskData['description'],
            'status' => $taskData['status'],
            'user_id' => $this->user->id,
        ]);
    }

    public function test_create_task_with_status_during(): void
    {
        Sanctum::actingAs($this->user);

        $taskData = [
            'title' => 'My new task',
            'description' => 'Lorem ipsum dolor sit amet',
            'status' => 'during',
        ];

        $response = $this->postJson(route('tasks.store'), $taskData);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'title' => $taskData['title'],
            'description' => $taskData['description'],
            'status' => $taskData['status'],
        ]);

        $this->assertDatabaseCount(Task::class, 1);
        $this->assertDatabaseHas(Task::class, [
            'title' => $taskData['title'],
            'description' => $taskData['description'],
            'status' => $taskData['status'],
            'user_id' => $this->user->id,
        ]);
    }

    public function test_cannot_create_task_for_foreign_user(): void
    {
        Sanctum::actingAs($this->user);

        $foreignUser = User::factory()->create();

        $taskData = [
            'title' => 'My new task',
            'description' => 'Lorem ipsum dolor sit amet',
            'status' => 'open',
            'user_id' => $foreignUser->id,
        ];

        $response = $this->postJson(route('tasks.store'), $taskData);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'title' => $taskData['title'],
            'description' => $taskData['description'],
            'status' => $taskData['status'],
        ]);

        $this->assertDatabaseCount(Task::class, 1);
        $this->assertDatabaseHas(Task::class, [
            'title' => $taskData['title'],
            'description' => $taskData['description'],
            'status' => $taskData['status'],
            'user_id' => $this->user->id,
        ]);

        $this->assertDatabaseMissing(Task::class, [
            'title' => $taskData['title'],
            'description' => $taskData['description'],
            'status' => $taskData['status'],
            'user_id' => $foreignUser->id,
        ]);
    }

    public function test_cannot_create_task_with_invalid_status(): void
    {
        Sanctum::actingAs($this->user);

        $taskData = [
            'title' => 'My new task',
            'description' => 'Lorem ipsum dolor sit amet',
            'status' => 'invalid_for_test_purposes',
        ];

        $response = $this->postJson(route('tasks.store'), $taskData);

        $response->assertStatus(422);

        $this->assertDatabaseCount(Task::class, 0);
    }

    public function test_cannot_create_task_without_title(): void
    {
        Sanctum::actingAs($this->user);

        $taskData = [
            'description' => 'Lorem ipsum dolor sit amet',
            'status' => 'invalid_for_test_purposes',
        ];

        $response = $this->postJson(route('tasks.store'), $taskData);

        $response->assertStatus(422);

        $this->assertDatabaseCount(Task::class, 0);
    }

    public function test_cannot_create_task_without_description(): void
    {
        Sanctum::actingAs($this->user);

        $taskData = [
            'title' => 'My new task',
            'status' => 'invalid_for_test_purposes',
        ];

        $response = $this->postJson(route('tasks.store'), $taskData);

        $response->assertStatus(422);

        $this->assertDatabaseCount(Task::class, 0);
    }

    public function test_cannot_create_task_without_status(): void
    {
        Sanctum::actingAs($this->user);

        $taskData = [
            'title' => 'My new task',
            'description' => 'Lorem ipsum dolor sit amet'
        ];

        $response = $this->postJson(route('tasks.store'), $taskData);

        $response->assertStatus(422);

        $this->assertDatabaseCount(Task::class, 0);
    }
}
