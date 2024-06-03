<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SurveyCategory>
 */
class SurveyCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => fake()->numberBetween(1, 2),
            'name' => fake()->word(),
            'department_id' => fake()->numberBetween(1, 16),
            'category' => fake()->numberBetween(0, 1),
            'status' => fake()->numberBetween(0, 2),
            'frequency' => fake()->randomElement([3, 6]),
        ];
    }
}
