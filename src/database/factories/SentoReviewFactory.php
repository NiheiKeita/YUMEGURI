<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Sento;
use App\Models\SentoReview;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SentoReview>
 */
class SentoReviewFactory extends Factory
{
    protected $model = SentoReview::class;

    public function definition(): array
    {
        return [
            'sento_id' => Sento::factory(),
            'user_id' => User::factory(),
            'visited_at' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'rating' => fake()->numberBetween(3, 5),
            'body' => fake()->realText(120),
            'has_sauna' => fake()->boolean(),
            'sauna_temp' => fake()->numberBetween(80, 100),
            'has_mizuburo' => fake()->boolean(),
            'mizuburo_temp' => fake()->numberBetween(15, 22),
            'bath_types' => fake()->randomElements(['炭酸泉', '薬湯', 'シルク', '電気風呂', '露天'], 2),
            'want_revisit' => fake()->boolean(70),
            'crowding' => fake()->numberBetween(1, 5),
            'best_time' => '夕方',
        ];
    }
}
