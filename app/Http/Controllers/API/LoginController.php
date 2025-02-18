<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        // التحقق من نوع تسجيل الدخول (بصمة أو تقليدي)
        if ($request->has('fingerprint_data')) {
            return $this->loginWithFingerprint($request);
        }

        // Validate the incoming request data for traditional login
        $validator = Validator::make($request->all(), [
            'login' => 'required|string|max:255',
            'password' => 'required|string|min:8',
        ]);

        // If validation fails, return errors
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // تحديد ما إذا كان المدخل بريد إلكتروني أو رقم هاتف
        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone_number';

        // البحث عن المستخدم إما بالبريد الإلكتروني أو رقم الهاتف
        $user = User::where($loginType, $request->login)->first();

        // If user is not found or password does not match
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        // Return a success response with the token
        return response()->json([
            'message' => 'Login successful',
            'data' => $user,
        ]);
    }

    private function loginWithFingerprint(Request $request)
    {
        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'fingerprint_data' => 'required|string|max:255',
            'login_type' => 'required|string|in:fingerprint'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'بيانات غير صالحة',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Find user by device fingerprint and ensure fingerprint login is enabled
            $user = User::where('fingerprint_data', $request->fingerprint_data)
                       ->where('use_fingerprint', true)
                       ->first();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'لم يتم العثور على مستخدم مسجل بهذه البصمة'
                ], 401);
            }

            // Generate API token for the user
            $token = $user->createToken('auth_token')->plainTextToken;

            // Update last login timestamp
            $user->update([
                'last_login_at' => now(),
                'last_login_type' => 'fingerprint'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'تم تسجيل الدخول بنجاح',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone_number' => $user->phone_number,
                        'use_fingerprint' => $user->use_fingerprint
                    ],
                    'token' => $token
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Fingerprint login error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء تسجيل الدخول',
            ], 500);
        }
    }
}

