<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Traits\GetCompanyIdTrait;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Traits\GetLatestSurveyTermIdTrait;

class MonthlyChartController extends Controller
{
    use GetCompanyIdTrait;
    use GetLatestSurveyTermIdTrait;

    // ①と②の総合平均の推移
    public function additionalQuestionTransition(Request $request, $survey_term_id)
    {
        $company_id = $this->getCompanyId($survey_term_id);

        $organization_aspect = DB::table('survey_contents as sc')
            ->join('survey_personal_answers as spa', 'spa.survey_content_id', '=', 'sc.id')
            ->join('survey_monthly_answers as sma', 'spa.id', '=', 'sma.survey_personal_answer_id')
            ->join('survey_categories as scat', 'scat.id', '=', 'sc.survey_category_id')
            ->join('survey_questions as sq', 'sq.id', '=', 'sc.survey_question_id')
            ->join('survey_terms as st', 'spa.survey_term_id', '=', 'st.id')
            ->where([
                ['company_id', $company_id],
                ['st.id', '<=', $survey_term_id],
                ['sc.survey_question_id', 34],
            ])
            ->select(
                DB::raw('DATE_FORMAT(st.start_date, "%Y-%m") as month'),
                'st.id as survey_term_id',
                DB::raw('COUNT(sma.answer) as total_answers'),
                DB::raw('SUM(CASE WHEN sma.answer = true THEN 1 ELSE 0 END) as true_answers')
            )
            ->groupBy('st.id', 'sq.survey_question_category_id', 'st.start_date')
            ->get();

        // 各月の true の割合を計算する
        foreach ($organization_aspect as $aspect) {
            $aspect->true_ratio = $aspect->total_answers > 0 ? round(($aspect->true_answers / $aspect->total_answers) * 100, 2) : 0;
        }

        $employee_aspect = DB::table('survey_contents as sc')
            ->join('survey_personal_answers as spa', 'spa.survey_content_id', '=', 'sc.id')
            ->join('survey_monthly_answers as sma', 'spa.id', '=', 'sma.survey_personal_answer_id')
            ->join('survey_categories as scat', 'scat.id', '=', 'sc.survey_category_id')
            ->join('survey_questions as sq', 'sq.id', '=', 'sc.survey_question_id')
            ->join('survey_terms as st', 'spa.survey_term_id', '=', 'st.id')
            ->where([
                ['company_id', $company_id],
                ['st.id', '<=', $survey_term_id],
                ['sc.survey_question_id', 35],
            ])
            ->select(
                DB::raw('DATE_FORMAT(st.start_date, "%Y-%m") as month'),
                'st.id as survey_term_id',
                DB::raw('COUNT(sma.answer) as total_answers'),
                DB::raw('SUM(CASE WHEN sma.answer = true THEN 1 ELSE 0 END) as true_answers')
            )
            ->groupBy('st.id', 'sq.survey_question_category_id', 'st.start_date')
            ->get();

        // 各月の true の割合を計算する
        foreach ($employee_aspect as $aspect) {
            $aspect->true_ratio = $aspect->total_answers > 0 ? round(($aspect->true_answers / $aspect->total_answers) * 100, 2) : 0;
        }


        // $mergedData = $organization_aspect->merge($employee_aspect);
        // $result = [
        //     'month' => $organization_aspect->pluck('month'),
        //     '組織状態' => $organization_aspect->true_ratio,
        //     '従業員エンゲージメント' => $employee_aspect->true_ratio,
        // ];

        $data = [
            'organization_aspect' => $organization_aspect,
            'employee_aspect' => $employee_aspect,
        ];

        $organization_grouped = [];
        $employee_grouped = [];

        foreach ($data['organization_aspect'] as $item) {
            $organization_grouped[$item->month] = $item->true_ratio;
        }

        foreach ($data['employee_aspect'] as $item) {
            $employee_grouped[$item->month] = $item->true_ratio;
        }

        $result = [];

        foreach ($organization_grouped as $month => $organization_ratio) {
            $result[] = [
                'month' => $month,
                '組織状態' => $organization_ratio,
                '従業員エンゲージメント' => $employee_grouped[$month]
            ];
        }

        $result = array_values(array_slice($result, -6));

        return response()->json($result, 200);
    }
}
