<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Goal;

class GoalController extends Controller
{
    // 一覧表示
    public function index() {
        $goals = Goal::all();
        return response()->json([
            'data' => $goals
        ], 200);
    }

    // 登録
    public function store(Request $request) {
        $goal = new Goal();
        $goal->name = $request->name;
        $goal->save();
        return response()->json([
            'data' => $goal
        ], 201);
    }

    // 指定のデータのみ取得
    public function edit($id) {
        $goal = Goal::find($id);
        return response()->json([
            'data' => $goal
        ], 200);
    }

    // 更新
    public function update(Request $request, Goal $goal) {
        $goal->fill($request->all());
        $goal->save();
        return response()->json([
            'data' => $goal
        ], 200);
    }

    // 削除
    public function delete(Goal $goal) {
        $goal->delete();
        return response()->json([
            'message' => 'deleted successfully.'
        ], 200);
    }
}

