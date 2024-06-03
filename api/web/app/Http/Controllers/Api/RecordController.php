<?php

namespace App\Http\Controllers\Api;

use OpenAI;
use Carbon\Carbon;
use App\Models\Plan;
use Illuminate\Http\Request;
use App\Models\SurveyCategory;
use App\Services\ChartService;
use App\Traits\GetCompanyIdTrait;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Summary;
use App\Models\SurveyPersonalAnswer;

class RecordController extends Controller
{
    use GetCompanyIdTrait;
    protected $chartService;

    public function __construct(ChartService $chartService)
    {
        $this->chartService = $chartService;
    }

    public function responseRatio(Request $request, $survey_term_id)
    {
        $data = SurveyPersonalAnswer::where('survey_term_id', $survey_term_id)
            ->select('employee_id')
            ->distinct()
            ->count('employee_id');

        $company_id = $this->getCompanyId($survey_term_id);

        $employeeCount = DB::table('employees')
            ->where('company_id', $company_id)
            ->count();

        $responseRatio = round($data / $employeeCount * 100, 2);

        return response()->json($responseRatio, 200);
    }

    public function recordList(Request $request, $company_id)
    {
        $data = SurveyCategory::where('company_id', $company_id)
            ->join('survey_terms as st', 'survey_categories.id', '=', 'st.survey_category_id')
            ->where('category', 0)
            ->orderBy('start_date', 'asc')
            ->select('st.id as survey_term_id', 'start_date')
            ->get();

        $data->transform(function ($item) {
            $item->year_month = Carbon::parse($item->start_date)->format('Y年n月');
            unset($item->start_date);
            return $item;
        });

        // ページネーションのために12個ずつ格納した多重配列を作成する
        $paginationData = $data->chunk(12);

        $paginatedData = [];
        foreach ($paginationData as $key => $chunk) {
            $paginatedData[$key + 1] = $chunk->toArray();
        }

        return response()->json($paginatedData, 200);
    }

    public function recordPlan(Request $request, $survey_term_id)
    {
        $plan = Plan::where('survey_term_id', $survey_term_id)
            ->where('category', 1)
            ->select('text')
            ->first();

        if ($plan) {
            $result = explode("\n", $plan->text);
            return response()->json($result, 200);
        } else {
            $question_averages = $this->chartService->questionAvg($survey_term_id);

            $content = '
            #命令

            あなたは組織改善コンサルタントです。
            以下の条件と入力文をもとに、優れた出力をしてください。

            #背景：

            従業員アンケートを行い、会社に対する従業員たちの意見が届いています。
            あなたはそれに対して人事部としての施策を立案します。

            #条件：

            ①施策は以下のフォーマットで最低2つの回答を作成して下さい。
            ・対処する問題 課題となっているところがどこかを明示（title）
            ・具体的な施策内容 300字以上（plan）
            ・施策によって見込まれる改善内容（improvement）
            ・経過観察の方法（observation）

            ②以下の形の配列で回答してください。
            {
                "id":
                "title":
                "plan":
                "improvement":
                "observation":
            }

            #回答：' . $question_averages;

            $yourApiKey = getenv('OPENAI_API_KEY');
            $client = OpenAI::client($yourApiKey);
            $result = $client->chat()->create([
                'model' => 'gpt-3.5-turbo-0125',
                'messages' => [
                    ['role' => 'user', 'content' => $content],
                ],
            ]);

            $data = $result->choices[0]->message->content;

            $plan = Plan::create([
                'survey_term_id' => $survey_term_id,
                'text' => $data,
                'category' => 1,
            ]);

            $result = explode("\n", $data);

            return response()->json($result, 200);
        }
    }

    // 良い点と悪い点の要約
    public function recordSummary(Request $request, $survey_term_id)
    {
        $summary = Summary::where('survey_term_id', $survey_term_id)
            ->select('summary')
            ->first();

        if ($summary) {
            $result = explode("\n", $summary->summary);

            return response()->json($result, 200);
        } else {
            $question_averages = $this->chartService->questionAvg($survey_term_id);
            $question_type_ratio = $this->chartService->questionTypeAvg($survey_term_id);
            $eNPS_ratio = $this->chartService->eNPSRatio($survey_term_id);

            $content = '
            #命令

            あなたは組織改善コンサルタントです。
            以下の条件と入力文をもとに、優れた出力をしてください。

            #背景：

            従業員アンケートを行い、会社に対する従業員たちの意見が届いています。
            あなたはそれに対して人事部としての施策を立案します。

            #条件：

            ①以下の形の配列でそれぞれ200文字以上で異なるものを回答してください。
            {
                "positive":
                "negative":
            }

            #回答：

            1~5までの選択肢の32個の設問の平均値の結果:' . $question_averages .
                '従業員のエンゲージメントと従業員が組織に対して持つ印象の比較:' . $question_type_ratio .
                'eNPSの割合:' . $eNPS_ratio;

            $yourApiKey = getenv('OPENAI_API_KEY');
            $client = OpenAI::client($yourApiKey);
            $result = $client->chat()->create([
                'model' => 'gpt-3.5-turbo-0125',
                'messages' => [
                    ['role' => 'user', 'content' => $content],
                ],
            ]);

            $data = $result->choices[0]->message->content;

            $summary = Summary::create([
                'survey_term_id' => $survey_term_id,
                'summary' => $data,
            ]);

            $result = explode("\n", $data);

            return response()->json($result, 200);
        }
    }
}
