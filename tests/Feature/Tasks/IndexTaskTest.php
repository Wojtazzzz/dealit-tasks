<?php

declare(strict_types=1);

namespace Tests\Feature\Tasks;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class IndexTaskTest extends TestCase
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

    public function test_get_tasks(): void
    {
        Sanctum::actingAs($this->user);

        $firstTask = Task::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $secondTask = Task::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson(route('tasks.index'));

        $response->assertStatus(200);
        $response->assertExactJson([
            'data' => [
                [
                    'id' => $firstTask->id,
                    'title' => $firstTask->title,
                    'description' => $firstTask->description,
                    'status' => $firstTask->status,
                    'createdAt' => $firstTask->created_at->format('Y-m-d H:i:s'),
                    'updatedAt' => $firstTask->updated_at->format('Y-m-d H:i:s'),
                ],
                [
                    'id' => $secondTask->id,
                    'title' => $secondTask->title,
                    'description' => $secondTask->description,
                    'status' => $secondTask->status,
                    'createdAt' => $secondTask->created_at->format('Y-m-d H:i:s'),
                    'updatedAt' => $secondTask->updated_at->format('Y-m-d H:i:s'),
                ]
            ],
        ]);
    }

    public function test_cannot_get_tasks_as_unauthorized(): void
    {
        Task::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson(route('tasks.index'));

        $response->assertStatus(401);
    }

    public function test_cannot_get_foreign_users_tasks(): void
    {
        Sanctum::actingAs($this->user);

        $foreignUser = User::factory()->create([
            'name' => 'test_foreign_user',
            'email' => 'test_foreign@gmail.com',
            'password' => 'test_foreign_password',
        ]);

        $task = Task::factory()->create([
            'user_id' => $foreignUser->id,
        ]);

        $response = $this->getJson(route('tasks.index'));

        $response->assertStatus(200);
        $response->assertExactJson([
            'data' => []
        ]);

        Sanctum::actingAs($foreignUser);

        $response = $this->getJson(route('tasks.index', [
            'task' => $task
        ]));

        $response->assertStatus(200);
        $response->assertExactJson([
            'data' => [
                [
                    'id' => $task->id,
                    'title' => $task->title,
                    'description' => $task->description,
                    'status' => $task->status,
                    'createdAt' => $task->created_at->format('Y-m-d H:i:s'),
                    'updatedAt' => $task->updated_at->format('Y-m-d H:i:s'),
                ],
            ]
        ]);
    }
}
