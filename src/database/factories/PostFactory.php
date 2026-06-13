<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(4),
            'place_name' => fake()->optional()->city(),
            'lat' => fake()->optional()->randomFloat(7, 35.5, 35.9),
            'lng' => fake()->optional()->randomFloat(7, 139.4, 140.0),
            'visited_at' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'published_at' => now(),
        ];
    }

    public function draft(): static
    {
        return $this->state(['published_at' => null]);
    }
}
