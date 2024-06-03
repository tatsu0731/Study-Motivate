<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SurveyTerm>
 */
class SurveyTermFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $sequence = 0;

        // count をインクリメントして利用する
        $sequence++;

        return [
            'survey_category_id' => null,
            'start_date' => fake()->date(),
            'deadline' => fake()->numberBetween(7, 14),
            'count' => $sequence,
        ];
    }
}
