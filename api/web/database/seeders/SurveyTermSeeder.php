<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SurveyTermSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('survey_terms')->insert([
            'survey_category_id' => 1, // 仮の値、必要に応じて変更してください
            'start_date' => Carbon::today()->toDateString(),
            'deadline' => 7, // 仮の値、必要に応じて変更してください
            'count' => 0, // 仮の値、必要に応じて変更してください
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'deleted_at' => null,
        ]);

        // 2つ目のダミーデータ: start_date+deadlineが今日の日付になる
        $startDate = Carbon::today();
        $deadline = 5; // 仮の値、必要に応じて変更してください
        $endDate = $startDate->copy()->addDays($deadline);

        DB::table('survey_terms')->insert([
            'survey_category_id' => 2, // 仮の値、必要に応じて変更してください
            'start_date' => $startDate->toDateString(),
            'deadline' => $deadline,
            'count' => 0, // 仮の値、必要に応じて変更してください
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'deleted_at' => null,
        ]);
    }
}
