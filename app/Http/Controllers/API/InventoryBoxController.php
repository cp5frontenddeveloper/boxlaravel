<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\InventoryBox;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InventoryBoxController extends Controller
{
    public function index()
    {
        try {
            $boxes = InventoryBox::with(['workshop', 'boxType', 'boxStatus'])->get();
            return response()->json([
                'message' => 'تم جلب الصناديق بنجاح',
                'data' => $boxes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء جلب الصناديق',
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
            'box_status_id' => 'required|exists:box_statuses,id',
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
            $box = InventoryBox::create($request->all());
            return response()->json([
                'message' => 'تم إنشاء الصندوق بنجاح',
                'data' => $box
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء إنشاء الصندوق',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $box = InventoryBox::with(['workshop', 'boxType', 'boxStatus'])->findOrFail($id);
            return response()->json([
                'message' => 'تم جلب الصندوق بنجاح',
                'data' => $box
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء جلب الصندوق',
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
            'box_status_id' => 'sometimes|required|exists:box_statuses,id',
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
            $box = InventoryBox::findOrFail($id);
            $box->update($request->all());
            return response()->json([
                'message' => 'تم تحديث الصندوق بنجاح',
                'data' => $box
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء تحديث الصندوق',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $box = InventoryBox::findOrFail($id);
            $box->delete();
            return response()->json([
                'message' => 'تم حذف الصندوق بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء حذف الصندوق',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 