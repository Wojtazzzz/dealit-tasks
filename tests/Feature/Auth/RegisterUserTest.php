<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterUserTest extends TestCase
{
    use RefreshDatabase;

    private string $registerRoute;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registerRoute = route('auth.register');
    }

    public function test_register_user(): void
    {
        $response = $this->postJson($this->registerRoute, [
            'name' => 'test_user',
            'email' => 'jane.doe@gmail.com',
            'password' => 'test1234',
        ]);

        $response->assertStatus(200);
    }

    public function test_not_register_user_with_short_password(): void
    {
        $response = $this->postJson($this->registerRoute, [
            'name' => 'test_user',
            'email' => 'jane.doe@gmail.com',
            'password' => 'test1',
        ]);

        $response->assertStatus(422);
    }

    public function test_not_register_user_with_invalid_email(): void
    {
        $response = $this->postJson($this->registerRoute, [
            'name' => 'test_user',
            'email' => 'jane.doegmail.com',
            'password' => 'test1',
        ]);

        $response->assertStatus(422);
    }

    public function test_not_register_user_without_name(): void
    {
        $response = $this->postJson($this->registerRoute, [
            'email' => 'jane.doegmail.com',
            'password' => 'test1',
        ]);

        $response->assertStatus(422);
    }

    public function test_not_register_user_without_email(): void
    {
        $response = $this->postJson($this->registerRoute, [
            'name' => 'test_user',
            'password' => 'test1',
        ]);

        $response->assertStatus(422);
    }

    public function test_not_register_user_without_password(): void
    {
        $response = $this->postJson($this->registerRoute, [
            'name' => 'test_user',
            'email' => 'jane.doegmail.com',
        ]);

        $response->assertStatus(422);
    }
}
