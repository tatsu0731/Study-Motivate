<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Traits\GetCompanyIdTrait;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Traits\GetPreviousMainSurveyTermIdTrait;

use function PHPUnit\Framework\isEmpty;

class ComparisonController extends Controller
{
    use GetCompanyIdTrait;
    use GetPreviousMainSurveyTermIdTrait;

    public function previousCompanyComparison(Request $request, $survey_term_id)
    {
        $company_id = $this->getCompanyId($survey_term_id);
        $previous_main_survey_term_id = $this->GetPreviousMainSurveyTermIdTrait($survey_term_id);

        // 自社の平均値と設問カテゴリーごとの平均値を取得
        $survey_category_avg = $this->getSurveyCategoryAverage($company_id, $survey_term_id);
        $company_avg = $this->getCompanyAverage($survey_term_id);
        // 自社の今回の平均値を整形
        $present_avg = $survey_category_avg->map(function ($item) {
            return [
                'x' => 'Q' . $item->survey_question_category_id,
                'y' => round($item->average, 2),
            ];
        });
        // 会社の平均値と設問カテゴリーごとの平均値を比較
        $company_comparison_avg = $this->compareAverages($company_avg, $survey_category_avg);
        // 自社のsurvey_term_id以前の設問カテゴリの平均値を全取得
        $all_company_category_avg = $this->allCompanyCategoryAverage($survey_term_id, $company_id);

        // 自社の今回の偏差値を取得
        $present_deviation = $this->calculateDeviationScoresForCompany($all_company_category_avg, $survey_category_avg);
        // 全社のsurvey_term_id以前の設問カテゴリの平均値を全取得
        $all_companies_category_avg = $this->allCompaniesCategoryAverage($survey_term_id);
        // 全社のsurvey_term_id以前の偏差値を取得
        $all_companies_deviation = $this->calculateDeviationScoresForCompany($all_companies_category_avg, $survey_category_avg);
        // 会社の偏差値と設問カテゴリーごとの偏差値を比較
        $company_comparison_deviation = $this->compareDeviations($present_deviation, $all_companies_deviation);

        if (!empty($previous_main_survey_term_id)) {
            // 自社の前回の平均値取得処理
            $prev_survey_category_avg = $this->getSurveyCategoryAverage($company_id, $previous_main_survey_term_id);
            $prev_comparison_avg = $this->compareAverages($company_avg, $prev_survey_category_avg);

            // 自社の前回の偏差値取得処理
            $prev_all_company_category_avg = $this->allCompanyCategoryAverage($previous_main_survey_term_id, $company_id);
            $prev_survey_category_deviation = $this->calculateDeviationScoresForCompany($prev_all_company_category_avg, $prev_survey_category_avg);
            $prev_comparison_deviation = $this->compareDeviations($present_deviation, $prev_survey_category_deviation);
        } else {
            $prev_comparison_avg = null;
            $prev_comparison_deviation = null;
        }

        return [[
            'data_type' => '平均値',
            'data' => [
                [
                    'name' => '平均値',
                    'data' => $present_avg,
                ],
                [
                    'name' => '前回比',
                    'data' => $prev_comparison_avg,
                ],
                [
                    'name' => '他社比',
                    'data' => $company_comparison_avg,
                ],
            ],
        ], [
            'data_type' => '偏差値',
            'data' => [
                [
                    'name' => '偏差値',
                    'data' => $present_deviation,
                ],
                [
                    'name' => '前回比',
                    'data' => $prev_comparison_deviation,
                ],
                [
                    'name' => '他社比',
                    'data' => $company_comparison_deviation,
                ],
            ],
        ]];
    }

    protected function getSurveyCategoryAverage($company_id, $survey_term_id)
    {
        return DB::table('survey_contents as sc')
            ->join('survey_personal_answers as spa', 'spa.survey_content_id', '=', 'sc.id')
            ->join('survey_main_answers as sma', 'spa.id', '=', 'sma.survey_personal_answer_id')
            ->join('survey_categories as scat', 'scat.id', '=', 'sc.survey_category_id')
            ->join('survey_questions as sq', 'sq.id', '=', 'sc.survey_question_id')
            ->join('survey_terms as st', 'spa.survey_term_id', '=', 'st.id')
            ->where([['company_id', $company_id], ['scat.category', 0], ['st.id', '=', $survey_term_id], ['sq.survey_question_category_id', '<=', 16]])
            ->groupBy('st.id', 'sq.survey_question_category_id', 'st.start_date')
            ->orderBy('st.start_date')
            ->select(DB::raw('st.start_date as month, AVG(sma.answer) as average, sq.survey_question_category_id, st.id as survey_term_id'))
            ->get();
    }

