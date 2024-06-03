<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\SurveyPersonalAnswer;

class SurveyPersonalAnswerController extends Controller
{
    // 一覧表示
    public function index() {
        $survey_personal_answers = SurveyPersonalAnswer::all();
        return response()->json([
            'data' => $survey_personal_answers
        ], 200);
    }

    // 登録
    public function store(Request $request) {

        $survey_personal_answer = new SurveyPersonalAnswer();

        //リクエストからデータを取得
        $employee_id = $request->input('employee_id');
        $survey_content_id= $request->input('survey_content_id');
        $survey_term_id = $request->input('survey_term_id');
        // department_id未記入
        // $department_id
        $gender = $request->input('gender');
        $years_of_service = $request->input('years_of_service');
        $age = $request->input('age');

        $survey_personal_answer-> employee_id = $employee_id ;
        $survey_personal_answer-> survey_content_id =$survey_content_id;
        $survey_personal_answer-> survey_term_id =$survey_term_id;
        $survey_personal_answer-> department_id =1;
        $survey_personal_answer-> gender =$gender;
        $survey_personal_answer-> years_of_service =$years_of_service;
        $survey_personal_answer-> age =$age;
// personalIdを取得して定義。

        $survey_personal_answer->save();
        return response()->json([
            'data' => $survey_personal_answer
        ], 201);
    }

    // 指定のデータのみ取得
    public function edit($id) {
        $survey_personal_answer = SurveyPersonalAnswer::find($id);
        return response()->json([
            'data' => $survey_personal_answer
        ], 200);
    }

    // 更新
    public function update(Request $request, SurveyPersonalAnswer $survey_personal_answer) {
        $survey_personal_answer->fill($request->all());
        $survey_personal_answer->save();
        return response()->json([
            'data' => $survey_personal_answer
        ], 200);
    }

    // 削除
    public function delete(SurveyPersonalAnswer $survey_personal_answer) {
        $survey_personal_answer->delete();
        return response()->json([
            'message' => 'deleted successfully.'
        ], 200);
    }
}
