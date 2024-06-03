<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Employee;

class EmployeeController extends Controller
{
    // 一覧表示
    public function index() {
        $employees = Employee::all();
        return response()->json([
            'data' => $employees
        ], 200);
    }

    // 登録
    public function store(Request $request) {
        $employee = new Employee();
        $employee->name = $request->name;
        $employee->save();
        return response()->json([
            'data' => $employee
        ], 201);
    }

    // 指定のデータのみ取得
    public function edit($id) {
        $employee = Employee::find($id);
        return response()->json([
            'data' => $employee
        ], 200);
    }

    // 更新
    public function update(Request $request, Employee $employee) {
        $employee->fill($request->all());
        $employee->save();
        return response()->json([
            'data' => $employee
        ], 200);
    }

    // 削除
    public function delete(Employee $employee) {
        $employee->delete();
        return response()->json([
            'message' => 'deleted successfully.'
        ], 200);
    }
}

