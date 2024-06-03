<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Mail\InvitationEmail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class InvitationController extends Controller
{
    public function sendInvitationEmail(Request $request)
    {
        $email = $request->input('email');
        $company_id = $request->input('company_id');

        // トークンの有効期限を設定（例：1日間）
        $expiration = Carbon::now()->addDay()->format('Y-m-d');
        $token = hash('sha256', $email . $company_id . $expiration);

        // 招待メールを送信する処理
        $invitationLink = 'http://localhost:3000/register?email=' . $email . '&company_id=' . $company_id . '&token=' . $token;

        Mail::to($email)->send(new InvitationEmail($invitationLink));

        return response()->json(['message' => '招待メールが送信されました']);
    }
}
