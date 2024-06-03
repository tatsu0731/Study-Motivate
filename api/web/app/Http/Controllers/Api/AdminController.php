<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Admin;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // 一覧表示
    public function index() {
        $admins = Admin::all();
        return response()->json([
            'data' => $admins
        ], 200);
    }

    // 登録
    public function store(Request $request) {
        $admin = new Admin();
        $admin->name = $request->name;
        $admin->save();
        return response()->json([
            'data' => $admin
        ], 201);
    }

    // 指定のデータのみ取得
    public function edit($id) {
        $admin = Admin::find($id);
        return response()->json([
            'data' => $admin
        ], 200);
    }

    // 更新
    public function update(Request $request, Admin $admin) {
        $admin->fill($request->all());
        $admin->save();
        return response()->json([
            'data' => $admin
        ], 200);
    }

    // 削除
    public function delete(Admin $admin) {
        $admin->delete();
        return response()->json([
            'message' => 'deleted successfully.'
        ], 200);
    }
}
