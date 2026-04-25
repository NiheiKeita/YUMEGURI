<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Sento;
use App\Models\SentoPhoto;
use App\Models\SentoReview;
use App\Models\User;
use Illuminate\Database\Seeder;

class YumeguriSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::factory()->admin()->create([
            'name' => 'サイト管理者',
            'email' => 'owner@yumeguri.test',
        ]);

        $friends = User::factory(3)->create(['invited_by' => $admin->id]);

        $sentos = Sento::factory(20)->create();

        foreach ([$admin, ...$friends] as $user) {
            $visited = $sentos->random(min(5, $sentos->count()));
            foreach ($visited as $sento) {
                $review = SentoReview::factory()->create([
                    'sento_id' => $sento->id,
                    'user_id' => $user->id,
                ]);
                SentoPhoto::factory(rand(0, 2))->create([
                    'sento_id' => $sento->id,
                    'user_id' => $user->id,
                    'review_id' => $review->id,
                ]);
            }
        }
    }
}
