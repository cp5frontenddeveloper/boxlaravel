<?php

namespace App\Http\Controllers\API;

use App\Models\Subuser;
use App\Models\User;
use App\Models\Device;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubuserController extends Controller
{
    public function index()
    {
        return Subuser::with('user')->get();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'const_code' => 'required|integer',
            'user_id' => 'required|integer|exists:users,id',
            'email' => 'nullable|email|max:255|unique:subusers,email',
            'phone' => 'nullable|string|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user = Subuser::create([
            'username' => $request->username,
            'const_code' => $request->const_code,
            'user_id' => $request->user_id,
            'device_id' => $request->device_id,
            'email' => $request->email,
            'phone' => $request->phone
        ]);

        return response()->json($user, 201);
    }

    public function show(Request $request)
    { $userId = $request->query('user_id');

        if ($userId) {
            $subusers = Subuser::with('user')->where('user_id', $userId)->get();
        } else {
            $subusers = Subuser::with('user')->get();
        }

        return response()->json($subusers);
    }

    public function update(Request $request, $id)
    {
        $subuser = Device::find($id);

        if (!$subuser) {
            return response()->json(['error' => 'subuser not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:subusers,email,' . $id,
            'user_id' => 'sometimes|required|integer|exists:users,id',
        ]);

        $subuser->update($request->all());

        return response()->json($subuser);
    }

    public function destroy($id)
    {
        Subuser::findOrFail($id)->delete();
        if(!$Subuser){
            return response()->json(null, 204);
        }
        return response()->json(['message' => 'subuser deleted successfully']);
    }
    public function deleteByDeviceNumberdeviceId(Request $request)
    {
        // التحقق من وجود userid في الطلب
        if ($request->has('device_id')) {
            // الحصول على userid من الطلب
            $deviceid = $request->input('device_id');

            // التأكد من أن userid ليس فارغًا أو غير صالح
            if (empty($deviceid)) {
                return response()->json([
                    'message' => 'قيمة deviceid غير صحيحة.',
                ], 400);
            }

            // حذف جميع السجلات من جدول groups التي تتطابق مع userid
            $deletedRows = Subuser::where('device_id', $deviceid)->delete();

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
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'const_code' => 'required|string|size:4',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'الرجاء إدخال البريد الإلكتروني والرمز بشكل صحيح',
                'errors' => $validator->errors()
            ], 422);
        }

        $subuser = Subuser::where('email', $request->email)
                         ->where('const_code', $request->const_code)
                         ->first();

        if (!$subuser) {
            return response()->json([
                'status' => 'error',
                'message' => 'البريد الإلكتروني أو الرمز غير صحيح'
            ], 401);
        }

        // Get device information
        $device = Device::find($subuser->device_id);

        return response()->json([
            'status' => 'success',
            'message' => 'تم تسجيل الدخول بنجاح',
            'data' => [
                'username' => $subuser->username,
                'const_code' => $subuser->const_code,
                'user_id' => $subuser->user_id,
                'device_id' => $subuser->device_id,
                'email' => $subuser->email,
                'device' => $device
            ]
        ]);
    }
}
