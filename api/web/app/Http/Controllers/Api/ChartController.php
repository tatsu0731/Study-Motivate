<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\SurveyContent;
use App\Traits\GetCompanyIdTrait;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Traits\GetLatestSurveyTermIdTrait;

class ChartController extends Controller
{
    use GetCompanyIdTrait;
    use GetLatestSurveyTermIdTrait;

    // eNPSの推移
    public function enpsTransition(Request $request, $survey_term_id)
    {
        $company_id = $this->getCompanyId($survey_term_id);
        $latest_survey_term_id = $this->GetLatestSurveyTermIdTrait($survey_term_id);

        $data = SurveyContent::where('survey_question_id', 33) // eNPSの質問ID
            ->join('survey_personal_answers as spa', 'survey_contents.id', '=', 'spa.survey_content_id')
            ->join('survey_main_answers as sma', 'spa.id', '=', 'sma.survey_personal_answer_id')
            ->join('survey_categories as scat', 'survey_contents.survey_category_id', '=', 'scat.id')
            ->join('survey_terms as st', 'spa.survey_term_id', '=', 'st.id')
            ->where([['spa.survey_term_id', '<=', $latest_survey_term_id], ['company_id', $company_id]])
            ->select('spa.survey_term_id', 'spa.created_at', 'sma.answer', 'st.start_date');

        $full_data = $data->groupBy('spa.survey_term_id')
            ->select(
                DB::raw('CASE
                WHEN sma.answer >= 0 AND sma.answer <= 6 THEN "批判者"
                WHEN sma.answer >= 7 AND sma.answer <= 8 THEN "中立者"
                WHEN sma.answer >= 9 AND sma.answer <= 10 THEN "推奨者"
                ELSE "その他"
            END as label'),
                DB::raw('COUNT(sma.answer) as value'),
                DB::raw('DATE_FORMAT(st.start_date, "%Y-%m") as month'),
            )
            ->groupBy('label', 'month')
            ->orderByRaw('month')
            ->get();

        $grouped_data = collect($full_data)->groupBy('month')->map(function ($items) {
            return $items->groupBy('label')->map(function ($items) {
                return $items->sum('value');
            });
        });

        // 各 start_date ごとに合計値をもとに割合を計算
        $percentages_by_date = $grouped_data->map(function ($item) {
            $total = $item->sum();
            return $item->map(function ($value) use ($total) {
                return round($value / $total * 100, 2);
            });
        });

        // 推奨者と批判者の割合を取得して差を計算
        $difference_by_date = $percentages_by_date->map(function ($item) {
            return $item->get('推奨者') - $item->get('批判者');
        });

        $new_data = $difference_by_date->map(function ($value, $key) {
            return [
                'month' => $key,
                'enps' => $value,
            ];
        });

        $result = $new_data->slice(-36)->values();

        return response()->json($result, 200);
    }

    // eNPSの割合
    public function enpsRatio(Request $request, $survey_term_id)
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

        return response()->json($data, 200);
    }

    // 16の設問項目の総合点の推移
    public function questionCategoryTransition(Request $request, $survey_term_id)
    {
        $company_id = $this->getCompanyId($survey_term_id);

        $data = DB::table('survey_contents as sc')
            ->join('survey_personal_answers as spa', 'spa.survey_content_id', '=', 'sc.id')
            ->join('survey_main_answers as sma', 'spa.id', '=', 'sma.survey_personal_answer_id')
            ->join('survey_categories as scat', 'scat.id', '=', 'sc.survey_category_id')
            ->join('survey_questions as sq', 'sq.id', '=', 'sc.survey_question_id')
            ->join('survey_terms as st', 'spa.survey_term_id', '=', 'st.id')
            ->where([['company_id', $company_id], ['scat.category', 0], ['st.id', '<=', $survey_term_id], ['sq.survey_question_category_id', '<=', 16]])
            ->select('st.id', 'sq.survey_question_category_id', 'st.start_date', 'sma.answer');

        // 設問カテゴリーごとの平均値
        $survey_category_avg = $data
            ->groupBy('st.id', 'sq.survey_question_category_id', 'st.start_date')
            ->orderBy('st.start_date')
            ->select(DB::raw('st.start_date as month, AVG(sma.answer) as average, sq.survey_question_category_id, st.id as survey_term_id'))
            ->get();

        // survey_category_idごとの合計値を計算
        $result = $survey_category_avg
            ->sortBy('month')
            ->groupBy('survey_term_id', 'month')
            ->take(-6)
            ->map(function ($group) {
                $total = round($group->sum('average'), 2);
                $month = $group->first()->month;
                return (object) [
                    'month' => $month,
                    'total' => $total,
                ];
            });

        $result = $result->values()->all();

        return response()->json($result, 200);
    }

