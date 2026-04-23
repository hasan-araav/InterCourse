<?php

namespace Database\Factories;

use App\Models\Workshop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Workshop>
 */
class WorkshopFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'starts_at' => fake()->dateTimeBetween('+1 week', '+1 month'),
            'duration_minutes' => fake()->randomElement([60, 90, 120, 180]),
            'capacity' => fake()->numberBetween(5, 30),
        ];
    }
}
