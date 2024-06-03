<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\SurveyQuestionCategory;

class SurveyQuestionCategoryController extends Controller
{
    // 一覧表示
    public function index() {
        $survey_question_categories = SurveyQuestionCategory::all();
        return response()->json([
            'data' => $survey_question_categories
        ], 200);
    }

    // 登録
    public function store(Request $request) {
        $survey_question_category = new SurveyQuestionCategory();
        $survey_question_category->name = $request->name;
        $survey_question_category->save();
        return response()->json([
            'data' => $survey_question_category
        ], 201);
    }

    // 指定のデータのみ取得
    public function edit($id) {
        $survey_question_category = SurveyQuestionCategory::find($id);
        return response()->json([
            'data' => $survey_question_category
        ], 200);
    }

    // 更新
    public function update(Request $request, SurveyQuestionCategory $survey_question_category) {
        $survey_question_category->fill($request->all());
        $survey_question_category->save();
        return response()->json([
            'data' => $survey_question_category
        ], 200);
    }

    // 削除
    public function delete(SurveyQuestionCategory $survey_question_category) {
        $survey_question_category->delete();
        return response()->json([
            'message' => 'deleted successfully.'
        ], 200);
    }
}

