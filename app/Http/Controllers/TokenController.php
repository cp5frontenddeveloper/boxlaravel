<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserToken;

class TokenController extends Controller
{
    public function updateOrCreateToken(Request $request)
    {
        // استرداد user_id من الطلب
        $userId = $request->input('user_id');
        $token = $request->input('token');
        $accessToken = $request->input('access_token');
        // تحقق إذا كانت البيانات موجودة بالفعل للمستخدم
        $userToken = UserToken::where('user_id', $userId)->first();

        if ($userToken) {
            // إذا كان التوكن أو الأكسس توكن مختلف، قم بالتحديث
            if ($userToken->token !== $token || $userToken->access_token !== $accessToken) {
                $userToken->update([
                    'token' => $token,
                    'access_token' => $accessToken,
                ]);
                return response()->json(['message' => 'Token and Access Token updated'], 200);
            } else {
                return response()->json(['message' => 'Token and Access Token are up to date'], 200);
            }
        } else {
            // إذا لم تكن البيانات موجودة، قم بإنشاء سجل جديد
            UserToken::create([
                'user_id' => $userId,
                'token' => $token,
                'access_token' => $accessToken,
            ]);
            return response()->json(['message' => 'Token and Access Token created'], 201);
        }
    }
    public function getTokenByUserId($userId)
    {
        // استرداد بيانات التوكن من خلال user_id
        $userToken = UserToken::where('user_id', $userId)->first();

        if ($userToken) {
            // إذا كانت البيانات موجودة، قم بإرجاعها
            return response()->json([
                'user_id' => $userToken->user_id,
                'token' => $userToken->token,
                'access_token' => $userToken->access_token,
            ], 200);
        } else {
            // إذا لم تكن البيانات موجودة، قم بإرجاع رسالة خطأ
            return response()->json(['message' => 'No token found for this user'], 404);
        }
    }
}
