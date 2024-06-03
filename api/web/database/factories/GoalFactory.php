<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Goal>
 */
class GoalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'goal' => fake()->randomFloat(1, 1, 5),
            'survey_term_id' => fake()->numberBetween(1, 7),
            'survey_question_id' => fake()->numberBetween(1, 32),
        ];
    }
}
