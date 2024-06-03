<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SurveyPersonalAnswer>
 */
class SurveyPersonalAnswerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'employee_id' => null,
            'survey_content_id' => null,
            'department_id' => fake()->numberBetween(1, 16),
            'gender'=> fake()->numberBetween(0, 2),
            'age' => fake()->numberBetween(0, 5),
            'years_of_service' => fake()->numberBetween(0, 8),
            'survey_term_id' => fake()->numberBetween(1, 10),
        ];
    }
}