    // ①と②の総合平均の推移
    public function questionTypeTransition(Request $request, $survey_term_id)
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

        $result = array_values(array_slice($mergedData->toArray(), -6));
        return response()->json($result, 200);
    }

    // 32の設問項目の回答分布
    public function questionRatio(Request $request, $survey_term_id, $question_category_id)
    {
        $answers = DB::table('survey_contents as sc')
            ->join('survey_personal_answers as spa', 'spa.survey_content_id', '=', 'sc.id')
            ->join('survey_main_answers as sma', 'spa.id', '=', 'sma.survey_personal_answer_id')
            ->join('survey_categories as scat', 'scat.id', '=', 'sc.survey_category_id')
            ->join('survey_questions as sq', 'sq.id', '=', 'sc.survey_question_id')
            ->join('survey_terms as st', 'spa.survey_term_id', '=', 'st.id')
            ->where([['scat.category', 0], ['st.id', $survey_term_id], ['sq.survey_question_category_id', '<=', 16]])
            ->select('sq.id as survey_question_id', 'sma.answer');

        $data = $answers
            ->groupBy('sq.id', 'sma.answer')
            ->orderBy('sq.id')
            ->where(DB::raw('survey_question_id % 2'), $question_category_id)
            ->select('sq.id as survey_question_id', 'sma.answer', DB::raw('COUNT(sma.answer) as value'))
            ->get();

        // データを指定された形式に変換
        $new_data = [];

        foreach ($data as $item) {
            $survey_question_id = $item->survey_question_id;
            $answer = $item->answer;
            $value = $item->value;

            // サーベイ質問IDごとのオブジェクトを作成
            if (!isset($new_data[$survey_question_id])) {
                $new_data[$survey_question_id] = new \stdClass();
                $new_data[$survey_question_id]->{"question"} = 'Q' . $survey_question_id;
                $new_data[$survey_question_id]->{"total"} = 0; // 総合得点を初期化
                $new_data[$survey_question_id]->{"count"} = 0; // 回答数を初期化
            }

            // answerに基づいて適切なキーに値を割り当て
            switch ($answer) {
                case 1:
                    $new_data[$survey_question_id]->{"1. そう思わない"} = $value;
                    break;
                case 2:
                    $new_data[$survey_question_id]->{"2. あまりそう思わない"} = $value;
                    break;
                case 3:
                    $new_data[$survey_question_id]->{"3. どちらでもない"} = $value;
                    break;
                case 4:
                    $new_data[$survey_question_id]->{"4. 少しそう思う"} = $value;
                    break;
                case 5:
                    $new_data[$survey_question_id]->{"5. そう思う"} = $value;
                    break;
                default:
                    break;
            }

            // 総合得点を更新
            $new_data[$survey_question_id]->{"total"} += $answer * $value;
            // 回答数を更新
            $new_data[$survey_question_id]->{"count"} += $value;
        }

        // 平均スコアを算出
        foreach ($new_data as &$question) {
            if ($question->{"count"} > 0) {
                $question->{"平均スコア"} = $question->{"total"} / $question->{"count"};
            } else {
                $question->{"平均スコア"} = 0; // ゼロ除算を防ぐためのデフォルト値
            }

            // 不要なプロパティを削除
            unset($question->{"total"});
            unset($question->{"count"});
        }

        // JSON形式に変換
        $result = array_values($new_data);

        return response()->json($result, 200);
    }

    // 16の設問項目と属性の相関ヒートマップ
    public function attributeHeatmap(Request $request, $survey_term_id)
    {
        $attributes = [['性別', 'gender'], ['年代', 'age'], ['部署', 'department_id'], ['入社歴', 'years_of_service']];
        $result = [];

        foreach ($attributes as $attribute) {
            $data = $this->getSurveyData($survey_term_id);
            $averages = $this->calculateAverages($data, $attribute[1]);

            $result[] = [
                'attribute' => $attribute[0],
                'data' => $averages->values()->map(
                    function ($group, $index) {
                        return [
                            'name' => $index,
                            'data' => $group->groupBy('x')
                                ->values()->map(function ($group) {
                                    // 同じ x 値を持つデータをグループ化し、その平均を計算
                                    $averageY = $group->avg('y');
                                    return [
                                        'x' => $group->first()['x'],
                                        'y' => round($averageY, 2)
                                    ];
                                })
                        ];
                    }
                )
            ];
        }

        return response()->json($result, 200);
    }

    private function getSurveyData($survey_term_id)
    {
        return DB::table('survey_contents as sc')
            ->join('survey_personal_answers as spa', 'spa.survey_content_id', '=', 'sc.id')
            ->join('survey_main_answers as sma', 'spa.id', '=', 'sma.survey_personal_answer_id')
            ->join('survey_categories as scat', 'scat.id', '=', 'sc.survey_category_id')
            ->join('survey_questions as sq', 'sq.id', '=', 'sc.survey_question_id')
            ->join('survey_terms as st', 'spa.survey_term_id', '=', 'st.id')
            ->where([['scat.category', 0], ['st.id', $survey_term_id]])
            ->select('sq.id as survey_question_id', 'survey_question_category_id', 'sma.answer', 'gender', 'age', 'spa.department_id', 'years_of_service')
            ->get();
    }

    private function calculateAverages($data, $groupByAttribute)
    {
        return $data->sortBy($groupByAttribute)->groupBy($groupByAttribute)
            ->map(function ($group) use ($groupByAttribute) {
                return $group->groupBy('survey_question_id')->map(function ($subGroup) {
                    return [
                        'x' => $subGroup->first()->survey_question_category_id,
                        'y' => round($subGroup->avg('answer'), 2)
                    ];
                });
            });
    }

    // 追加設問の回答分布
    public function additionalQuestionRatio(Request $request, $survey_term_id)
    {
        $latest_survey_term_id = $this->GetLatestSurveyTermIdTrait($survey_term_id);
        $data = DB::table('survey_contents as sc')
            ->join('survey_personal_answers as spa', 'spa.survey_content_id', '=', 'sc.id')
            ->join('survey_monthly_answers as sma', 'spa.id', '=', 'sma.survey_personal_answer_id')
            ->join('survey_categories as scat', 'scat.id', '=', 'sc.survey_category_id')
            ->join('survey_questions as sq', 'sq.id', '=', 'sc.survey_question_id')
            ->join('survey_terms as st', 'spa.survey_term_id', '=', 'st.id')
            ->where([['scat.category', 1], ['st.id', '<=', $latest_survey_term_id], ['st.id', '>=', $survey_term_id], ['sq.survey_question_category_id', '=', 19]])
            ->select('sq.id as survey_question_id', 'sma.answer', 'st.id as survey_term_id', 'sq.text', 'start_date');

        $data = $data
            ->groupBy('sq.id', 'sma.answer', 'start_date')
            ->orderBy('sq.id')
            ->orderBy('sma.answer', 'desc')
            ->select('sq.id as survey_question_id', 'sma.answer', DB::raw('COUNT(sma.answer) as value'), 'sq.text as question', 'start_date as month')
            ->get();

        $new_data = [];
        foreach ($data as $item) {
            $question = $item->question;
            $month = $item->month;
            $answerText = $item->answer == 1 ? 'はい' : 'いいえ';
            $value = $item->value;

            if (!isset($new_data[$month])) {
                $new_data[$month] = [];
            }
            if (!isset($new_data[$month][$question])) {
                $new_data[$month][$question] = [];
            }

            $new_data[$month][$question][] = [
                'answer' => $answerText,
                'value' => $value
            ];
        }

        $result = [];

        foreach ($new_data as $month => $questions) {
            $monthlyData = [];

            foreach ($questions as $question => $answers) {
                $monthlyData[] = [
                    'question' => $question,
                    'data' => $answers
                ];
            }

            $result[] = [
                'month' => $month,
                'data' => $monthlyData
            ];
        }
        return response()->json($result, 200);
    }

    // 設問項目の平均ヒートマップ
    public function questionCategoryTreemap(Request $request, $survey_term_id)
    {
        $data = $this->getSurveyData($survey_term_id);
        $averages = $this->calculateAverages($data, 'survey_question_category_id');

        $result = [
            'name' => '平均値',
            'data' => $averages->values()->flatMap(
                function ($group) {
                    return $group->groupBy('x')
                        ->values()->map(function ($group) {
                            $averageY = $group->avg('y');
                            return [
                                'x' => '項目' . $group->first()['x'],
                                'y' => round($averageY, 2)
                            ];
                        });
                }
            )
        ];
        return response()->json($result, 200);
    }
}
