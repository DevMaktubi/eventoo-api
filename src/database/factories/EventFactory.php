<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "name" => fake()->sentence(3),
            "description" => fake()->paragraph(),
            "event_date" => fake()->dateTimeBetween('+1 week', '+1 year'),
            "available_seats" => fake()->numberBetween(1, 100),
        ];
    }
}
