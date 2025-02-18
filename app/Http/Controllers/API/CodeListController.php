<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CodeList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CodeListController extends Controller
{
    // جلب جميع قوائم الأكواد مع علاقات المستخدم والجهاز
    public function index()
    {
        return CodeList::with(['user', 'device'])->get();
    }

    // إنشاء قائمة أكواد جديدة
    public function store(Request $request)
    {
        // التحقق من صحة البيانات المدخلة
        $validator = Validator::make($request->all(), [
            'codes' => 'required|string',
            'user_id' => 'required|integer|exists:users,id',
            'device_id' => 'required|integer|exists:devices,id|unique:code_lists',
        ]);

        // إرجاع رسالة خطأ في حالة فشل التحقق
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // إنشاء قائمة الأكواد الجديدة
        $codeList = CodeList::create($request->all());

        // إرجاع البيانات مع رسالة نجاح
        return response()->json([
            'success' => true,
            'data' => $codeList
        ], 201);
    }

    // عرض قائمة أكواد محددة
    public function show($id)
    {
        $codeList = CodeList::with(['user', 'device'])->findOrFail($id);
        return response()->json($codeList);
    }

    // تحديث قائمة أكواد موجودة
    public function update(Request $request, $id)
    {
        // التحقق من صحة البيانات المحدثة
        $validator = Validator::make($request->all(), [
            'codes' => 'sometimes|required|string',
            'user_id' => 'sometimes|required|integer|exists:users,id',
            'device_id' => 'sometimes|required|integer|exists:devices,id|unique:code_lists,device_id,' . $id,
        ]);

        // إرجاع رسالة خطأ في حالة فشل التحقق
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // تحديث قائمة الأكواد
        $codeList = CodeList::findOrFail($id);
        $codeList->update($request->all());

        // إرجاع البيانات المحدثة مع رسالة نجاح
        return response()->json([
            'success' => true,
            'data' => $codeList
        ]);
    }

    // حذف قائمة أكواد محددة
    public function destroy($id)
    {
        CodeList::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}