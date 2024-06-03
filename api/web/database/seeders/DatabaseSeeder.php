<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\CompanySeeder;
use Database\Seeders\SurveyQuestionSeeder;
use Database\Seeders\SurveyQuestionCategorySeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SurveyQuestionCategorySeeder::class,
            SurveyQuestionSeeder::class,
            CompanySeeder::class,
            AdminSeeder::class,
        ]);
    }
}
