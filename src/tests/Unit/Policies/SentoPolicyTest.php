<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use App\Models\Sento;
use App\Models\User;
use App\Policies\SentoPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SentoPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_sento(): void
    {
        $admin = User::factory()->admin()->create();
        $sento = Sento::factory()->create();
        $this->assertTrue((new SentoPolicy())->update($admin, $sento));
    }

    public function test_member_cannot_update_sento(): void
    {
        $member = User::factory()->create();
        $sento = Sento::factory()->create();
        $this->assertFalse((new SentoPolicy())->update($member, $sento));
    }

    public function test_member_can_propose_sento(): void
    {
        $member = User::factory()->create();
        $sento = Sento::factory()->create();
        $this->assertTrue((new SentoPolicy())->propose($member, $sento));
    }
}
