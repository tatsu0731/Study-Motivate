<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SurveyQuestionCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $survey_question_categories = [
            [
                'name' => '1 顧客基盤の安定性: 企業が長期間にわたって安定した顧客関係を築き、維持している状態。',
            ],
            [
                'name' => '2 理念戦略への納得感: 従業員が企業のビジョンや戦略に共感し、それに対する理解と信頼を持っていること。',
            ],
            [
                'name' => '3 社会的貢献: 企業が社会的責任を果たし、地域社会や社会全体へ積極的に貢献している行為。',
            ],
            [
                'name' => '4 責任と顧客・社会への貢献: 企業が顧客への約束を守り、社会に対しても積極的に貢献している姿勢。',
            ],
            [
                'name' => '5 連帯感と相互尊重: 従業員間での団結力と互いの価値観を尊重する文化があること。',
            ],
            [
                'name' => '6 魅力的な上司・同僚: 職場において、尊敬できる上司や魅力的な同僚がいること。',
            ],
            [
                'name' => '7 勤務地や会社設備の魅力: 勤務地の立地や会社の設備が充実していて働きやすい環境が整っていること。',
            ],
            [
                'name' => '8 評価・給与と柔軟な働き方: 公正な評価と適正な給与、柔軟な勤務体制が提供されていること。',
            ],
            [
                'name' => '9 顧客ニーズや事業戦略の伝達: 顧客の要望や企業の事業戦略が従業員に明確に伝えられていること。',
            ],
            [
                'name' => '10 上司や会社からの理解: 従業員の意見や状況に対して、上司や会社が理解と支持を示していること。',
            ],
            [
                'name' => '11 公平な評価:  従業員の業績や行動が公正な基準によって評価されていること。',
            ],
            [
                'name' => '12 上司からの適切な教育・支援: 上司が従業員の成長を支援し、必要な知識やスキルの提供を行っていること。',
            ],
            [
                'name' => '13 顧客の期待を上回る提案: 従業員が顧客の期待を超える提案やサービスを提供していること。',
            ],
            [
                'name' => '14 具体的な目標の共有: 会社の目標が明確であり、それが従業員と共有されていること。',
            ],
            [
                'name' => '15 未来に向けた活動: 企業が将来の成功に向けて戦略的な活動を行っていること。',
            ],
            [
                'name' => '16 ナレッジの標準化: 企業が持つ知識や情報が整理され、効率的に活用されていること。',
            ],
            [
                'name' => 'eNPS',
            ],
            [
                'name' => 'マンスリーアンケート常設設問',
            ],
            [
                'name' => 'その他',
            ]
        ];
        foreach ($survey_question_categories as $survey_question_category) {
            DB::table('survey_question_categories')->insert([
                'name' => $survey_question_category['name'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }
    }
}
