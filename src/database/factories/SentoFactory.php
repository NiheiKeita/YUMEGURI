<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Enum\Prefecture;
use App\Domain\Enum\SentoStatus;
use App\Models\Sento;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sento>
 */
class SentoFactory extends Factory
{
    protected $model = Sento::class;

    public function definition(): array
    {
        $prefecture = fake()->randomElement(Prefecture::cases())->jaName();
        return [
            'name' => fake()->company() . '湯',
            'name_kana' => 'ゆめぐりゆ',
            'prefecture' => $prefecture,
            'city' => fake()->city(),
            'address' => $prefecture . fake()->streetAddress(),
            'lat' => fake()->randomFloat(7, 35.5, 35.9),
            'lng' => fake()->randomFloat(7, 139.4, 140.0),
            'phone' => fake()->phoneNumber(),
            'hours' => '15:00-23:00',
            'closed_days' => '月曜日',
            'price' => 520,
            'source_url' => fake()->url(),
            'nearest_station' => fake()->city() . '駅',
            'walk_minutes' => fake()->numberBetween(1, 15),
            'has_shampoo' => fake()->boolean(),
            'has_soap' => fake()->boolean(),
            'status' => SentoStatus::Open->value,
            'info_updated_at' => now()->toDateString(),
            'is_manually_updated' => false,
        ];
    }
}
