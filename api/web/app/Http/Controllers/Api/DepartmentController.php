<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Department;
use App\Models\SurveyCategory;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    // 一覧表示
    public function index()
    {
        $departments = Department::all();
        return response()->json([
            'data' => $departments
        ], 200);
    }

    public function departmentsPerCompany($company_id)
    {
        $departments = Department::where('company_id', $company_id)
            ->select('id as department_id', 'name as department_name', 'total')
            ->get();
        return response()->json([
            'data' => $departments
        ], 200);
    }

    public function departmentsPerCompanyFromSurveyCategoryId($survey_category_id)
    {
        $departments = SurveyCategory::where('survey_categories.id', $survey_category_id)
            ->join('companies', 'survey_categories.company_id', '=', 'companies.id')
            ->join('departments', 'companies.id', '=', 'departments.company_id')
            ->select('departments.id as department_id', 'departments.name as department_name')
            ->get();

        return response()->json([
            'data' => $departments
        ], 200);
    }

    // 登録
    public function store(Request $request)
    {
        $department = new Department();
        $department->name = $request->department;
        $department->company_id = $request->company_id;
        $department->total = $request->total;
        $department->save();
        return response()->json([
            'data' => $department
        ], 201);
    }

    // 指定のデータのみ取得
    public function edit($id)
    {
        $department = Department::find($id);
        return response()->json([
            'data' => $department
        ], 200);
    }

    // 更新
    public function update(Request $request, Department $department)
    {
        $department->fill($request->all());
        $department->save();
        return response()->json([
            'data' => $department
        ], 200);
    }

    // 削除
    public function delete(Department $department)
    {
        $department->delete();
        return response()->json([
            'message' => 'deleted successfully.'
        ], 200);
    }
}
