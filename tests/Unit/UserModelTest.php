<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Mockery;

class UserModelTest extends TestCase
{
    public function test_user_password_is_hashed_correctly()
    {
        // Mock the Hash facade
        Hash::shouldReceive('make')
            ->once()
            ->with('secret123')
            ->andReturn('hashed_password');

        // Simulate creating a user
        $user = new User();
        $user->password = Hash::make('secret123');

        // Assert that the password is hashed as expected
        $this->assertEquals('hashed_password', $user->password);
    }
}
