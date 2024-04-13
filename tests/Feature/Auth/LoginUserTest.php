<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class LoginUserTest extends TestCase
{
    use RefreshDatabase;

    private string $loginRoute;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loginRoute = route('auth.login');

        $this->user = User::factory()->create([
            'name' => 'test_user',
            'email' => 'test@gmail.com',
            'password' => 'test_password',
        ]);
    }

    public function test_login_user(): void
    {
        $response = $this->postJson($this->loginRoute, [
            'email' => $this->user->email,
            'password' => 'test_password',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('access_token', fn (string $token) => !empty($token) && strlen($token) > 10);
    }

    public function test_login_with_invalid_email(): void
    {
        $response = $this->postJson($this->loginRoute, [
            'email' => 'invalid_email@gmail.com',
            'password' => 'test_password',
        ]);

        $response->assertStatus(401);
    }

    public function test_login_with_invalid_password(): void
    {
        $response = $this->postJson($this->loginRoute, [
            'email' => $this->user->email,
            'password' => 'test_invalid_password',
        ]);

        $response->assertStatus(401);
    }

    public function test_not_login_with_name(): void
    {
        $response = $this->postJson($this->loginRoute, [
            'email' => $this->user->name,
            'password' => 'test_password',
        ]);

        $response->assertStatus(422);
    }

    public function test_not_login_without_email(): void
    {
        $response = $this->postJson($this->loginRoute, [
            'password' => 'test_password',
        ]);

        $response->assertStatus(422);
    }

    public function test_not_login_without_password(): void
    {
        $response = $this->postJson($this->loginRoute, [
            'email' => $this->user->email
        ]);

        $response->assertStatus(422);
    }
}
