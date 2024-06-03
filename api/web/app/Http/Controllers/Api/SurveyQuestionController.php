<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\SurveyQuestion;

class SurveyQuestionController extends Controller
{
    // 一覧表示
    public function index() {
        $survey_questions = SurveyQuestion::all();
        return response()->json([
            'data' => $survey_questions
        ], 200);
    }

    // 登録
    public function store(Request $request) {
        $survey_question = new SurveyQuestion();
        $survey_question->name = $request->name;
        $survey_question->save();
        return response()->json([
            'data' => $survey_question
        ], 201);
    }

    // 指定のデータのみ取得
    public function edit($id) {
        $survey_question = SurveyQuestion::find($id);
        return response()->json([
            'data' => $survey_question
        ], 200);
    }

    // 更新
    public function update(Request $request, SurveyQuestion $survey_question) {
        $survey_question->fill($request->all());
        $survey_question->save();
        return response()->json([
            'data' => $survey_question
        ], 200);
    }

    // 削除
    public function delete(SurveyQuestion $survey_question) {
        $survey_question->delete();
        return response()->json([
            'message' => 'deleted successfully.'
        ], 200);
    }
}

