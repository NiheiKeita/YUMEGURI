<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use App\Models\SentoPhoto;
use App\Models\User;
use App\Policies\SentoPhotoPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SentoPhotoPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_delete(): void
    {
        $user = User::factory()->create();
        $photo = SentoPhoto::factory()->create(['user_id' => $user->id]);
        $this->assertTrue((new SentoPhotoPolicy())->delete($user, $photo));
    }

    public function test_admin_can_delete_any_photo(): void
    {
        $admin = User::factory()->admin()->create();
        $photo = SentoPhoto::factory()->create();
        $this->assertTrue((new SentoPhotoPolicy())->delete($admin, $photo));
    }

    public function test_other_user_cannot_delete(): void
    {
        $user = User::factory()->create();
        $photo = SentoPhoto::factory()->create();
        $this->assertFalse((new SentoPhotoPolicy())->delete($user, $photo));
    }
}
