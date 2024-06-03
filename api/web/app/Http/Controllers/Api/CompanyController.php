<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Company;

class CompanyController extends Controller
{
    // 一覧表示
    public function index()
    {
        $companies = Company::all();
        return response()->json([
            'data' => $companies
        ], 200);
    }

    // 登録
    public function store(Request $request)
    {
        $company = new Company();
        $company->name = $request->name;
        $company->save();
        return response()->json([
            'data' => $company
        ], 201);
    }

    // 指定のデータのみ取得
    public function edit($id)
    {
        $company = Company::find($id);
        return response()->json([
            'data' => $company
        ], 200);
    }

    // 更新
    public function update(Request $request, Company $company)
    {
        $company->fill($request->all());
        $company->save();
        return response()->json([
            'data' => $company
        ], 200);
    }

    // 削除
    public function delete(Company $company)
    {
        $company->delete();
        return response()->json([
            'message' => 'deleted successfully.'
        ], 200);
    }
}
