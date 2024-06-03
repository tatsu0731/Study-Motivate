<?php

namespace App\Services;

use App\Models\SurveyContent;
use App\Traits\GetCompanyIdTrait;
use Illuminate\Support\Facades\DB;
use App\Traits\GetLatestSurveyTermIdTrait;

class ChartService
{
  use GetCompanyIdTrait;
  use GetLatestSurveyTermIdTrait;

  public function questionAvg($survey_term_id)
  {
    $company_id = $this->getCompanyId($survey_term_id);

    $data = DB::table('survey_contents as sc')
      ->join('survey_personal_answers as spa', 'spa.survey_content_id', '=', 'sc.id')
      ->join('survey_main_answers as sma', 'spa.id', '=', 'sma.survey_personal_answer_id')
      ->join('survey_categories as scat', 'scat.id', '=', 'sc.survey_category_id')
      ->join('survey_questions as sq', 'sq.id', '=', 'sc.survey_question_id')
      ->join('survey_terms as st', 'spa.survey_term_id', '=', 'st.id')
      ->where([
        ['company_id', $company_id],
        ['scat.category', 0],
        ['st.id', '=', $survey_term_id],
      ])
      ->select(
        DB::raw(
          'ROUND(AVG(sma.answer), 2) as average'
        ),
        'sq.text as question',
      )
      ->groupBy('sq.id', 'sq.text')
      ->get();

    return $data;
  }

  public function questionTypeAvg($survey_term_id)
  {
    $company_id = $this->getCompanyId($survey_term_id);

    $evenData = DB::table('survey_contents as sc')
      ->join('survey_personal_answers as spa', 'spa.survey_content_id', '=', 'sc.id')
      ->join('survey_main_answers as sma', 'spa.id', '=', 'sma.survey_personal_answer_id')
      ->join('survey_categories as scat', 'scat.id', '=', 'sc.survey_category_id')
      ->join('survey_questions as sq', 'sq.id', '=', 'sc.survey_question_id')
      ->join('survey_terms as st', 'spa.survey_term_id', '=', 'st.id')
      ->where([
        ['company_id', $company_id],
        ['scat.category', 0],
        ['st.id', '<=', $survey_term_id],
      ])
      ->whereRaw('sq.survey_question_category_id % 2 = 0')
      ->select(
        'st.start_date as month',
        'st.id as survey_term_id',
        DB::raw('AVG(sma.answer) as average')
      )
      ->groupBy('st.id', 'sq.survey_question_category_id', 'st.start_date')
      ->get();

    $oddData = DB::table('survey_contents as sc')
      ->join('survey_personal_answers as spa', 'spa.survey_content_id', '=', 'sc.id')
      ->join('survey_main_answers as sma', 'spa.id', '=', 'sma.survey_personal_answer_id')
      ->join('survey_categories as scat', 'scat.id', '=', 'sc.survey_category_id')
      ->join('survey_questions as sq', 'sq.id', '=', 'sc.survey_question_id')
      ->join('survey_terms as st', 'spa.survey_term_id', '=', 'st.id')
      ->where([
        ['company_id', $company_id],
        ['scat.category', 0],
        ['st.id', '<=', $survey_term_id],
        ['sq.survey_question_category_id', '<=', 16]
      ])
      ->whereRaw('sq.survey_question_category_id % 2 = 1')
      ->select(
        'st.start_date as month',
        'st.id as survey_term_id',
        'sq.survey_question_category_id',
        DB::raw('AVG(sma.answer) as average')
      )
      ->groupBy('st.id', 'sq.survey_question_category_id', 'st.start_date')
      ->get();

    // 偶数データを start_date をキーとしてグループ化
    $evenDataGrouped = $evenData->groupBy('month')->map(function ($items) {
      return round($items->sum('average'), 2);
    });

    // 奇数データを start_date をキーとしてグループ化
    $oddDataGrouped = $oddData->groupBy('month')->map(function ($items) {
      return round($items->sum('average'), 2);
    });

    // 両方のデータをマージ
    $mergedData = $evenDataGrouped->merge($oddDataGrouped)->map(function ($item, $key) use ($evenDataGrouped, $oddDataGrouped) {
      return [
        'month' => $key,
        '組織状態' => $oddDataGrouped[$key] ?? null,
        '従業員エンゲージメント' => $evenDataGrouped[$key] ?? null,
      ];
    });

    return $mergedData;
  }

  public function eNPSRatio($survey_term_id)
  {
    $latest_survey_term_id = $this->GetLatestSurveyTermIdTrait($survey_term_id);

    $data = SurveyContent::where('survey_question_id', 33) // eNPSの質問ID
      ->join('survey_personal_answers as spa', 'survey_contents.id', '=', 'spa.survey_content_id')
      ->join('survey_main_answers as sma', 'spa.id', '=', 'sma.survey_personal_answer_id')
      ->where('spa.survey_term_id', $latest_survey_term_id)
      ->select(
        DB::raw('CASE
                    WHEN sma.answer >= 0 AND sma.answer <= 6 THEN "批判者"
                    WHEN sma.answer >= 7 AND sma.answer <= 8 THEN "中立者"
                    WHEN sma.answer >= 9 AND sma.answer <= 10 THEN "推奨者"
                    ELSE "その他"
                END as label'),
        DB::raw('COUNT(sma.answer) as value')
      )
      ->groupBy('label')
      ->orderByRaw('CASE
                    WHEN label = "批判者" THEN 1
                    WHEN label = "中立者" THEN 2
                    WHEN label = "推奨者" THEN 3
                    ELSE 4
                END')
      ->get();

    return $data;
  }
}
