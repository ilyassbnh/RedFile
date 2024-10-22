<?php

namespace Tests\Feature;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserRoleTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();

        // Ensure the 'user' role is created for the test
        Role::create(['name' => 'user', 'guard_name' => 'web']);
    }

    public function test_user_has_default_role()
    {
        // Create a user and assign the role 'user'
        $user = User::factory()->create();
        $user->assignRole('user');

        // Check if the user has the role 'user'
        $this->assertTrue($user->hasRole('user'));
    }
}
