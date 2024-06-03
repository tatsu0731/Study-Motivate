<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Report;

class ReportController extends Controller
{
    // 一覧表示
    public function index() {
        $reports = Report::all();
        return response()->json([
            'data' => $reports
        ], 200);
    }

    // 登録
    public function store(Request $request) {
        $report = new Report();
        $report->name = $request->name;
        $report->save();
        return response()->json([
            'data' => $report
        ], 201);
    }

    // 指定のデータのみ取得
    public function edit($id) {
        $report = Report::find($id);
        return response()->json([
            'data' => $report
        ], 200);
    }

    // 更新
    public function update(Request $request, Report $report) {
        $report->fill($request->all());
        $report->save();
        return response()->json([
            'data' => $report
        ], 200);
    }

    // 削除
    public function delete(Report $report) {
        $report->delete();
        return response()->json([
            'message' => 'deleted successfully.'
        ], 200);
    }
}

