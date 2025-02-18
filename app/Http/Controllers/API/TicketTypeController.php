<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TicketTypeController extends Controller
{
    public function index()
    {
        try {
            $ticketTypes = TicketType::where('is_active', true)->get();
            
            return response()->json([
                'message' => 'تم جلب أنواع التذاكر بنجاح',
                'data' => $ticketTypes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء جلب أنواع التذاكر',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:50|unique:ticket_types',
                'description' => 'nullable|string',
                'is_active' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'بيانات غير صالحة',
                    'errors' => $validator->errors()
                ], 422);
            }

            $ticketType = TicketType::create($request->all());
            
            return response()->json([
                'message' => 'تم إنشاء نوع التذكرة بنجاح',
                'data' => $ticketType
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء إنشاء نوع التذكرة',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $ticketType = TicketType::findOrFail($id);
            
            return response()->json([
                'message' => 'تم جلب نوع التذكرة بنجاح',
                'data' => $ticketType
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء جلب نوع التذكرة',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'string|max:50|unique:ticket_types,name,' . $id,
                'description' => 'nullable|string',
                'is_active' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'بيانات غير صالحة',
                    'errors' => $validator->errors()
                ], 422);
            }

            $ticketType = TicketType::findOrFail($id);
            $ticketType->update($request->all());
            
            return response()->json([
                'message' => 'تم تحديث نوع التذكرة بنجاح',
                'data' => $ticketType
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء تحديث نوع التذكرة',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $ticketType = TicketType::findOrFail($id);
            $ticketType->delete();
            
            return response()->json([
                'message' => 'تم حذف نوع التذكرة بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء حذف نوع التذكرة',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 