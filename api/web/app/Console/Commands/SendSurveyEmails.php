<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\SurveyNotification;
use App\Models\SurveyTerm;
use App\Models\Employee;
use Carbon\Carbon;

class SendSurveyEmails extends Command
{
    protected $signature = 'email:send-surveys';
    protected $description = 'Send survey emails';

    public function handle()
    {
        $today = Carbon::today();
    
        // SurveyTerms の start_date をチェックし、該当する場合にメール送信
        $surveyTerms = SurveyTerm::whereDate('start_date', $today)->get();
    
        foreach ($surveyTerms as $surveyTerm) {
            $category = $surveyTerm->surveyCategory->category;
            $departmentId = $surveyTerm->surveyCategory->department_id;
            $company = $surveyTerm->surveyCategory->company_id;
            $employees = Employee::where('company_id', $company)->get();
    
            foreach ($employees as $employee) {
                $email = $employee->email;
    
                // survey_categories の company_id と employees の company_id を比較して一致する場合にメールを送信
                if ($surveyTerm->surveyCategory->company_id == $employee->company_id) {
                    switch ($category) {
                        case 0:
                            $subject = '【Gajup!】従業員サーベイ';
                            $content = '従業員サーベイです。以下のリンクから回答をしてください。';
                            $link = 'http://localhost:3000/survey/main?' . http_build_query([
                                'employee_id' => $employee->id,
                                'survey_term_id' => $surveyTerm->id,
                                'survey_category_id' => $surveyTerm->surveyCategory->id,
                                'survey_content_id' => optional($surveyTerm->surveyCategory->surveyContents->first())->id,
                                'department_id' => $departmentId,
                            ]);

                            // department_id が指定されている場合は、該当する部門の社員にのみ配信を行う
                            if (!is_null($departmentId)) {
                                $departmentEmployees = $surveyTerm->surveyPersonalAnswers()
                                    ->where('department_id', $departmentId)
                                    ->pluck('employee_id');
                                $departmentEmployees = Employee::whereIn('id', $departmentEmployees)->get();
                                foreach ($departmentEmployees as $departmentEmployee) {
                                    Mail::to($departmentEmployee->email)->send(new SurveyNotification($subject, $content, $link));
                                }
                            } else {
                                // department_id が null の場合でも全社員に配信を行う
                                Mail::to($email)->send(new SurveyNotification($subject, $content, $link));
                            }
                            break;
                        case 1:
                            $subject = '【Gajup!】マンスリーアンケート';
                            $content = 'マンスリーアンケートです。以下のリンクから回答をしてください。';
                            $link = 'http://localhost:3000/survey/monthly?' . http_build_query([
                                'employee_id' => $employee->id,
                                'survey_term_id' => $surveyTerm->id,
                                'survey_category_id' => $surveyTerm->surveyCategory->id,
                                'survey_content_id' => optional($surveyTerm->surveyCategory->surveyContents->first())->id,
                                'department_id' => $departmentId,
                            ]);

                            // department_id が指定されている場合は、該当する部門の社員にのみ配信を行う
                            if (!is_null($departmentId)) {
                                $departmentEmployees = $surveyTerm->surveyPersonalAnswers()
                                    ->where('department_id', $departmentId)
                                    ->pluck('employee_id');
                                $departmentEmployees = Employee::whereIn('id', $departmentEmployees)->get();
                                foreach ($departmentEmployees as $departmentEmployee) {
                                    Mail::to($departmentEmployee->email)->send(new SurveyNotification($subject, $content, $link));
                                }
                            } else {
                                // department_id が null の場合は、全社員に配信を行う
                                Mail::to($email)->send(new SurveyNotification($subject, $content, $link));
                            }
                            break;
                        default:
                            continue 2;
                    }
                }
            }
        }
    
        $this->info('Survey emails sent successfully!');
    }
}
