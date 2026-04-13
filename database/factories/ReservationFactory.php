<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = CarbonImmutable::instance(fake()->dateTimeBetween('+1 day', '+2 weeks'))->minute(0);

        return [
            'room_id' => Room::factory(),
            'teacher_id' => User::factory()->teacher(),
            'title' => fake()->randomElement(['Math class', 'Chemistry lab', 'History workshop', 'Team meeting']),
            'notes' => fake()->optional()->sentence(),
            'starts_at' => $start,
            'ends_at' => $start->addHour(),
        ];
    }
}
