<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private function spa(): static
    {
        // Sanctum hanya menyalakan sesi untuk origin yang terdaftar di SANCTUM_STATEFUL_DOMAINS
        return $this->withHeader('Origin', 'http://localhost:5173');
    }

    public function test_login_with_email_succeeds(): void
    {
        $user = User::factory()->create(['email' => 'a@test.id']);

        $this->spa()->postJson('/api/v1/auth/login', ['login' => 'a@test.id', 'password' => 'password'])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.role', 'student');
    }

    public function test_login_with_username_succeeds(): void
    {
        User::factory()->create(['username' => 'budi']);

        $this->spa()->postJson('/api/v1/auth/login', ['login' => 'budi', 'password' => 'password'])
            ->assertOk();
    }

    public function test_login_with_wrong_password_fails(): void
    {
        User::factory()->create(['email' => 'a@test.id']);

        $this->spa()->postJson('/api/v1/auth/login', ['login' => 'a@test.id', 'password' => 'salah'])
            ->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    public function test_inactive_user_cannot_login(): void
    {
        User::factory()->inactive()->create(['email' => 'off@test.id']);

        $this->spa()->postJson('/api/v1/auth/login', ['login' => 'off@test.id', 'password' => 'password'])
            ->assertStatus(422);
    }

    public function test_login_requires_fields(): void
    {
        $this->spa()->postJson('/api/v1/auth/login', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['login', 'password']);
    }

    public function test_me_requires_authentication(): void
    {
        $this->getJson('/api/v1/auth/me')
            ->assertStatus(401)
            ->assertJsonPath('success', false);
    }

    public function test_me_returns_current_user(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*'], 'web');

        $this->getJson('/api/v1/auth/me')->assertOk()->assertJsonPath('data.id', $user->id);
    }

    public function test_logout_succeeds(): void
    {
        Sanctum::actingAs(User::factory()->create(), ['*'], 'web');

        $this->spa()->postJson('/api/v1/auth/logout')->assertOk()->assertJsonPath('success', true);
    }
}