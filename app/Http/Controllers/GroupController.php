<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;

class GroupController extends Controller
{
    public function generateRandomNumbers(Request $request)
{
    // عدد الأرقام في كل مجموعة
    $numberOfDigits = 100;
    // عدد المجموعات المراد إنشاؤها
    $numberOfGroups = 1; // يمكنك ضبط هذا العدد حسب الحاجة

    // تحقق إذا كان device_number موجودًا في الطلب
    if ($request->has('device_number')) {
        $deviceNumber = $request->input('device_number');

        // التحقق مما إذا كان device_number موجودًا بالفعل في جدول groups
        $existingGroup = Group::where('device_number', $deviceNumber)->first();

        // إذا كان device_number موجودًا، إعادة رسالة توضح أنه لا يمكن إنشاء المجموعة
        if ($existingGroup) {
            return response()->json([
                'message' => 'رقم الجهاز موجود بالفعل، لا يمكن إنشاء مجموعة جديدة.',
                'device_number' => $deviceNumber
            ], 400); // 400 تعني طلب غير صالح
        }
    }

    $createdGroups = []; // مصفوفة لتخزين تفاصيل المجموعات التي تم إنشاؤها

    for ($i = 0; $i < $numberOfGroups; $i++) {
        $numbers = [];
        for ($j = 0; $j < $numberOfDigits; $j++) {
            do {
                // توليد رقم عشوائي مكون من 4 أرقام بين 1000 و 9999
                $randomNumber = rand(1000, 9999);
            } while (strpos((string)$randomNumber, '0') !== false); // التحقق من عدم وجود صفر

            $numbers[] = $randomNumber;
        }

        // تحويل المصفوفة إلى سلسلة مفصولة بفواصل
        $numbersString = implode(',', $numbers);

        // إعداد البيانات للإدخال
        $data = ['numbers' => $numbersString];

        // إضافة userid إذا كان موجودًا في الطلب
        if ($request->has('userid')) {
            $data['userid'] = $request->input('userid');
        }

        // إضافة device_number إذا كان موجودًا في الطلب
        if ($request->has('device_number')) {
            $data['device_number'] = $request->input('device_number');
        }

        // حفظ المجموعة في جدول groups
        $group = Group::create($data);

        // تخزين تفاصيل المجموعة التي تم إنشاؤها
        $createdGroups[] = [
            'id' => $group->id,
            'numbers' => $group->numbers,
            'device_number' => $group->device_number,
        ];
    }

    // إعادة استجابة JSON مع بيانات المجموعات التي تم إنشاؤها
    return response()->json([
        'message' => 'تم إنشاء وحفظ المجموعات التي تحتوي على أرقام عشوائية بنجاح!',
        'groups' => $createdGroups,
    ]);
}


    // دالة لجلب البيانات على حسب device_number
    public function getByDeviceNumber(Request $request)
    {
        // التحقق من وجود device_number في الطلب
        if ($request->has('device_number')) {
            // جلب السجلات من جدول groups التي تتطابق مع device_number
            $groups = Group::where('device_number', $request->input('device_number'))->get();

            // إعادة الاستجابة بالبيانات
            return response()->json([
                'message' => 'تم جلب البيانات بنجاح',
                'groups' => $groups,
            ]);
        } else {
            // إذا لم يتم توفير device_number في الطلب
            return response()->json([
                'message' => 'يرجى توفير device_number في الطلب',
            ], 400);
        }
    }
    //========================================================delete device
    public function deleteByDeviceNumber(Request $request)
{
    // التحقق من وجود device_number في الطلب
    if ($request->has('device_number')) {
        // الحصول على device_number من الطلب
        $deviceNumber = $request->input('device_number');

        // حذف السجلات من جدول groups التي تتطابق مع device_number
        $deletedRows = Group::where('device_number', $deviceNumber)->delete();

        // التحقق من عدد السجلات التي تم حذفها
        if ($deletedRows > 0) {
            // إعادة استجابة JSON في حالة النجاح
            return response()->json([
                'state' => "successDelete",
            ]);
        } else {
            // إذا لم يتم العثور على أي سجلات تطابق device_number
            return response()->json([
                'message' => 'لم يتم العثور على أي سجلات تطابق device_number المحدد',
            ], 404);
        }
    } else {
        // إذا لم يتم توفير device_number في الطلب
        return response()->json([
            'message' => 'يرجى توفير device_number في الطلب',
        ], 400);
    }
}
//======================================================delete use user id
    //========================================================delete device
    public function deleteByDeviceNumberUserId(Request $request)
    {
        // التحقق من وجود userid في الطلب
        if ($request->has('userid')) {
            // الحصول على userid من الطلب
            $userid = $request->input('userid');

            // التأكد من أن userid ليس فارغًا أو غير صالح
            if (empty($userid)) {
                return response()->json([
                    'message' => 'قيمة userid غير صحيحة.',
                ], 400);
            }

            // حذف جميع السجلات من جدول groups التي تتطابق مع userid
            $deletedRows = Group::where('userid', $userid)->delete();

            // التحقق من عدد السجلات التي تم حذفها
            if ($deletedRows > 0) {
                // إعادة استجابة JSON في حالة النجاح
                return response()->json([
                    'state' => 'successDelete',
                    'deleted_rows' => $deletedRows,
                ]);
            } else {
                // إذا لم يتم العثور على أي سجلات تطابق userid
                return response()->json([
                    'message' => 'لم يتم العثور على أي سجلات تطابق userid المحدد.',
                ], 404);
            }
        } else {
            // إذا لم يتم توفير userid في الطلب
            return response()->json([
                'message' => 'يرجى توفير userid في الطلب.',
            ], 400);
        }
    }

}
