<?php

namespace Database\Factories;

use App\Models\Building;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'building_id' => Building::factory(),
            'name' => 'Room '.fake()->unique()->bothify('##?'),
            'code' => fake()->unique()->bothify('R-###'),
            'capacity' => fake()->numberBetween(10, 45),
        ];
    }
}
