<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\SurveyTerm;

use Carbon\Carbon;
use App\Models\SurveyCategory;

class SurveyTermController extends Controller
{
    // 一覧表示
    public function index() {
        $survey_terms = SurveyTerm::all();
        return response()->json([
            'data' => $survey_terms
        ], 200);
    }

    // 登録
    public function store(Request $request) {
        $survey_term = new SurveyTerm();
        $survey_term->survey_category_id = $request->survey_category_id;
        $survey_term->start_date = $request->start_date;
        $survey_term->deadline = $request->deadline;
        $survey_term->count = $request->count;
        $survey_term->save();

        $survey_category = SurveyCategory::where("id", $request->survey_category_id)->where("category", 1)->first();
        for($i = 0; $i <= (int)$survey_category["frequency"] - 1; $i++) {
            $survey_term = new SurveyTerm();
            $survey_term->survey_category_id = $request->survey_category_id;
            $survey_term->start_date = date("Y-m-d",strtotime($request->start_date . "+$i month"));
            $survey_term->deadline = $request->deadline;
            $survey_term->count = $request->count;
            $survey_term->save();
        }

        return response()->json([
            'data' => $survey_term
        ], 201);
    }

    // 指定のデータのみ取得
    public function edit($id) {
        $survey_term = SurveyTerm::find($id);
        return response()->json([
            'data' => $survey_term
        ], 200);
    }

    // 更新
    public function update(Request $request, SurveyTerm $survey_term) {
        $survey_term->fill($request->all());
        $survey_term->save();
        return response()->json([
            'data' => $survey_term
        ], 200);
    }

    // 削除
    public function delete(SurveyTerm $survey_term) {
        $survey_term->delete();
        return response()->json([
            'message' => 'deleted successfully.'
        ], 200);
    }
}

