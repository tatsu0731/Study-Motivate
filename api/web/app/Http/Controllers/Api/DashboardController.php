<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\SurveyTerm;
use Illuminate\Http\Request;
use App\Models\SurveyCategory;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\SurveyPersonalAnswer;

class DashboardController extends Controller
{

    public function dashboardData(Request $request, $company_id)
    {
        // survey_term_idとstatusを取得
        $survey_term = SurveyCategory::where('company_id', $company_id)
            ->join('survey_terms as st', 'survey_categories.id', '=', 'st.survey_category_id')
            ->select('st.id as survey_term_id', DB::raw('CASE WHEN status = 0 THEN "実施前" WHEN status = 1 THEN "実施中" WHEN status = 2 THEN "停止中" END as status'))
            ->where('category', 0)
            ->where('st.start_date', '<=', Carbon::now())
            ->orderBy('st.start_date', 'desc')
            ->first();

        $survey_term_id = $survey_term->survey_term_id;

        // 回答率を取得
        $data = SurveyPersonalAnswer::where('survey_term_id', $survey_term_id)
            ->select('employee_id')
            ->distinct()
            ->count('employee_id');

        $employeeCount = DB::table('employees')
            ->where('company_id', $company_id)
            ->count();

        $responseRatio = round($data / $employeeCount * 100, 2);

        // 次回アンケートの開始日を取得
        $next_survey_date = SurveyTerm::join('survey_categories', 'survey_terms.survey_category_id', '=', 'survey_categories.id')
            ->where('company_id', $company_id)
            ->where('start_date', '>', date('Y-m-d'))
            ->orderBy('start_date', 'asc')
            ->selectRaw('DATE_FORMAT(start_date, "%Y/%m/%d") as start_date')
            ->first()->start_date ?? null;

        // 総合点数を取得
        $data = DB::table('survey_contents as sc')
            ->join('survey_personal_answers as spa', 'spa.survey_content_id', '=', 'sc.id')
            ->join('survey_main_answers as sma', 'spa.id', '=', 'sma.survey_personal_answer_id')
            ->join('survey_categories as scat', 'scat.id', '=', 'sc.survey_category_id')
            ->join('survey_questions as sq', 'sq.id', '=', 'sc.survey_question_id')
            ->join('survey_terms as st', 'spa.survey_term_id', '=', 'st.id')
            ->where([['st.id', '=', $survey_term_id], ['sq.survey_question_category_id', '<=', 16]])
            ->select('st.id', 'sq.survey_question_category_id', 'sma.answer');

        // 設問カテゴリーごとの平均値
        $survey_category_avg = $data
            ->groupBy('sq.survey_question_category_id')
            ->select(DB::raw('AVG(sma.answer) as average, sq.survey_question_category_id'))
            ->get();

        // 目標を取得
        $goal = DB::table('goals')
            ->where('survey_term_id', $survey_term_id)
            ->select('goal')
            ->first();

        return [
            "survey_term_id" => $survey_term_id,
            "status" => $survey_term->status,
            "response_ratio" => $responseRatio,
            "next_survey_date" => $next_survey_date,
            "overall_score" => round($survey_category_avg->sum('average'), 2),
            "goal" => $goal->goal ?? "未設定",
        ];
    }
}
