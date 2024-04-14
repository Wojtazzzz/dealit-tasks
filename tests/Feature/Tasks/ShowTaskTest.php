<?php

declare(strict_types=1);

namespace Tests\Feature\Tasks;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ShowTaskTest extends TestCase
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

    public function test_get_task(): void
    {
        Sanctum::actingAs($this->user);

        $task = Task::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson(route('tasks.show', [
            'task' => $task,
        ]));

        $response->assertStatus(200);
        $response->assertExactJson([
            'data' => [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'status' => $task->status,
                'createdAt' => $task->created_at->format('Y-m-d H:i:s'),
                'updatedAt' => $task->updated_at->format('Y-m-d H:i:s'),
            ],
        ]);
    }

    public function test_get_correct_task(): void
    {
        Sanctum::actingAs($this->user);

        $task = Task::factory()->create([
            'title' => 'Incorrect task title',
            'user_id' => $this->user->id,
        ]);

        $anotherTask = Task::factory()->create([
            'title' => 'Correct task title',
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson(route('tasks.show', [
            'task' => $anotherTask,
        ]));

        $response->assertStatus(200);
        $response->assertExactJson([
            'data' => [
                'id' => $anotherTask->id,
                'title' => $anotherTask->title,
                'description' => $anotherTask->description,
                'status' => $anotherTask->status,
                'createdAt' => $anotherTask->created_at->format('Y-m-d H:i:s'),
                'updatedAt' => $anotherTask->updated_at->format('Y-m-d H:i:s'),
            ],
        ]);
    }

    public function test_cannot_get_task_as_unauthorized(): void
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson(route('tasks.show', [
            'task' => $task,
        ]));

        $response->assertStatus(401);
    }

    public function test_cannot_get_foreign_users_task(): void
    {
        Sanctum::actingAs($this->user);

        $foreign_user = User::factory()->create([
            'name' => 'test_foreign_user',
            'email' => 'test_foreign@gmail.com',
            'password' => 'test_foreign_password',
        ]);

        $task = Task::factory()->create([
            'user_id' => $foreign_user->id,
        ]);

        $response = $this->getJson(route('tasks.show', [
            'task' => $task
        ]));

        $response->assertStatus(404);

        Sanctum::actingAs($foreign_user);

        $response = $this->getJson(route('tasks.show', [
            'task' => $task
        ]));

        $response->assertStatus(200);
    }
}
