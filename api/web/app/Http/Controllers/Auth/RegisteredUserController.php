<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\Admin;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $receivedToken = $request->input('token');
        $email = $request->input('email');
        $company_id = $request->input('company_id');

        // トークンの有効期限を再度計算
        $expiration = Carbon::now()->addDay()->format('Y-m-d');
        $expectedToken = hash('sha256', $email . $company_id . $expiration);

        // 受信したトークンと期待されるトークンを比較
        if ($receivedToken === $expectedToken) {
            // 有効期限内の場合は登録処理を実行

            $request->validate([
                'company_id' => ['required', 'integer', 'exists:' . Company::class . ',id'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . Admin::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            $admin = Admin::create([
                'company_id' => $request->company_id,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            event(new Registered($admin));

            Auth::login($admin);

            return response()->json(['message' => '登録が完了しました']);
        } else {
            // 有効期限切れの場合はエラーメッセージを返す
            return response()->json(['error' => '有効期限切れのトークンです'], 400);
        }
    }
}
