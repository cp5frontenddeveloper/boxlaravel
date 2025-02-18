<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ManufacturedBox;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ManufacturedBoxController extends Controller
{
    public function index()
    {
        try {
            $boxes = ManufacturedBox::with(['workshop', 'boxType'])->get();
            return response()->json([
                'message' => 'تم جلب الصناديق المصنعة بنجاح',
                'data' => $boxes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء جلب الصناديق المصنعة',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'invoice_number' => 'required|string',
            'workshop_id' => 'required|exists:workshops,id',
            'box_type_id' => 'required|exists:box_types,id',
            'quantity' => 'required|integer|min:1',
            'received_quantity' => 'required|integer|min:0',
            'order_date' => 'required|date',
            'actual_delivery_date' => 'nullable|date',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'بيانات غير صالحة',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $box = ManufacturedBox::create($request->all());
            return response()->json([
                'message' => 'تم إنشاء الصندوق المصنع بنجاح',
                'data' => $box
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء إنشاء الصندوق المصنع',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $box = ManufacturedBox::with(['workshop', 'boxType'])->findOrFail($id);
            return response()->json([
                'message' => 'تم جلب الصندوق المصنع بنجاح',
                'data' => $box
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء جلب الصندوق المصنع',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'invoice_number' => 'sometimes|required|string',
            'workshop_id' => 'sometimes|required|exists:workshops,id',
            'box_type_id' => 'sometimes|required|exists:box_types,id',
            'quantity' => 'sometimes|required|integer|min:1',
            'received_quantity' => 'sometimes|required|integer|min:0',
            'order_date' => 'sometimes|required|date',
            'actual_delivery_date' => 'nullable|date',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'بيانات غير صالحة',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $box = ManufacturedBox::findOrFail($id);
            $box->update($request->all());
            return response()->json([
                'message' => 'تم تحديث الصندوق المصنع بنجاح',
                'data' => $box
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء تحديث الصندوق المصنع',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $box = ManufacturedBox::findOrFail($id);
            $box->delete();
            return response()->json([
                'message' => 'تم حذف الصندوق المصنع بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء حذف الصندوق المصنع',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 