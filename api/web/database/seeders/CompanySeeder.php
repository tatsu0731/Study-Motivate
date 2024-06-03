<?php

namespace Database\Seeders;

use App\Models\Goal;
use App\Models\Plan;
use App\Models\Admin;
use App\Models\Report;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Department;
use App\Models\SurveyTerm;
use Illuminate\Support\Arr;
use App\Models\SurveyContent;
use App\Models\SurveyCategory;
use App\Models\SurveyQuestion;
use Illuminate\Database\Seeder;
use App\Models\SurveyMainAnswer;
use App\Models\SurveyMonthlyAnswer;
use App\Models\SurveyPersonalAnswer;
use App\Models\SurveyDescriptionAnswer;


class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::factory()->count(2)->create();
        $companies->each(function ($company) {
            $company->departments()->saveMany(Department::factory()->count(8)->make([
                'company_id' => $company->id,
            ]));
            $company->employees()->saveMany(Employee::factory()->count(50)->make([
                'company_id' => $company->id,
            ]));
            $company->admins()->saveMany(Admin::factory()->count(5)->make([
                'company_id' => $company->id,
            ]));

            $start_year = 2021; // 開始年
            $start_month = 1;   // 開始月
            $end_year = 2024;   // 終了年
            $end_month = 6;     // 終了月

            $monthly_start_date = [];

            $current_year = $start_year;
            $current_month = $start_month;

            while ($current_year < $end_year || ($current_year == $end_year && $current_month <= $end_month)) {
                $date = sprintf('%d-%02d-01', $current_year, $current_month);
                $monthly_start_date[] = $date;

                // 次の月へ移動
                $current_month++;
                if ($current_month > 12) {
                    $current_month = 1;
                    $current_year++;
                }
            }

            $survey_category_main = SurveyCategory::factory()->create([
                'department_id' => null,
                'company_id' => $company->id,
                'category' => 0,
                'frequency' => 6,
            ]);
            $survey_category_monthly = SurveyCategory::factory()->create([
                'department_id' => null,
                'company_id' => $company->id,
                'category' => 1,
                'frequency' => 1,
            ]);
            for ($i = 1; $i < 33; $i++) {
                $survey_content = SurveyContent::factory()->create([
                    'survey_category_id' => $survey_category_main->id,
                    'survey_question_id' => $i,
                ]);
            };
            for ($i = 33; $i < 36; $i++) {
                $survey_content = SurveyContent::factory()->create([
                    'survey_category_id' => $survey_category_monthly->id,
                    'survey_question_id' => $i,
                ]);
            };

            $employee_ids = Employee::where('company_id', $survey_category_main->company_id)->pluck('id')->toArray();
            $department_ids = Department::where('company_id', $survey_category_main->company_id)->pluck('id')->toArray();
            $max_index = count($employee_ids) - 1;

            foreach ($monthly_start_date as $key => $date) {
                if ($key % 6 === 0) {
                    $survey_term = SurveyTerm::factory()->create([
                        'survey_category_id' => $survey_category_main->id,
                        'start_date' => $date,
                        'count' => $key / 6 + 1,
                    ]);
                    $survey_term->plan()->save(Plan::factory()->make([
                        'survey_term_id' => $survey_term->id,
                    ]));
                    $survey_term->reports()->saveMany(Report::factory()->count(3)->make([
                        'survey_term_id' => $survey_term->id,
                    ]));
                    $survey_term->goals()->save(Goal::factory()->make([
                        'survey_term_id' => $survey_term->id,
                    ]));

                    $survey_content_ids_main = SurveyContent::where('survey_category_id', $survey_category_main->id)->pluck('id')->toArray();
                    $survey_content_ids_monthly = SurveyContent::where('survey_category_id', $survey_category_monthly->id)->pluck('id')->toArray();
                    $survey_content_ids = array_merge($survey_content_ids_main, $survey_content_ids_monthly);
                    for ($i = 1; $i < 36; $i++) {
                        foreach (range(0, 49) as $index) {
                            $employee_id = $employee_ids[$index % $max_index];
                            $survey_personal_answer = SurveyPersonalAnswer::factory()->create([
                                'survey_content_id' => $survey_content_ids[$i - 1],
                                'employee_id' => $employee_id,
                                'department_id' => Arr::random($department_ids),
                                'survey_term_id' => $survey_term->id,
                            ]);

                            if ($i === 33) {
                                SurveyMainAnswer::factory()->create([
                                    'survey_personal_answer_id' => $survey_personal_answer->id,
                                    'answer' => mt_rand(0, 10),
                                ]);
                            } else if ($i === 34 || $i === 35) {
                                SurveyMonthlyAnswer::factory()->create([
                                    'survey_personal_answer_id' => $survey_personal_answer->id,
                                ]);
                            } else {
                                SurveyMainAnswer::factory()->create([
                                    'survey_personal_answer_id' => $survey_personal_answer->id,
                                ]);
                            }
                        }
                    }
                } else if ($key % 6 === 1) {
                    $survey_category_additional = SurveyCategory::factory()->create([
                        'department_id' => rand(0, 1) === 0 ? null : Arr::random($company->departments->pluck('id')->toArray()),
                        'company_id' => $company->id,
                        'category' => 1,
                        'frequency' => 1,
                    ]);
                    $survey_term = SurveyTerm::factory()->create([
                        'survey_category_id' => $survey_category_additional->id,
                        'start_date' => $date,
                        'count' => 1,
                    ]);
                    for ($i = 33; $i < 41; $i++) {
                        if ($i >= 36) {
                            $survey_question = SurveyQuestion::factory()->create([
                                'survey_question_category_id' => 19,
                            ]);
                            $survey_content = SurveyContent::factory()->create([
                                'survey_category_id' => $survey_category_additional->id,
                                'survey_question_id' => $survey_question->id,
                            ]);
                        } else {
                            $survey_content = SurveyContent::factory()->create([
                                'survey_category_id' => $survey_category_additional->id,
                                'survey_question_id' => $i,
                            ]);
                        }
                        $employee_ids = Employee::where('company_id', $company->id)->pluck('id')->toArray();
                        $department_ids = Department::where('company_id', $company->id)->pluck('id')->toArray();
                        $max_index = count($employee_ids) - 1;

                        foreach (range(0, 49) as $index) {
                            $employee_id = $employee_ids[$index % $max_index];
                            $survey_personal_answer = SurveyPersonalAnswer::factory()->create([
                                'survey_content_id' => $i >= 36 ? $survey_content->id : $i,
                                'employee_id' => $employee_id,
                                'department_id' => Arr::random($department_ids),
                                'survey_term_id' => $survey_term->id,
                            ]);

                            if ($i === 33) {
                                SurveyMainAnswer::factory()->create([
                                    'survey_personal_answer_id' => $survey_personal_answer->id,
                                    'answer' => mt_rand(0, 10),
                                ]);
                            } else {
                                SurveyMonthlyAnswer::factory()->create([
                                    'survey_personal_answer_id' => $survey_personal_answer->id,
                                ]);
                            }
                        }
                        if ($survey_personal_answer->id % 10 === 0) {
                            SurveyDescriptionAnswer::factory()->create([
                                'survey_personal_answer_id' => $survey_personal_answer->id,
                            ]);
                        }
                    };
                } else {
                    $survey_term_count = SurveyTerm::where('survey_category_id', $survey_category_monthly->id)->count();
                    $survey_term = SurveyTerm::factory()->create([
                        'survey_category_id' => $survey_category_monthly->id,
                        'start_date' => $date,
                        'count' => $survey_term_count + 1,
                    ]);
                    for ($i = 33; $i < 36; $i++) {
                        $employee_ids = Employee::where('company_id', $company->id)->pluck('id')->toArray();
                        $department_ids = Department::where('company_id', $company->id)->pluck('id')->toArray();
                        $max_index = count($employee_ids) - 1;

                        foreach (range(0, 49) as $index) {
                            $employee_id = $employee_ids[$index % $max_index];
                            $survey_personal_answer = SurveyPersonalAnswer::factory()->create([
                                'survey_content_id' => $i,
                                'employee_id' => $employee_id,
                                'department_id' => Arr::random($department_ids),
                                'survey_term_id' => $survey_term->id,
                            ]);

                            if ($i === 33) {
                                SurveyMainAnswer::factory()->create([
                                    'survey_personal_answer_id' => $survey_personal_answer->id,
                                    'answer' => mt_rand(0, 10),
                                ]);
                            } else {
                                SurveyMonthlyAnswer::factory()->create([
                                    'survey_personal_answer_id' => $survey_personal_answer->id,
                                ]);
                            }
                        }
                        if ($survey_personal_answer->id % 10 === 0) {
                            SurveyDescriptionAnswer::factory()->create([
                                'survey_personal_answer_id' => $survey_personal_answer->id,
                            ]);
                        }
                    };
                }
                $survey_term->plan()->save(Plan::factory()->make([
                    'survey_term_id' => $survey_term->id,
                ]));
            }
        });
    }
}
