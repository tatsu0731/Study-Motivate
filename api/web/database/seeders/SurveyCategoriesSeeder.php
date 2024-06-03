<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SurveyCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 2; $i++) {
            DB::table('survey_categories')->insert([
                'company_id' => 1,
                'name' => 'Category ' . ($i + 1), // カテゴリ名に適宜変更が必要です
                'department_id' => null,
                'category' => 0,
                'status' => 1,
                "frequency"=> 6,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ]);
            }
            
        for ($i = 0; $i < 2; $i++) {
            DB::table('survey_categories')->insert([
                'company_id' => 1,
                'name' => 'Category ' . ($i + 1), // カテゴリ名に適宜変更が必要です
                'department_id' => null,
                'category' => 1,
                'status' => 1,
                                "frequency"=> 6,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ]);
            }
    }
}
