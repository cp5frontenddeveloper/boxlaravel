<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\TicketUpdate;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TicketUpdateController extends Controller
{
    public function index($ticket_id)
    {
        try {
            $updates = TicketUpdate::where('ticket_id', $ticket_id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'message' => 'تم جلب التحديثات بنجاح',
                'data' => $updates
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء جلب التحديثات',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request, $ticket_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:new,in_progress,waiting,closed',
                'text' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'بيانات غير صالحة',
                    'errors' => $validator->errors()
                ], 422);
            }

            $ticket = Ticket::findOrFail($ticket_id);
            
            $update = $ticket->updates()->create([
                'status' => $request->status,
                'text' => $request->text
            ]);

            // تحديث حالة التذكرة الرئيسية
            $ticket->update([
                'status' => $request->status
            ]);

            return response()->json([
                'message' => 'تم إضافة التحديث بنجاح',
                'data' => $update
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء إضافة التحديث',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($ticket_id, $update_id)
    {
        try {
            $update = TicketUpdate::where('ticket_id', $ticket_id)
                ->where('id', $update_id)
                ->firstOrFail();

            return response()->json([
                'message' => 'تم جلب التحديث بنجاح',
                'data' => $update
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء جلب التحديث',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 