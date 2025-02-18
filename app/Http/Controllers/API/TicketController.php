<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    public function index()
    {
        try {
            $tickets = Ticket::with(['user', 'type'])->latest()->get();
            
            return response()->json([
                'message' => 'تم جلب التذاكر بنجاح',
                'data' => $tickets
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء جلب التذاكر',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'type_id' => 'required|exists:ticket_types,id',
                'description' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'بيانات غير صالحة',
                    'errors' => $validator->errors()
                ], 422);
            }

            $ticket = Ticket::create($request->all());

            // Create initial ticket update
            $ticket->updates()->create([
                'status' => 'new',
                'text' => 'تم استلام طلبك وسيتم معالجته قريباً'
            ]);

            return response()->json([
                'message' => 'تم إنشاء التذكرة بنجاح',
                'data' => $ticket->load(['user', 'type', 'updates'])
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء إنشاء التذكرة',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'sometimes|required|in:new,in_progress,waiting,closed',
                'progress' => 'sometimes|required|integer|min:0|max:100',
                'status_notes' => 'sometimes|required|string',
                'update_text' => 'required|string' // New field for update text
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            $ticket = Ticket::findOrFail($id);
            
            // Update ticket status
            $updateData = array_filter($request->only([
                'status',
                'progress',
                'status_notes'
            ]));
            
            $ticket->update($updateData);

            // Create ticket update record
            $ticket->updates()->create([
                'status' => $request->status ?? $ticket->status,
                'text' => $request->update_text
            ]);

            // Reload the ticket with relationships
            $ticket = $ticket->fresh(['user', 'type', 'updates']);

            return response()->json([
                'message' => 'تم تحديث التذكرة بنجاح',
                'data' => $ticket
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء تحديث التذكرة',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $ticket = Ticket::findOrFail($id);
            $ticket->delete();
            
            return response()->json([
                'message' => 'تم حذف التذكرة بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء حذف التذكرة',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getUserTickets($user_id)
    {
        try {
            $tickets = Ticket::with(['type'])
                ->where('user_id', $user_id)
                ->orderBy('created_at', 'desc')
                ->get();

            if ($tickets->isEmpty()) {
                return response()->json([
                    'message' => 'لا توجد تذاكر لهذا المستخدم',
                    'data' => []
                ], 200);
            }

            return response()->json([
                'message' => 'تم جلب التذاكر بنجاح',
                'data' => $tickets
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء جلب التذاكر',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $ticket = Ticket::with(['user', 'type'])->findOrFail($id);

            return response()->json([
                'message' => 'تم جلب التذكرة بنجاح',
                'data' => $ticket
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء جلب التذكرة',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getUpdates($ticket_id)
    {
        try {
            $ticket = Ticket::findOrFail($ticket_id);
            $updates = $ticket->updates()
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'message' => 'تم إلب التحديثات بنجاح',
                'data' => $updates
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء جلب التحديثات',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 