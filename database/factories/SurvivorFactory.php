<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Survivor>
 */
class SurvivorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'      => fake()->unique()->name(),
            'age'       => fake()->numberBetween(18,80),
            'gender_id' => fake()->numberBetween(1,2),
            'latitude'  => fake()->latitude,
            'longitude' => fake()->longitude,
        ];
    }
}
