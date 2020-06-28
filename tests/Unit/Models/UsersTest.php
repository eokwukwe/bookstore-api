<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\User;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UsersTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function a_users_ID_is_a_UUID_instead_of_an_integer()
    {
        $user = factory(User::class)->create();
        $this->assertFalse(is_integer($user->id));
        $this->assertEquals(36, strlen($user->id));
    }

    /**
     * @test
     */
    public function it_has_a_role_of_user_by_default()
    {
        $user = factory(User::class)->create();
        $this->assertEquals('user', $user->role);
    }
}
