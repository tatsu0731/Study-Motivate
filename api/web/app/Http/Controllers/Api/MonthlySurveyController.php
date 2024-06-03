<?php

namespace App\Http\Controllers\Api;

use App\Models\Employee;

use App\Models\Department;
use App\Models\SurveyTerm;
use Illuminate\Http\Request;
use App\Models\SurveyCategory;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\SurveyPersonalAnswer;

class MonthlySurveyController extends Controller
{
    // マンスリーアンケートの一覧取得
    public function monthlySurveyList(Request $request, $company_id)
    {
        $present_monthly_survey = SurveyCategory::where('survey_categories.company_id', $company_id)
            ->where('category', '!=', 0)
            ->join('survey_terms as st', 'survey_categories.id', '=', 'st.survey_category_id')
            ->leftJoin('departments as d', 'survey_categories.department_id', '=', 'd.id')
            ->where('start_date', '<=', date('Y-m-d'))
            ->whereRaw('DATE_ADD(start_date, INTERVAL deadline DAY) >= CURDATE()')
            ->select('survey_categories.id', DB::raw('DATE_FORMAT(st.start_date, "%Y/%m/%d") as start_date'), 'st.id as survey_term_id', 'd.name as department_name')
            ->orderBy('start_date', 'asc')
            ->get();

        $next_monthly_survey = SurveyCategory::where('survey_categories.company_id', $company_id)
            ->where('category', '!=', 0)
            ->join('survey_terms as st', 'survey_categories.id', '=', 'st.survey_category_id')
            ->leftJoin('departments as d', 'survey_categories.department_id', '=', 'd.id')
            ->where('st.start_date', '>', date('Y-m-d'))
            ->select('survey_categories.id', DB::raw('DATE_FORMAT(st.start_date, "%Y/%m/%d") as start_date'), 'st.id as survey_term_id', 'd.name as department_name')
            ->orderBy('start_date', 'asc')
            ->get();

        $data = [
            'present' => $present_monthly_survey,
            'next' => $next_monthly_survey
        ];

        $presentData = [];
        $nextData = [];

        foreach ($data['present'] as $item) {
            $surveyTermId = $item["survey_term_id"];

            if (!isset($presentData[$surveyTermId])) {
                $presentData[$surveyTermId] = [
                    'survey_term_id' => $surveyTermId,
                    'start_date' => $item["start_date"],
                    'target' => [],
                ];
            }

            if ($item["department_name"] !== null) {
                $presentData[$surveyTermId]["target"][] = $item["department_name"];
            }
        }

        foreach ($presentData as &$presentItem) {
            $presentItem["target"] = implode(',', $presentItem["target"]);
        }

        foreach ($data['next'] as $item) {
            $surveyTermId = $item["survey_term_id"];

            if (!isset($nextData[$surveyTermId])) {
                $nextData[$surveyTermId] = [
                    'survey_term_id' => $surveyTermId,
                    'start_date' => $item["start_date"],
                    'target' => [],
                ];
            }

            if ($item["department_name"] !== null) {
                $nextData[$surveyTermId]["target"][] = $item["department_name"];
            }
        }

        foreach ($nextData as &$nextItem) {
            $nextItem["target"] = implode(',', $nextItem["target"]);
        }

        $presentData = array_values($presentData);
        $nextData = array_values($nextData);

        $presentData = array_map(function ($item) {
            $item["response_ratio"] = $this->getResponseRatio($item["survey_term_id"]);
            return $item;
        }, $presentData);

        $next_survey_date = $this->getNextSurveyDate($company_id);

        return response()->json([
            'data' => [
                'next_survey_date' => $next_survey_date,
                'main_survey_term_id' => $this->getMainSurveyTermId($company_id),
                'present' => $presentData,
                'next' => $nextData,
            ],
        ], 200);
    }

    // マンスリーアンケートの回答率取得
    public function getResponseRatio($survey_term_id)
    {
        $survey_term = SurveyTerm::find($survey_term_id);
        $survey_category = SurveyCategory::find($survey_term->survey_category_id);
        $department = Department::find($survey_category->department_id);

        if (!$department) {
            $total = Employee::where('company_id', $survey_category->company_id)->count();
        } else {
            $total = $department->total;
        }

        $answered = SurveyPersonalAnswer::where('survey_term_id', $survey_term_id)
            ->select('employee_id')
            ->distinct()
            ->count('employee_id');

        $response_ratio = $total === 0 ? 0 : round($answered / $total * 100, 2);

        return $response_ratio;
    }

    // 次回アンケート配信日の取得
    public function getNextSurveyDate($company_id)
    {
        $next_survey_date = SurveyTerm::join('survey_categories', 'survey_terms.survey_category_id', '=', 'survey_categories.id')
            ->where('company_id', $company_id)
            ->where('start_date', '>', date('Y-m-d'))
            ->orderBy('start_date', 'asc')
            ->selectRaw('DATE_FORMAT(start_date, "%Y/%m/%d") as start_date')
            ->first()->start_date ?? null;

        return $next_survey_date;
    }

    public function getMainSurveyTermId($company_id)
    {
        $survey_term_id = SurveyCategory::where('company_id', $company_id)
            ->join('survey_terms as st', 'survey_categories.id', '=', 'st.survey_category_id')
            ->where('start_date', '<=', date('Y-m-d'))
            ->whereRaw('DATE_ADD(start_date, INTERVAL deadline DAY) >= CURDATE()')
            ->select('st.id as survey_term_id')
            ->orderBy('start_date', 'asc')
            ->first()->survey_term_id;

        $main_survey_term_id = SurveyTerm::where('survey_terms.id', '<=', $survey_term_id)
            ->join('survey_categories as scat', 'survey_terms.survey_category_id', '=', 'scat.id')
            ->where([['scat.company_id', $company_id], ['category', 0]])
            ->orderBy('survey_terms.id', 'desc')
            ->select('survey_terms.id as survey_term_id')
            ->first()->survey_term_id;

        return $main_survey_term_id;
    }
}