    protected function getCompanyAverage($survey_term_id)
    {
        return DB::table('survey_contents as sc')
            ->join('survey_personal_answers as spa', 'spa.survey_content_id', '=', 'sc.id')
            ->join('survey_main_answers as sma', 'spa.id', '=', 'sma.survey_personal_answer_id')
            ->join('survey_categories as scat', 'scat.id', '=', 'sc.survey_category_id')
            ->join('survey_questions as sq', 'sq.id', '=', 'sc.survey_question_id')
            ->join('survey_terms as st', 'spa.survey_term_id', '=', 'st.id')
            ->where([['scat.category', 0], ['st.id', '<=', $survey_term_id], ['sq.survey_question_category_id', '<=', 16]])
            ->groupBy('sq.survey_question_category_id')
            ->select(DB::raw('AVG(sma.answer) as average, sq.survey_question_category_id'))
            ->orderBy('sq.survey_question_category_id')
            ->get();
    }

    protected function compareAverages($company_avg, $survey_category_avg)
    {
        $comparison = $company_avg->map(function ($item) use ($survey_category_avg) {
            $survey_category_avg = $survey_category_avg->where('survey_question_category_id', $item->survey_question_category_id);
            return [
                'x' => 'Q' . $item->survey_question_category_id,
                'y' => round($item->average - $survey_category_avg->first()->average, 2),
            ];
        });

        return $comparison;
    }

    protected function calculateDeviationScoresForCompany($all_company_category_avg, $survey_category_avg)
    {
        // 自社の偏差値を計算するための標準偏差を取得
        $company_standard_deviations = $this->calculateStandardDeviation($all_company_category_avg->pluck('average')->toArray());

        // カテゴリIDをキーとしてカテゴリごとの標準偏差を保持する連想配列を作成
        $category_standard_deviations = [];
        foreach ($all_company_category_avg as $category) {
            $category_standard_deviations[$category->survey_question_category_id] = $company_standard_deviations;
        }

        // 各設問カテゴリーごとの偏差値を計算
        return $survey_category_avg->map(function ($item) use ($category_standard_deviations, $all_company_category_avg) {
            // 自社の偏差値を計算
            $category_standard_deviation = $category_standard_deviations[$item->survey_question_category_id];
            $deviation_score = ($item->average - $all_company_category_avg->where('survey_question_category_id', $item->survey_question_category_id)->first()->average) / $category_standard_deviation * 10 + 50;

            return [
                'x' => 'Q' . $item->survey_question_category_id,
                'y' => round($deviation_score, 2),
            ];
        });
    }

    protected function compareDeviations($company_deviation, $survey_category_deviation)
    {
        $comparison = $company_deviation->map(function ($item) use ($survey_category_deviation) {
            $survey_category_deviation = $survey_category_deviation->where('x', $item["x"])->values();
            return $survey_category_deviation->map(function ($survey_deviation) use ($item) {
                return [
                    'x' => $item["x"],
                    'y' => round($item["y"] - $survey_deviation["y"], 2),
                ];
            });
        });

        // 1つの配列に結合する
        $mergedComparison = $comparison->collapse()->all();

        return $mergedComparison;
    }

    protected function calculateStandardDeviation($values)
    {
        $count = count($values);
        $mean = array_sum($values) / $count;
        $variance = array_reduce($values, function ($carry, $value) use ($mean) {
            return $carry + pow($value - $mean, 2);
        }, 0) / $count;
        return sqrt($variance);
    }

    protected function allCompanyCategoryAverage($survey_term_id, $company_id)
    {
        $survey_date = DB::table('survey_terms')->where('id', $survey_term_id)->value('start_date');

        return DB::table('survey_contents as sc')
            ->join('survey_personal_answers as spa', 'spa.survey_content_id', '=', 'sc.id')
            ->join('survey_main_answers as sma', 'spa.id', '=', 'sma.survey_personal_answer_id')
            ->join('survey_categories as scat', 'scat.id', '=', 'sc.survey_category_id')
            ->join('survey_questions as sq', 'sq.id', '=', 'sc.survey_question_id')
            ->join('survey_terms as st', 'spa.survey_term_id', '=', 'st.id')
            ->where('scat.category', 0)
            ->where('company_id', '=', $company_id)
            ->whereDate('st.start_date', '<=', $survey_date)
            ->where('sq.survey_question_category_id', '<=', 16)
            ->groupBy('st.id', 'sq.survey_question_category_id')
            ->select(DB::raw('AVG(sma.answer) as average, sq.survey_question_category_id'))
            ->get();
    }

    protected function allCompaniesCategoryAverage($survey_term_id)
    {
        $survey_date = DB::table('survey_terms')->where('id', $survey_term_id)->value('start_date');

        return DB::table('survey_contents as sc')
            ->join('survey_personal_answers as spa', 'spa.survey_content_id', '=', 'sc.id')
            ->join('survey_main_answers as sma', 'spa.id', '=', 'sma.survey_personal_answer_id')
            ->join('survey_categories as scat', 'scat.id', '=', 'sc.survey_category_id')
            ->join('survey_questions as sq', 'sq.id', '=', 'sc.survey_question_id')
            ->join('survey_terms as st', 'spa.survey_term_id', '=', 'st.id')
            ->where('scat.category', 0)
            ->whereDate('st.start_date', '<=', $survey_date)
            ->where('sq.survey_question_category_id', '<=', 16)
            ->groupBy('st.id', 'sq.survey_question_category_id')
            ->select(DB::raw('AVG(sma.answer) as average, sq.survey_question_category_id'))
            ->get();
    }
}
