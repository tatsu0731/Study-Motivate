<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // ユーザー登録
    public function register(Request $request) {
        $admin = Admin::create([
            'company_id' => $request->company_id,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $json = [
            'data' => $admin
        ];
        return response()->json( $json, Response::HTTP_OK);
    }

    // ログイン
    public function login(Request $request) {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $admin = Admin::whereEmail($request->email)->first();
            $admin->tokens()->delete();
            $token = $admin->createToken("login:admin{$admin->id}")->plainTextToken;
            //ログインが成功した場合はトークンを返す
            return response()->json(['token' => $token], Response::HTTP_OK);
        }
        return response()->json('Can Not Login.', Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
