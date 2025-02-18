<?php

namespace App\Http\Controllers;

use App\Models\AdminCommunicationLog;
use Illuminate\Http\Request;
use App\Models\Representative;
class AdminCommunicationLogController extends Controller
{
    // عرض جميع السجلات
    public function index()
    {
        return AdminCommunicationLog::with('representative')->get();
    }

    // إنشاء سجل جديد
    public function store(Request $request)
    {
        // التحقق من البيانات المرسلة
        $validatedData = $request->validate([
            'representative_id' => 'nullable|exists:representatives,id',
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'note' => 'nullable|string',
        ]);

        // تعيين isNew تلقائيًا إلى true
        $validatedData['isNew'] = true;

        // إنشاء السجل
        $log = AdminCommunicationLog::create($validatedData);
        
        return response()->json([
            'status' => true,
            'message' => 'تم إنشاء السجل بنجاح',
            'log' => $log->load('representative')
        ], 201);
    }

    // عرض سجل محدد
    public function show(AdminCommunicationLog $adminCommunicationLog)
    {
        return $adminCommunicationLog->load('representative');
    }

    // تحديث سجل محدد
    public function update(Request $request, AdminCommunicationLog $adminCommunicationLog)
    {
        // التحقق من البيانات المرسلة
        $validatedData = $request->validate([
            'representative_id' => 'nullable|exists:representatives,id',
            'title' => 'sometimes|string|max:255',
            'date' => 'sometimes|date',
            'note' => 'nullable|string',
        ]);

        // تعيين isNew تلقائيًا إلى false
        $validatedData['isNew'] = false;

        // تحديث السجل
        $adminCommunicationLog->update($validatedData);
        
        return response()->json([
            'status' => true,
            'message' => 'تم تحديث السجل بنجاح',
            'log' => $adminCommunicationLog->load('representative')
        ]);
    }

    // حذف سجل محدد
    public function destroy(AdminCommunicationLog $adminCommunicationLog)
    {
        $adminCommunicationLog->delete();
        return response()->json([
            'status' => true,
            'message' => 'تم حذف السجل بنجاح'
        ]);
    }

    // الحصول على إشعارات المندوب
    public function getRepresentativeLogs($representative_id)
    {
        $notifications = AdminCommunicationLog::with('representative')
            ->where('representative_id', $representative_id)
            ->orderBy('date', 'desc')
            ->select([
                'id',
                'representative_id',
                'title',
                'date',
                'isNew',
                'note'
            ])
            ->get();

        // تحديث حالة الإشعارات لتصبح مقروءة
        AdminCommunicationLog::where('representative_id', $representative_id)
            ->where('isNew', true)
            ->update(['isNew' => false]);

        return response()->json([
            'status' => true,
            'message' => 'تم جلب الإشعارات بنجاح',
            'notifications' => $notifications,
            'unread_count' => $notifications->where('isNew', true)->count()
        ]);
    }
}