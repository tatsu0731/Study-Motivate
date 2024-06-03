<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\SurveyCategory;

class SurveyCategoryController extends Controller
{
    // 一覧表示
    public function index() {
        $survey_categories = SurveyCategory::all();
        return response()->json([
            'data' => $survey_categories
        ], 200);
    }

    // 登録
    public function store(Request $request) {
        for($i = 0; $i <= 1; $i++){
            $survey_category = new SurveyCategory();
            $survey_category->company_id = (int) $request->company_id;
            $survey_category->name = $request->name;
            $survey_category->frequency = (int) $request->frequency;
            $survey_category->category = $i;
            $survey_category->status = 0;
            $survey_category->save();
        }
        return response()->json([
            'data' => $survey_category
        ], 201);
    }

    // 指定のデータのみ取得
    public function edit($id) {
        $survey_category = SurveyCategory::find($id);
        return response()->json([
            'data' => $survey_category
        ], 200);
    }

    // 更新
    public function update(Request $request, SurveyCategory $survey_category) {
        $survey_category->fill($request->all());
        $survey_category->save();
        return response()->json([
            'data' => $survey_category
        ], 200);
    }

    // 削除
    public function delete(SurveyCategory $survey_category) {
        $survey_category->delete();
        return response()->json([
            'message' => 'deleted successfully.'
        ], 200);
    }
}