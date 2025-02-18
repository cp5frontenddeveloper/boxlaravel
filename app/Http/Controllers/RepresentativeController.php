<?php

namespace App\Http\Controllers;

use App\Models\Representative;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RepresentativeController extends Controller
{
    public function index()
    {
        try {
            $representatives = Representative::all();
            return response()->json([
                'status' => true,
                'data' => $representatives
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء جلب البيانات'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'phone_number' => 'required|string|unique:representatives',
                'email' => 'required|email|unique:representatives',
                'password' => 'required|string|min:6',
            ], [
                'name.required' => 'الاسم مطلوب',
                'phone_number.required' => 'رقم الهاتف مطلوب',
                'phone_number.unique' => 'رقم الهاتف مسجل مسبقاً',
                'email.required' => 'البريد الإلكتروني مطلوب',
                'email.unique' => 'البريد الإلكتروني مسجل مسبقاً',
                'password.required' => 'كلمة المرور مطلوبة',
                'password.min' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'خطأ في البيانات',
                    'errors' => $validator->errors()
                ], 422);
            }

            $representative = Representative::create([
                'name' => $request->name,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            return response()->json([
                'status' => true,
                'message' => 'تم إنشاء الممثل بنجاح',
                'representative' => $representative
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء إنشاء الممثل'
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required'
            ], [
                'email.required' => 'البريد الإلكتروني مطلوب',
                'email.email' => 'البريد الإلكتروني غير صحيح',
                'password.required' => 'كلمة المرور مطلوبة'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'خطأ في البيانات',
                    'errors' => $validator->errors()
                ], 422);
            }

            $representative = Representative::where('email', $request->email)->first();

            if (!$representative || !Hash::check($request->password, $representative->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'بيانات الدخول غير صحيحة'
                ], 401);
            }

            $representative->tokens()->delete();
            $token = $representative->createToken('representative-token')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'تم تسجيل الدخول بنجاح',
                'token' => $token,
                'representative' => $representative
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء تسجيل الدخول'
            ], 500);
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:representatives,email',
                'new_password' => 'required|string|min:6',
            ], [
                'email.exists' => 'البريد الإلكتروني غير مسجل في النظام',
                'email.required' => 'البريد الإلكتروني مطلوب',
                'new_password.required' => 'كلمة المرور الجديدة مطلوبة',
                'new_password.min' => 'كلمة المرور الجديدة يجب أن تكون 6 أحرف على الأقل'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'خطأ في البيانات',
                    'errors' => $validator->errors()
                ], 422);
            }

            $representative = Representative::where('email', $request->email)->first();
            
            $representative->update([
                'password' => Hash::make($request->new_password)
            ]);

            $representative->tokens()->delete();

            return response()->json([
                'status' => true,
                'message' => 'تم تحديث كلمة المرور بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء تحديث كلمة المرور'
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();
            return response()->json([
                'status' => true,
                'message' => 'تم تسجيل الخروج بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء تسجيل الخروج'
            ], 500);
        }
    }
}