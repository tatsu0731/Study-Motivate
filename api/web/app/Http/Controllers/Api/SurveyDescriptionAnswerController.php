<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\SurveyDescriptionAnswer;

class SurveyDescriptionAnswerController extends Controller
{
    // 一覧表示
    public function index() {
        $survey_description_answers = SurveyDescriptionAnswer::all();
        return response()->json([
            'data' => $survey_description_answers
        ], 200);
    }

    // 登録
    public function store(Request $request) {
        $survey_description_answer = new SurveyDescriptionAnswer();
        $survey_description_answer->name = $request->name;
        $survey_description_answer->save();
        return response()->json([
            'data' => $survey_description_answer
        ], 201);
    }

    // 指定のデータのみ取得
    public function edit($id) {
        $survey_description_answer = SurveyDescriptionAnswer::find($id);
        return response()->json([
            'data' => $survey_description_answer
        ], 200);
    }

    // 更新
    public function update(Request $request, SurveyDescriptionAnswer $survey_description_answer) {
        $survey_description_answer->fill($request->all());
        $survey_description_answer->save();
        return response()->json([
            'data' => $survey_description_answer
        ], 200);
    }

    // 削除
    public function delete(SurveyDescriptionAnswer $survey_description_answer) {
        $survey_description_answer->delete();
        return response()->json([
            'message' => 'deleted successfully.'
        ], 200);
    }
}

