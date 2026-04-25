<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Enum\PhotoCategory;
use App\Models\Sento;
use App\Models\SentoPhoto;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SentoPhoto>
 */
class SentoPhotoFactory extends Factory
{
    protected $model = SentoPhoto::class;

    public function definition(): array
    {
        return [
            'sento_id' => Sento::factory(),
            'user_id' => User::factory(),
            'review_id' => null,
            'path' => 'sento_photos/' . fake()->uuid() . '.jpg',
            'category' => fake()->randomElement(PhotoCategory::cases())->value,
            'caption' => fake()->sentence(),
        ];
    }
}
