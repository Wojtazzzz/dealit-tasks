<?php

declare(strict_types=1);

namespace Tests\Feature\Tasks;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeleteTaskTest extends TestCase
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

    public function test_delete_task(): void
    {
        Sanctum::actingAs($this->user);

        $this->assertDatabaseCount(Task::class, 1);

        $response = $this->deleteJson(route('tasks.destroy', $this->task));

        $response->assertStatus(204);

        $this->assertDatabaseCount(Task::class, 0);
    }

    public function test_cannot_delete_foreign_user_task(): void
    {
        Sanctum::actingAs($this->user);

        $foreignUser = User::factory()->create();
        $foreignTask = Task::factory()->create([
            'user_id' => $foreignUser->id,
        ]);

        $this->assertDatabaseCount(Task::class, 2);

        $response = $this->deleteJson(route('tasks.destroy', $foreignTask));

        $response->assertStatus(404);

        $this->assertDatabaseCount(Task::class, 2);
    }

    public function test_cannot_delete_task_as_unauthorized(): void
    {
        $this->assertDatabaseCount(Task::class, 1);

        $response = $this->deleteJson(route('tasks.destroy', $this->task));

        $response->assertStatus(401);

        $this->assertDatabaseCount(Task::class, 1);
    }
}
