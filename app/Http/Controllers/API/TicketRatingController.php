<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TicketRatingController extends Controller
{
    public function store(Request $request, $ticket_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'rating' => 'required|integer|between:1,5',
                'resolution' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'بيانات غير صالحة',
                    'errors' => $validator->errors()
                ], 422);
            }

            $ticket = Ticket::findOrFail($ticket_id);

            // التحقق من أن التذكرة مغلقة قبل السماح بالتقييم
            if ($ticket->status !== 'closed') {
                return response()->json([
                    'message' => 'لا يمكن تقييم التذكرة إلا بعد إغلاقها'
                ], 400);
            }

            // التحقق من عدم وجود تقييم سابق
            if ($ticket->rating()->exists()) {
                return response()->json([
                    'message' => 'تم تقييم هذه التذكرة مسبقاً'
                ], 400);
            }

            $rating = $ticket->rating()->create([
                'rating' => $request->rating,
                'resolution' => $request->resolution
            ]);

            return response()->json([
                'message' => 'تم إضافة التقييم بنجاح',
                'data' => $rating
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء إضافة التقييم',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($ticket_id)
    {
        try {
            $rating = TicketRating::where('ticket_id', $ticket_id)
                ->with('ticket')
                ->first();

            if (!$rating) {
                return response()->json([
                    'message' => 'لم يتم العثور على تقييم لهذه التذكرة',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'message' => 'تم إلب التقييم بنجاح',
                'data' => $rating
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء جلب التقييم',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $ticket_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'rating' => 'required|integer|between:1,5',
                'resolution' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'بيانات غير صالحة',
                    'errors' => $validator->errors()
                ], 422);
            }

            $rating = TicketRating::where('ticket_id', $ticket_id)->firstOrFail();
            
            $rating->update($request->all());

            return response()->json([
                'message' => 'تم تحديث التقييم بنجاح',
                'data' => $rating->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء تحديث التقييم',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 