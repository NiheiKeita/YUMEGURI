<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_factory_marks_role_correctly(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->create();

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($member->isAdmin());
    }

    public function test_invited_by_relationship(): void
    {
        $owner = User::factory()->admin()->create();
        $friend = User::factory()->create(['invited_by' => $owner->id]);

        $this->assertSame($owner->id, $friend->inviter->id);
    }
}
