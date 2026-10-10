<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINTS = [
        'student' => 'student',
        'teacher' => 'teacher',
        'parent' => 'parent',
        'admin' => 'admin',
    ];

    public function test_each_role_can_only_access_its_own_dashboard(): void
    {
        foreach (Role::cases() as $role) {
            foreach (self::ENDPOINTS as $roleValue => $area) {
                $this->refreshApplication();
                $this->artisan('migrate:fresh');

                Sanctum::actingAs(User::factory()->withRole($role)->create(), ['*'], 'web');

                $expected = $role->value === $roleValue ? 200 : 403;

                $this->getJson("/api/v1/{$area}/dashboard")
                    ->assertStatus($expected);
            }
        }
    }

    public function test_guest_cannot_access_any_dashboard(): void
    {
        foreach (self::ENDPOINTS as $area) {
            $this->getJson("/api/v1/{$area}/dashboard")->assertStatus(401);
        }
    }

    public function test_inactive_user_is_denied_even_with_valid_session(): void
    {
        Sanctum::actingAs(User::factory()->inactive()->withRole(Role::Admin)->create(), ['*'], 'web');

        $this->getJson('/api/v1/admin/dashboard')->assertStatus(403);
    }
}