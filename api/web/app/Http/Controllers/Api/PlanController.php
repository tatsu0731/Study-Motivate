<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Plan;

class PlanController extends Controller
{
    // 一覧表示
    public function index()
    {
        $plans = Plan::all();
        return response()->json([
            'data' => $plans
        ], 200);
    }

    public function store(Request $request) {
        // 登録
        $plan = Plan::create([
            'survey_term_id' => $request->survey_term_id,
            'text' => $request->text,
        ]);
        return response()->json([
            'data' => $plan
        ], 201);
    }

    // 指定のデータのみ取得
    public function edit($id)
    {
        $plan = Plan::find($id);
        return response()->json([
            'data' => $plan
        ], 200);
    }

    // 更新
    public function update(Request $request, Plan $plan)
    {
        $plan->fill($request->all());
        $plan->save();
        return response()->json([
            'data' => $plan
        ], 200);
    }

    // 削除
    public function delete(Plan $plan)
    {
        $plan->delete();
        return response()->json([
            'message' => 'deleted successfully.'
        ], 200);
    }
}
