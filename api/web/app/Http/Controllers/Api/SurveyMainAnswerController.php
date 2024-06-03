<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Models\SurveyContent;
use App\Models\SurveyMainAnswer;
use App\Models\SurveyMonthlyAnswer;
use App\Http\Controllers\Controller;
use App\Models\SurveyPersonalAnswer;
use App\Models\SurveyDescriptionAnswer;

class SurveyMainAnswerController extends Controller
{
    // 一覧表示
    public function index()
    {
        $survey_main_answers = SurveyMainAnswer::all();
        return response()->json([
            'data' => $survey_main_answers
        ], 200);
    }

    // 登録
    public function store(Request $request)
    {
        // リクエスト,パラメータからデータを取得
        $employee_id = $request->employee_id;
        $survey_term_id = $request->survey_term_id;
        $gender = $request->gender;
        $years_of_service = $request->years_of_service;
        $age = $request->age;
        $answers = $request->answers;
        $additionalAnswers = $request->additionalAnswers;
        $freeDescription = $request->free_description;
        $department_id = $request->department_id;

        //survey_personal_answersテーブルとsurvey_main_answersテーブルの両者に設問数分データが挿入される。
        //前者はcontent_idの影響、後者はそれに基づいたpersonal_idの影響

        // 改善：設問数のlengthでfor文を回す。
        foreach ($answers as $i => $answer) {
            $survey_personal_answer = new SurveyPersonalAnswer();
            // answers配列
            // $i+1は質問番号、answers[$i]は常設質問番号$i+1の回答内容に対応している。
            // ----------
            //survey_category_idは後ほどパラメータ等から取得する
            // ----------
            // //企業ごと、設問ごとのidを取得

            //survey_personal_answersにデータ挿入
            $survey_personal_answer->employee_id = $employee_id;
            $survey_personal_answer->survey_content_id = $answer['question_content_id'];
            $survey_personal_answer->survey_term_id = $answer['survey_term_id'];
            // --department_idがダミーデータではnullであるから、検証時には挿入する必要がある。--
            $survey_personal_answer->department_id = $department_id;
            $survey_personal_answer->gender = $gender;
            $survey_personal_answer->years_of_service = $years_of_service;
            $survey_personal_answer->age = $age;

            // genderなどの情報を保存
            $survey_personal_answer->save();

            $personal_answer_id = $survey_personal_answer->id;

            $survey_main_answer = new SurveyMainAnswer();
            $survey_main_answer->answer = $answer['answer'];
            $survey_main_answer->survey_personal_answer_id = $personal_answer_id;
            $survey_main_answer->save();
        }

        // 追加質問の回答を保存
        foreach ($additionalAnswers as $additionalAnswer) {
            $survey_personal_answer = new SurveyPersonalAnswer();
            $survey_personal_answer->employee_id = $employee_id;
            $survey_personal_answer->survey_content_id = $additionalAnswer['question_content_id'];
            $survey_personal_answer->survey_term_id = $answer['survey_term_id'];
            $survey_personal_answer->department_id = $department_id;
            $survey_personal_answer->gender = $gender;
            $survey_personal_answer->years_of_service = $years_of_service;
            $survey_personal_answer->age = $age;
            $survey_personal_answer->save();

            $personal_answer_id = $survey_personal_answer->id;

            $survey_monthly_answer = new SurveyMonthlyAnswer();
            $survey_monthly_answer->answer = $additionalAnswer['answer'];
            $survey_monthly_answer->survey_personal_answer_id = $personal_answer_id;
            $survey_monthly_answer->save();
        }

        if ($freeDescription !== null) {
            // フリーテキストの回答を保存
            $survey_personal_answer = new SurveyPersonalAnswer();
            $survey_personal_answer->employee_id = $employee_id;
            $survey_personal_answer->survey_content_id = $additionalAnswer['question_content_id'];
            $survey_personal_answer->survey_term_id = $answer['survey_term_id'];
            $survey_personal_answer->department_id = $department_id;
            $survey_personal_answer->gender = $gender;
            $survey_personal_answer->years_of_service = $years_of_service;
            $survey_personal_answer->age = $age;
            $survey_personal_answer->save();

            $personal_answer_id = $survey_personal_answer->id;

            $survey_description = new SurveyDescriptionAnswer();
            $survey_description->answer = $freeDescription;
            $survey_description->survey_personal_answer_id = $personal_answer_id;
            $survey_description->save();
        }


        // $survey_main_answer->name = $request->name;
        return response()->json([
            'data' => $survey_monthly_answer
        ], 201);
    }

    // 指定のデータのみ取得
    public function edit($id)
    {
        $survey_main_answer = SurveyMainAnswer::find($id);
        return response()->json([
            'data' => $survey_main_answer
        ], 200);
    }

    // 更新
    public function update(Request $request, SurveyMainAnswer $survey_main_answer)
    {
        $survey_main_answer->fill($request->all());
        $survey_main_answer->save();
        return response()->json([
            'data' => $survey_main_answer
        ], 200);
    }

    // 削除
    public function delete(SurveyMainAnswer $survey_main_answer)
    {
        $survey_main_answer->delete();
        return response()->json([
            'message' => 'deleted successfully.'
        ], 200);
    }
}
