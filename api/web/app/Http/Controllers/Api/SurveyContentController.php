<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\SurveyContent;

class SurveyContentController extends Controller
{
    // 一覧表示
    public function index() {
        $survey_contents = SurveyContent::all();
        return response()->json([
            'data' => $survey_contents
        ], 200);
    }

    // 登録
    public function store(Request $request) {
        $survey_content = new SurveyContent();
        $survey_content->name = $request->name;
        $survey_content->save();
        return response()->json([
            'data' => $survey_content
        ], 201);
    }

    // 指定のデータのみ取得
    public function edit($id) {
        $survey_content = SurveyContent::find($id);
        return response()->json([
            'data' => $survey_content
        ], 200);
    }

    // 更新
    public function update(Request $request, SurveyContent $survey_content) {
        $survey_content->fill($request->all());
        $survey_content->save();
        return response()->json([
            'data' => $survey_content
        ], 200);
    }

    // 削除
    public function delete(SurveyContent $survey_content) {
        $survey_content->delete();
        return response()->json([
            'message' => 'deleted successfully.'
        ], 200);
    }
}

